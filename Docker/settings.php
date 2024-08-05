<?php

$databases = [];
$settings['hash_salt'] = '7ztAEHLuq0o9fN0u7SALe6BVVUVxbKsXRZej4pbye6xQNTwJkrXiF955K3DggUAk6YWgtkPt3A';
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];
$settings['entity_update_batch_size'] = 50;
$settings['entity_update_backup'] = TRUE;
$settings['migrate_node_migrate_type_classic'] = FALSE;

$databases['default']['default'] = array (
  'database' => getenv('DB_DATABASE'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASSWORD'),
  'prefix' => '',
  'host' => getenv('DB_HOST'),
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

// SSL configuration for PDO
// $options = array(
//     PDO::MYSQL_ATTR_SSL_CA => getenv('SSL_CA_CERT_PATH')
// );

$options = array(
  PDO::MYSQL_ATTR_SSL_CA => '/var/www/html/bin/DigiCertGlobalRootCA.pem'
);

try {
    $pdo = new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';port=3306;dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USER'),
        getenv('DB_PASSWORD'),
        $options
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>