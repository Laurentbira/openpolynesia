<?php

declare(strict_types=1);

namespace Drupal\openculturas_custom;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use function is_array;

/**
 * Service Provider for Openculturas
 */
class OpenculturasCustomServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $filter_protocols = $container->getParameter('filter_protocols');
    if (is_array($filter_protocols)) {
      $filter_protocols[] = 'geo';
      $container->setParameter('filter_protocols', $filter_protocols);
    }

    $modules = $container->getParameter('container.modules');
    if (isset($modules['default_content'])) {
      $service_definition = new Definition('Drupal\openculturas_custom\Normalizer', array(
        new Reference('default_content_fixes.normalizer.inner'),
      ));
      $service_definition->setDecoratedService('default_content.content_entity_normalizer', null, 9);
      $container->setDefinition('default_content_fixes.normalizer', $service_definition);
    }
  }

}
