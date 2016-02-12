<?php

/**
 * @file
 * Contains Drupal\mespronos\Entity\RankingLeague.
 */

namespace Drupal\mespronos\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\mespronos\Controller\RankingController;
use Drupal\Core\Database\Database;

/**
 * Defines the RankingLeague entity.
 *
 * @ingroup mespronos
 *
 * @ContentEntityType(
 *   id = "ranking_league",
 *   label = @Translation("RankingLeague entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mespronos\Entity\Controller\RankingLeagueListController",
 *     "views_data" = "Drupal\mespronos\Entity\ViewsData\RankingLeagueViewsData",
 *     "access" = "Drupal\mespronos\ControlHandler\RankingLeagueAccessControlHandler",
 *   },
 *   base_table = "mespronos__ranking_league",
 *   admin_permission = "administer RankingLeague entity",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class RankingLeague extends Ranking {


  /**
   * @return integer
   */
  public function getLeagueiD() {
    return $this->get('league')->target_id;
  }

  /**
   * @return League
   */
  public function getLeague() {
    $day_storage = \Drupal::entityManager()->getStorage('league');
    $day = $day_storage->load($this->get('league')->target_id);
    return $day;
  }

  public static function createRanking(League $league) {
    self::removeRanking($league);
    $data = self::getData($league);
    RankingController::sortRankingDataAndDefinedPosition($data);
    foreach($data as $row) {
      $rankingLeague = self::create([
        'better' => $row->better,
        'league' => $league->id(),
        'games_betted' => $row->nb_bet,
        'points' => $row->points,
        'position' => $row->position,
      ]);
      $rankingLeague->save();
    }
    return count($data);
  }

  public static function getData(League $league) {
    $injected_database = Database::getConnection();
    $query = $injected_database->select('mespronos__ranking_day','rd');
    $query->addField('rd','better');
    $query->addExpression('sum(rd.points)','points');
    $query->addExpression('count(rd.id)','nb_bet');
    $query->join('mespronos__day','d','d.id = rd.day');
    $query->groupBy('rd.better');
    $query->orderBy('points','DESC');
    $query->orderBy('nb_bet','DESC');
    $query->condition('d.league',$league->id());
    $results = $query->execute()->fetchAllAssoc('better');

    return $results;
  }

  public static function removeRanking(League $league) {

    $storage = \Drupal::entityManager()->getStorage('ranking_league');
    $query = \Drupal::entityQuery('ranking_league');
    $query->condition('league',$league->id());
    $ids = $query->execute();

    $rankings = $storage->loadMultiple($ids);
    $nb_deleted = count($rankings);
    foreach ($rankings as $ranking) {
      $ranking->delete();
    }

    return $nb_deleted;
  }

  /**
   * @param \Drupal\mespronos\Entity\League $league
   * @return \Drupal\mespronos\Entity\RankingLeague[]
   */
  public static function getRankingForLeague(League $league) {
    $storage = \Drupal::entityManager()->getStorage('ranking_league');
    $query = \Drupal::entityQuery('ranking_league');
    $query->condition('league', $league->id());
    $query->sort('position','ASC');
    $ids = $query->execute();

    $rankings = $storage->loadMultiple($ids);
    return $rankings;
  }

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $better
   * @return \Drupal\mespronos\Entity\RankingLeague
   * @todo : abstract duplicated part in Ranking
   */
  public static function getRankingForBetter(\Drupal\Core\Session\AccountProxyInterface $better) {
    $storage = \Drupal::entityManager()->getStorage('ranking_league');
    $query = \Drupal::entityQuery('ranking_league');
    $query->condition('better', $better->id());
    $query->sort('position','ASC');
    $ids = $query->execute();
    $id = array_pop($ids);
    $rankings = $storage->load($id);
    return $rankings;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['league'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('League'))
      ->setDescription(t('League entity reference'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'league')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'entity_reference',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'settings' => array(),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    return $fields;
  }

}
