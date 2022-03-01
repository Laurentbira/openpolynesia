<?php

declare(strict_types=1);

namespace Drupal\openculturas_custom\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * field_gallery via field_event_description reference.
 *
 * @ExtraFieldDisplay(
 *   id = "event_gallery",
 *   label = @Translation("Gallery"),
 *   description = "field_gallery via field_event_description reference",
 *   visible = false,
 *   bundles = {
 *     "node.date",
 *   }
 * )
 */
class EventGallery extends ExtraFieldBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(ContentEntityInterface $entity) {
    $this->setFieldname('field_gallery');
    $this->setReferenceField('field_event_description');

    $build = parent::viewElements($entity);
    if ($build !== []) {
      $display_options = ['type' => 'entity_reference_entity_view', 'viewmode' => 'default', 'label' => 'hidden'];
      $renderArray = $this->eventEntity->get('field_gallery')->view($display_options);
      $build['#markup'] = $this->renderer->render($renderArray);
    }

    return $build;
  }

}