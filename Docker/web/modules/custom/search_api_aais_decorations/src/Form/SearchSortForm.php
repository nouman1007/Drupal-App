<?php

namespace Drupal\search_api_aais_decorations\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements the 'Search Sort' form.
 */
class SearchSortForm extends FormBase {

  public function getFormId() {
    return 'search_sort_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['sort_by'] = [
      '#type' => 'select',
      '#title' => $this->t('Sort by'),
      '#options' => [
        'relevance' => $this->t('Relevance'),
        'added' => $this->t('Added'),
        'updated' => $this->t('Updated'),
      ],
      '#default_value' => \Drupal::request()->query->get('sort_by', 'relevance'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $sort_by = $form_state->getValue('sort_by');

    // Get the current request to access existing query parameters.
    $current_request = \Drupal::request();
    $current_query_params = $current_request->query->all();

    // Add or override the 'sort_by' parameter.
    $new_query_params = array_merge($current_query_params, ['sort_by' => $sort_by]);

    // Redirect to the URL with the merged query parameters.
    $form_state->setRedirectUrl(Url::fromRoute('view.search.page_1', [], [
        'query' => $new_query_params,
    ]));
  }

}
