<?php

/**
 * @file
 * Contains Drupal\mespronos\GameAccessControlHandler.
 */

namespace Drupal\mespronos;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Game entity.
 *
 * @see \Drupal\mespronos\Entity\Game.
 */
class GameAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view Game entity');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit Game entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete Game entity');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add Game entity');
  }

}
