<?php

namespace Drupal\americorps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrateProcessInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;

/**
 * @MigrateProcessPlugin(
 *   id = "list_text"
 * )
 */
class ListTextProcessPlugin extends ProcessPluginBase implements MigrateProcessInterface {

  /**
   * Transforms the value by converting it to uppercase.
   *
   * @param mixed $value
   *   The value to be transformed.
   * @param MigrateExecutableInterface $migrate_executable
   *   The migration executable.
   * @param Row $row
   *   The row from the source to be processed.
   * @param string $destination_property
   *   The destination property currently being processed.
   *
   * @return mixed
   *   The transformed value.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return ['value' => $value];
  }

}
