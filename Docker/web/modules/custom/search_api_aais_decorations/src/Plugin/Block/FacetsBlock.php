<?php

namespace Drupal\search_api_aais_decorations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\Entity\Index;
use Drupal\search_api_aais\Plugin\search_api\backend\SearchApiAzureAiSearchBackend;
use Drupal\Component\Utility\Xss;
use Drupal\Component\Utility\Html;
use Symfony\Component\HttpFoundation\Request;
use Drupal\search_api\Item\Field;
use Drupal\field\Entity\FieldConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a 'Search Facets' block.
 *
 * @Block(
 *   id = "search_facets_block",
 *   admin_label = @Translation("Search Facets"),
 *   category = @Translation("Custom")
 * )
 */
class FacetsBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManagerInterface $entityFieldManager, EntityTypeManagerInterface $entityTypeManager, RequestStack $requestStack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeManager = $entityTypeManager;
    $this->request = $requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Temporarily iterate through Search View for facetFields to expose.
    // @todo use contrib Facets functionality once available instead of this Block.

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

    $softFilterLimit = 3;
    $result = [];
    $markup = '<form id="facets" method="GET" action="/search">';
    $markup .= '<h2>' . $this->t('Filter by') . ':</h2>';
    $markup .= '<input type="hidden" name="keywords" value="' . $this->get('keywords') . '">';
    foreach ($facetableFields as $facetableField => $info) {
      $markup .= '<div>';
      $markup .= '<h3>' . $this->t($info['label']) . '</h3>';
      $i = 0;
      foreach ($info['options'] as $option => $label) {
        $markup .= vsprintf('<div class="%s"><label><input type="checkbox" name="%s[]" value="%s"%s>&nbsp;%s</label></div>', [
          $i > $softFilterLimit ? 'hidden' : '',
          $facetableField,
          $option,
          $this->isChecked($facetableField, $option) ? ' checked' : '',
          $this->t($label),
        ]);
        $i++;
      }
      $remainder = $i - $softFilterLimit - 1;
      if ($remainder > 0) {
        $markup .= "<a href='#'>Show ($remainder) more</a>";
      }
      $markup .= '</div>';
    }
    $markup .= '</form>';

    $result['form'] = [
      '#type' => 'markup',
      '#markup' => $markup,
      '#allowed_tags' => ['div', 'h2', 'h3', 'form', 'input', 'a'],
      '#attached' => [
        'library' => [
          'search_api_aais_decorations/facets_block',
        ],
      ],
    ];
    return $result;
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

  private function isChecked(string $param, string $value): bool
  {
    return in_array($value, $this->all($param));
  }

  private function get(string $param): string
  {
    $raw = $this->request->query->get($param);
    if (empty($raw)) {
      return '';
    }
    return Html::escape(Xss::filter(trim($raw)));
  }

  private function all(string $param): array
  {
    $values = [];
    $rawValues = $this->request->query->all($param);
    foreach ($rawValues as $rawValue) {
      $values[] = Html::escape(Xss::filter(trim($rawValue)));
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
