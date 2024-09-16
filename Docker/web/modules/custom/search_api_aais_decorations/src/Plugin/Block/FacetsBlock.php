<?php

namespace Drupal\search_api_aais_decorations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\search_api_aais_decorations\Form\FacetsForm;

/**
 * Provides a 'Search Facets' block.
 *
 * @Block(
 *   id = "search_facets_block",
 *   admin_label = @Translation("Search Facets"),
 *   category = @Translation("Custom")
 * )
 */
class FacetsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm(FacetsForm::class);
  }

}
