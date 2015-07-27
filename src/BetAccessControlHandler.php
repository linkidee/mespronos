<?php

/**
 * @file
 * Contains Drupal\mespronos\BetAccessControlHandler.
 */

namespace Drupal\mespronos;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Bet entity.
 *
 * @see \Drupal\mespronos\Entity\Bet.
 */
class BetAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view Bet entity');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit Bet entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete Bet entity');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add Bet entity');
  }

}
