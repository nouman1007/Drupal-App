<?php

namespace Drupal\search_api_aais_decorations;

use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api_aais\Azure\Query\QueryParamBuilder;
use Drupal\Component\Utility\Xss;
use Drupal\Component\Utility\Html;
use Symfony\Component\HttpFoundation\Request;
use Drupal\views\Views;
use Drupal\search_api\Entity\Index;
use Drupal\search_api_aais\Plugin\search_api\backend\SearchApiAzureAiSearchBackend;
use Drupal\search_api\Item\Field;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\search_api_aais\Azure\Query\QueryFilterBuilder;
use Drupal\search_api_aais\Azure\Query\QuerySortBuilder;
use Drupal\search_api\Utility\FieldsHelperInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryParamBuilderDecorator extends QueryParamBuilder {

  private Request $request;

  private EntityFieldManagerInterface $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(QueryFilterBuilder $query_filter_builder, QuerySortBuilder $query_sort_builder, FieldsHelperInterface $fields_helper, LanguageManagerInterface $language_manager, RequestStack $requestStack, EntityFieldManagerInterface $entityFieldManager)
  {
    parent::__construct($query_filter_builder, $query_sort_builder, $fields_helper, $language_manager);
    $this->request = $requestStack->getCurrentRequest();
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildQueryParams(QueryInterface $query): array {
    $params = parent::buildQueryParams($query);

    $params['queryType'] = 'simple';
    $params['answers'] = 'none';

    if (!isset($params['search'])) {
      $keywords = trim($this->get('keywords'));
      $keywords = Xss::filter($keywords);
      $keywords = Html::escape($keywords);
      $params['search'] = $keywords;
    }

    $view = Views::getView('search');
    if ($view) {
      $view->setDisplay('page_1');
      $view->initHandlers();
      $base_table = $view->storage->get('base_table');
      $index = Index::load(str_replace('search_api_index_', '', $base_table));
      if ($index) {
        $backend = $index->getServerInstance()->getBackend();
        if ($backend instanceof SearchApiAzureAiSearchBackend) {
          $filterClauses = [];
          foreach($index->getFields() as $field => $fieldDefinition) {
            /** @var Field $fieldDefinition */
            if (!empty($fieldDefinition->getConfiguration()['facetable'])) {
              $values = $this->all($field);
              if (!empty($values)) {
                $params['facets'][] = $field;
                $filterClause = $this->buildFilter($fieldDefinition, $values);
                if (!empty($filterClause)) {
                  $filterClauses[] = $filterClause;
                }
              }
            }
          }
          $params['filter'] = implode(' and ', $filterClauses);
        }
      }
    }
    return $params;
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

  private function buildFilter(Field $field, array $values): string
  {
    $filter = [];
    $id = $field->getFieldIdentifier();
    $valueOptions = $this->getFieldValueOptions($field);
    foreach ($values as $value) {
      if (!in_array($value, $valueOptions)) {
        continue;
      }
      switch ($field->getType()) {
        case 'aais.string_collection':
          $filter[] = "$id/any(t: t eq '$value')";
          $operator = 'and';
          break;

        default:
          $filter[] = "$id eq '$value'";
          $operator = 'or';
      }
    }
    return count($filter) ? '(' . implode(" $operator ", $filter) . ')' : '';
  }

  private function getFieldValueOptions(Field $field): array
  {
    $property = $field->getPropertyPath();
    $entityTypeId = str_replace('entity:', '', $field->getDatasourceId());
    $storageDefs = $this->entityFieldManager->getFieldStorageDefinitions($entityTypeId);
    if (!isset($storageDefs[$property])) {
      return [];
    }
    $settings = $storageDefs[$property]->getSettings();
    if (isset($settings['allowed_values'])) {
      return array_keys($settings['allowed_values']);;
    }
    if ($property == 'type') {
      return $this->getIndexNodeBundles($field->getIndex());
    }
    return [];
  }

  private function getIndexNodeBundles(Index $index): array
  {
    $dataSourceSettings = $index->get('datasource_settings');
    return $dataSourceSettings['entity:node']['bundles']['selected'];
  }

}
