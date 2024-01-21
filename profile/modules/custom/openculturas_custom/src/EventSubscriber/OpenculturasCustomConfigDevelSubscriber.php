<?php

declare(strict_types = 1);

namespace Drupal\openculturas_custom\EventSubscriber;

use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\InstallStorage;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\config_devel\Event\ConfigDevelEvents;
use Drupal\config_devel\Event\ConfigDevelSaveEvent;
use Drupal\views\ViewEntityInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function array_unique;
use function basename;
use function class_exists;
use function dirname;
use function pathinfo;
use function reset;
use function str_ends_with;

/**
 * OpenCulturas Customizing event subscriber.
 */
class OpenculturasCustomConfigDevelSubscriber implements EventSubscriberInterface {

  /**
   * Creates OpenculturasCustomConfigDevelSubscriber objects.
   *
   * @param \Drupal\Core\Config\ConfigManagerInterface $configManager
   *   Config manager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module handler service.
   */
  public function __construct(protected ConfigManagerInterface $configManager, protected ModuleHandlerInterface $moduleHandler) {
  }

  /**
   * Adds enforced dependency and adds back the uuid for view config entities.
   *
   * @param \Drupal\config_devel\Event\ConfigDevelSaveEvent $configDevelSaveEvent
   *   The ConfigDevelSaveEvent event object.
   */
  public function onConfigDevelSave(ConfigDevelSaveEvent $configDevelSaveEvent): void {
    $data = $configDevelSaveEvent->getData();
    $file_names = $configDevelSaveEvent->getFileNames();
    $file_path = reset($file_names);
    $config_name = pathinfo((string) $file_path, PATHINFO_FILENAME);
    $entity_type_id = $this->configManager->getEntityTypeIdByName($config_name);
    $extension = basename(dirname((string) $file_path, 3));
    if ($entity_type_id === NULL) {
      return;
    }

    if ($entity_type_id === 'view') {
      /** @var \Drupal\views\ViewEntityInterface|null $configEntity */
      $configEntity = $this->configManager->loadConfigEntityByName($config_name);
      if ($configEntity instanceof ViewEntityInterface && $configEntity->uuid() !== NULL) {
        $data = ['uuid' => $configEntity->uuid()] + $data;
        $configDevelSaveEvent->setData($data);
      }
    }

    // // @phpstan-ignore-next-line
    if ($extension === \Drupal::installProfile()) {
      return;
    }

    if (!$this->moduleHandler->moduleExists($extension)) {
      return;
    }

    if (str_ends_with(dirname((string) $file_path), InstallStorage::CONFIG_INSTALL_DIRECTORY) === FALSE) {
      return;
    }

    $data['dependencies']['enforced']['module'][] = $extension;
    $data['dependencies']['enforced']['module'] = array_unique($data['dependencies']['enforced']['module']);

    $configDevelSaveEvent->setData($data);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    if (!class_exists(ConfigDevelEvents::class)) {
      return [];
    }

    return [
      ConfigDevelEvents::SAVE => ['onConfigDevelSave'],
    ];
  }

}
