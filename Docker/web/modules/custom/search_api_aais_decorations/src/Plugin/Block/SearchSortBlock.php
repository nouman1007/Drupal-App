<?php

namespace Drupal\search_api_aais_decorations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\search_api_aais_decorations\Form\SearchSortForm;

/**
 * Provides a 'Sort by' block.
 *
 * @Block(
 *   id = "search_sort_block",
 *   admin_label = @Translation("Search Sort Block"),
 *   category = @Translation("Custom")
 * )
 */
class SearchSortBlock extends BlockBase {
  public function build() {
    return \Drupal::formBuilder()->getForm(SearchSortForm::class);
  }
}
