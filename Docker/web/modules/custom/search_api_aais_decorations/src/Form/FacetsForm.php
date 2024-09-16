<?php

namespace Drupal\search_api_aais_decorations\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views\Views;
use Drupal\search_api\Entity\Index;
use Drupal\search_api_aais\Plugin\search_api\backend\SearchApiAzureAiSearchBackend;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Component\Utility\Html;
use Symfony\Component\HttpFoundation\Request;
use Drupal\search_api\Item\Field;
use Drupal\field\Entity\FieldConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Implements the 'Facets' form.
 */
class FacetsForm extends FormBase {

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  protected EntityTypeManagerInterface $entityTypeManager;

  private Request $request;

  /**
   * Manually define facet order & labels, since weights are not supported.
   */
  private array $facetFields = [
    'field_americorps_program_s_' => 'Program',
    'type' => 'Resource Type',
    'field_focus_population_s_communi' => 'Focus Population',
    'focus_areas' => 'Focus Areas',
    'field_age_s_studied' => 'Ages Studied',
  ];

  public function __construct(EntityFieldManagerInterface $entityFieldManager, EntityTypeManagerInterface $entityTypeManager, RequestStack $requestStack) {
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeManager = $entityTypeManager;
    $this->request = $requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
    );
  }

  public function getFormId() {
    return 'facets_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $params = $this->request->query->all();
    $form['#attached']['library'][] = 'search_api_aais_decorations/facets_block';

    $form['header'] = [
      '#type' => 'markup',
      '#markup' => '<h2>' . $this->t('Filter by') . '</h2>',
    ];
    $form['sort_by'] = [
      '#type' => 'hidden',
      '#default_value' => $params['sort_by'] ?? '',
    ];
    foreach ($this->getFacetableFields() as $facetableField => $info) {
      $form[$facetableField] = [
        '#type' => 'checkboxes',
        '#title' => $this->t($info['label']),
        '#options' => $info['options'],
        '#default_value' => $params[$facetableField] ?? [],
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Apply'),
        '#button_type' => 'primary',
        '#attributes' => [
        ],
      ],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $params = [];
    $keywords = $form_state->getValue('keywords');
    if (empty($keywords)) {
      $params['keywords'] = $this->get('keywords');
    } else {
      $params['keywords'] = $keywords;
    }

    $sortBy = $form_state->getValue('sort_by');
    if (!empty($sortBy)) {
      $params['sort_by'] = $sortBy;
    }

    foreach ($this->facetFields as $facetableField => $label) {
      $values = $form_state->getValue($facetableField);
      if ($values) {
        foreach ($values as $key => $value) {
          if (!empty($value)) {
            $params[$facetableField][] = $value;
          }
        }
      }
      if (isset($params[$facetableField])) {
        $params[$facetableField] = array_unique($params[$facetableField]);
      }
    }

    // Redirect to the URL with the merged query parameters.
    $form_state->setRedirectUrl(Url::fromRoute('view.search.page_1', [], [
      'query' => $params,
    ]));
  }

  private function getFacetableFields(): array
  {
    $facetableFields = [];
    $view = Views::getView('search');
    if ($view) {
      $view->setDisplay('page_1');
      $view->initHandlers();
      $base_table = $view->storage->get('base_table');
      $index = Index::load(str_replace('search_api_index_', '', $base_table));
      if ($index) {
        $backend = $index->getServerInstance()->getBackend();
        if ($backend instanceof SearchApiAzureAiSearchBackend) {
          foreach ($this->facetFields as $facetField => $facetFieldLabel) {
            $field = $index->getField($facetField);
            if (!$field) {
              continue;
            }
            $index_field_config = $field->getConfiguration();
            if (empty($index_field_config['facetable'])) {
              continue;
            }
            if (isset($facetableFields[$facetField])) {
              continue;
            }
            $options = $this->loadOptions($index, $field);
            if (count($options)) {
              $facetableFields[$facetField] = [
                'label' => $facetFieldLabel,
                'options' => $options,
              ];
            }
          }
        }
      }
    }
    return $facetableFields;
  }

  private function loadOptions(Index $index, Field $index_field): array
  {
    switch($index_field->getType()) {
      case 'string':
        // Assume the string refers to a node bundle.
        // @todo add support for other edge-cases
        $options = [];
        foreach ($this->getIndexNodeBundles($index) as $selectedBundle) {
          $options[$selectedBundle] = $this->entityTypeManager->getStorage('node_type')
          ->load($selectedBundle)
          ->label();
        }
        return $options;

      case "aais.string_collection":
        $bundles = $this->getIndexNodeBundles($index);
        $entityFieldManager = \Drupal::service('entity_field.manager');
        $propertyPath = $index_field->getPropertyPath();
        foreach ($bundles as $bundle) {
          $fieldDefinitions = $entityFieldManager->getFieldDefinitions('node', $bundle);
          if (!isset($fieldDefinitions[$propertyPath])) {
            continue;
          }
          /** @var FieldConfig $fieldDefinition */
          $fieldDefinition = $fieldDefinitions[$propertyPath];
          $options = $fieldDefinition->getSettings()['allowed_values'];
        }
        return $options;

    }

    return [];
  }

  private function getIndexNodeBundles(Index $index): array
  {
    $dataSourceSettings = $index->get('datasource_settings');
    return $dataSourceSettings['entity:node']['bundles']['selected'];
  }

  private function get(string $param): string
  {
    $raw = $this->request->query->get($param);
    if (empty($raw)) {
      return '';
    }
    return Html::escape(Xss::filter(trim($raw)));
  }

}
