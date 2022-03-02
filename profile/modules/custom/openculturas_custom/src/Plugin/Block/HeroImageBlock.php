<?php

declare(strict_types=1);

namespace Drupal\openculturas_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\openculturas_custom\CurrentEntityHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a hero image from current entity block.
 *
 * @Block(
 *   id = "openculturas_custom_hero_image",
 *   admin_label = @Translation("Hero image from current entity (from field_mood_image)"),
 *   category = @Translation("Openculturas")
 * )
 */
class HeroImageBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_entity = CurrentEntityHelper::getEventReference(CurrentEntityHelper::get_current_page_entity());
    if ($current_entity !== NULL
      && $current_entity->hasField('field_mood_image')
      && !$current_entity->get('field_mood_image')->isEmpty()) {
      $display_options = [
        'type' => 'entity_reference_entity_view',
        'label' => 'hidden',
        'settings' => ['view_mode' => 'header_image',]
      ];
      $build = $current_entity->get('field_mood_image')->view($display_options);
      $this->renderer->addCacheableDependency($build, $current_entity);
      return $build;
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), [
      'url.path',
    ]);
  }

}
