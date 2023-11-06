<?php

/**
 * @file
 * Install, update and uninstall module functions.
 */

declare(strict_types = 1);

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;

/**
 * Implements hook_removed_post_updates().
 */
function openculturas_removed_post_updates(): array {
  return [
    'openculturas_post_update_0001' => '1.4.0',
    'openculturas_post_update_0002' => '1.4.0',
    'openculturas_post_update_0003' => '1.4.0',
    'openculturas_post_update_0004' => '1.4.0',
    'openculturas_post_update_0005' => '1.4.0',
    'openculturas_post_update_0006' => '1.4.0',
    'openculturas_post_update_0007' => '1.4.0',
    'openculturas_post_update_0008' => '1.4.0',
    'openculturas_post_update_0009' => '1.4.0',
    'openculturas_post_update_0010' => '1.4.0',
    'openculturas_post_update_0011' => '1.4.0',
    'openculturas_post_update_0012' => '1.4.0',
    'openculturas_post_update_0013' => '1.4.0',
    'openculturas_post_update_0014' => '1.4.0',
    'openculturas_post_update_0015' => '1.4.0',
    'openculturas_post_update_0016' => '1.4.0',
    'openculturas_post_update_0017' => '1.4.0',
    'openculturas_post_update_0018' => '1.4.0',
    'openculturas_post_update_0019' => '1.4.0',
    'openculturas_post_update_0020' => '1.4.0',
    'openculturas_post_update_0021' => '1.4.0',
    'openculturas_post_update_0022' => '1.4.0',
    'openculturas_post_update_0023' => '1.4.0',
    'openculturas_post_update_0024' => '1.4.0',
    'openculturas_post_update_0025' => '1.4.0',
    'openculturas_post_update_0026' => '1.4.0',
    'openculturas_post_update_0027' => '1.4.0',
    'openculturas_post_update_0028' => '1.4.0',
    'openculturas_post_update_0029' => '1.4.0',
    'openculturas_post_update_0030' => '1.4.0',
    'openculturas_post_update_0031' => '1.4.0',
    'openculturas_post_update_0032' => '1.4.0',
    'openculturas_post_update_0033' => '1.4.0',
    'openculturas_post_update_0034' => '1.4.0',
    'openculturas_post_update_0035' => '1.4.0',
    'openculturas_post_update_0036' => '1.4.0',
    'openculturas_post_update_0037' => '1.4.0',
    'openculturas_post_update_0038' => '1.4.0',
    'openculturas_post_update_0039' => '1.4.0',
    'openculturas_post_update_0040' => '1.4.0',
    'openculturas_post_update_0041' => '1.4.0',
    'openculturas_post_update_0042' => '1.4.0',
    'openculturas_post_update_0043' => '1.4.0',
    'openculturas_post_update_0044' => '1.4.0',
    'openculturas_post_update_oc_gin_theme_overrides_paragraphs_behaviors' => '1.4.0',
    'openculturas_post_update_views_refactor_0001' => '1.4.0',
    'openculturas_post_update_views_refactor_0002' => '1.4.0',
    'openculturas_post_update_views_refactor_0003' => '1.4.0',
    'openculturas_post_update_views_refactor_0004' => '1.4.0',
    'openculturas_post_update_views_refactor_0005' => '1.4.0',
    'openculturas_post_update_views_refactor_0006' => '1.4.0',
    'openculturas_post_update_views_refactor_0007' => '1.4.0',
    'openculturas_post_update_views_refactor_0008' => '1.4.0',
    'openculturas_post_update_views_refactor_0009' => '1.4.0',
    'openculturas_post_update_views_refactor_0010' => '1.4.0',
    'openculturas_post_update_views_refactor_0011' => '1.4.0',
  ];
}

/**
 * Configuration update.
 */
function openculturas_post_update_0045(): string {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('openculturas', 'openculturas_post_update_0045');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

function _openculturas_post_update_interaction_button_section_remove_bookmark(string $component, EntityViewDisplayInterface $display): void {
  $display->removeComponent($component);
  $group_interact = $display->getThirdPartySetting('field_group', 'group_interact');
  if ($group_interact) {
    foreach ($group_interact['children'] as $key => $child) {
      if ($child === $component) {
        unset($group_interact['children'][$key]);
        $group_interact['children'] = array_values($group_interact['children']);
      }
    }
    $display->setThirdPartySetting('field_group', 'group_interact', $group_interact);
  }
}

/**
 * Disable bookmark as field and enable it as block.
 */
function openculturas_post_update_interaction_button_section(): string {

  /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display */
  $entity_display = \Drupal::service('entity_display.repository');

  $bundle_ids = ['article', 'date', 'event', 'location', 'page', 'profile', 'faq'];
  foreach ($bundle_ids as $bundle_id) {
    $display = $entity_display->getViewDisplay('node', $bundle_id, 'full');
    if (!$display->isNew()) {
      _openculturas_post_update_interaction_button_section_remove_bookmark('flag_bookmark_node', $display);
      $display->save();
    }
  }

  $bundle_ids = ['article_type', 'category', 'column', 'event_type', 'location_type', 'page_type', 'faq_category'];
  foreach ($bundle_ids as $bundle_id) {
    $display = $entity_display->getViewDisplay('taxonomy_term', $bundle_id, 'full');
    if (!$display->isNew()) {
      _openculturas_post_update_interaction_button_section_remove_bookmark('flag_bookmark_term', $display);
      $display->save();
    }
  }

  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('openculturas', 'openculturas_post_update_interaction_button_section');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}
