<?php

/**
 * @file
 * Install, update and uninstall module functions.
 */

use Drupal\user\Entity\Role;
use Drupal\views\Views;
use Drupal\workflows\Entity\Workflow;

/**
 * Views: Replaces the Content: publish filter with Content: Published status or admin user
 */
function openculturas_post_update_0001() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $no_warnings = $updater->executeUpdate('openculturas', 'openculturas_post_update_0001');
  if ($no_warnings) {
    $view = Views::getView('entity_reference_node');
    if ($view) {
      if ($view->getHandler('default', 'filter', 'status')) {
        $view->removeHandler('default', 'filter', 'status');
      }
      if ($view->getHandler('er_organizer', 'filter', 'status')) {
        $view->removeHandler('er_organizer', 'filter', 'status');
      }
      if ($view->getHandler('er_organizer', 'filter', 'status_1')) {
        $view->removeHandler('er_organizer', 'filter', 'status_1');
      }
      $view->save();
    }
  }

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Views: Set granularity to hour for the not exposed start/end date filter.
 */
function openculturas_post_update_0002() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('openculturas', 'openculturas_post_update_0002');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Adds missing unpublish moderation state option in bulk edit
 */
function openculturas_post_update_0003() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('openculturas', 'openculturas_post_update_0003');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Installs and places the field field_main_profile into user account
 */
function openculturas_post_update_0004() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('openculturas', 'openculturas_post_update_0004');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Maps: add pager below map, add result counter, expose no/page option
 */
function openculturas_post_update_0005() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('openculturas', 'openculturas_post_update_0005');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * deprecate wrong machine name for state (to_review)/transition (review) and add new one
 */
function openculturas_post_update_0006() {
  $workflows = ['draften', 'magazine_article'];
  foreach ($workflows as $id) {
    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    $workflow = Workflow::load($id);
    if ($workflow) {
      $workflow->getTypePlugin()->setStateWeight('draft', -2);
      $workflow->getTypePlugin()->addState('review', 'In review');
      $workflow->getTypePlugin()->setStateWeight('review', -1);
      $workflow->getTypePlugin()->setStateWeight('published', 0);
      $workflow->getTypePlugin()->setStateWeight('unpublished', 1);
      if ($workflow->getTypePlugin()->hasState('to_review')) {
        $workflow->getTypePlugin()->setStateLabel('to_review', 'In review (deprecated)');
        $workflow->getTypePlugin()->setStateWeight('to_review', 2);
      }

      $workflow->getTypePlugin()->addTransition('to_review', 'To review', ['draft', 'published', 'review', 'to_review', 'unpublished'], 'review');
      $workflow->getTypePlugin()->deleteTransition('review');
      $workflow->getTypePlugin()->setTransitionFromStates('create_new_draft', ['draft', 'published', 'review', 'to_review']);
      $workflow->getTypePlugin()->setTransitionFromStates('publish', ['draft', 'published', 'review', 'to_review', 'unpublished']);
      $workflow->getTypePlugin()->setTransitionFromStates('unpublish', ['draft', 'published', 'review', 'to_review']);
      $workflow->getTypePlugin()->setTransitionWeight('create_new_draft', -2);
      $workflow->getTypePlugin()->setTransitionWeight('to_review', -1);
      $workflow->getTypePlugin()->setTransitionWeight('publish', 0);
      $workflow->getTypePlugin()->setTransitionWeight('unpublish', 1);
      $workflow->save();
    }
  }

  $role = Role::load('authenticated');
  $role->grantPermission('use draften transition to_review');
  $role->revokePermission('use draften transition review');
  $role->save();

  $role = Role::load('oc_organizer');
  $role->revokePermission('use draften transition review');
  $role->save();

  $role = Role::load('magazine_editor');
  $role->grantPermission('use magazine_article transition to_review');
  $role->save();

  $role = Role::load('oc_admin');
  $role->grantPermission('use magazine_article transition to_review');
  $role->save();

  $view = Views::getView('moderated_content');
  if ($view) {
    if (($handler_configuration = $view->getHandler('default', 'filter', 'moderation_state'))) {
      foreach ($handler_configuration['group_info']['group_items'] as &$group_item) {
        $value = &$group_item['value'];
        if (isset($value['draften-to_review'])) {
          unset($value['draften-to_review']);
          $value['draften-review'] = 'draften-review';
        }
        if (isset($value['magazine_article-to_review'])) {
          unset($value['magazine_article-to_review']);
          $value['magazine_article-review'] = 'magazine_article-review';
        }
      }
      unset($group_item);
      $handler_configuration['group_info']['group_items'][] = [
        'operator' => 'in',
        'title' => 'In review (deprecated)',
        'value' => ['draften-to_review' => 'draften-to_review', 'magazine_article-to_review' => 'magazine_article-to_review'],
      ];
      $view->setHandler('default', 'filter', 'moderation_state', $handler_configuration);
      $view->save();
    }
  }
}