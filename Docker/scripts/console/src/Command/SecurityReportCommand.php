<?php

namespace Console\Command;

use BadMethodCallException;
use Drupal\Component\Uuid\Php;
use Exception;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SecurityReportCommand extends Command
{
    protected static $defaultName = 'security:report';

    private const PHP_SUPPORTED_VERSIONS_URL = 'https://www.php.net/releases/index.php?json&version=';

    private const PHP_EOL_URL = 'https://www.php.net/supported-versions.php';

    private const DRUPAL_RELEASE_URL = 'https://www.drupal.org/about/core/policies/core-release-cycles/schedule';

    private const DATE_FORMAT = 'Y-m-d';

    private $httpClient;

    private int $cacheFileMaxAge = 60 * 60;

    public function __construct(HttpClientInterface $httpClient)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
    }

    protected function configure()
    {
        $this->setDescription('Creates a single-page HTML security report.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report['php'] = $this->getPhpReport();
        $report['composer'] = $this->getComposerReport();
        $report['drupal'] = $this->getDrupalReport();

        $html = '<html><head><style>body{font-family:verdana,georgia,arial;color:#000;}a{font-weight:bold;color:#000;}section,div{border: 1px #ccc solid;margin:0 0 10px 0;padding:10px;border-radius:5px;}h5,h6{margin-top:0;}.warning{color:#EB9D02;background-color:#E6E6A9;}.moderately-critical{color:#000;background-color:#965656;}.critical{color:#502323;background-color:#E97272;}</style></head><body>';
        $html .= '<h1>AmeriCorps Website Report</h1>' . PHP_EOL;
        $html .= '<h2>' . date('c') . '</h2>' . PHP_EOL;
        foreach ($report as $section => $sectionReport) {
          $html .= '<section>' . PHP_EOL;
          $html .= '<h2>' . strtoupper($section) . '</h2>' . PHP_EOL;
          if (isset($sectionReport['version'])) {
            $html .= '<h3>Version ' . $sectionReport['version'] . '</h3>' . PHP_EOL;
            if (!empty($sectionReport['support'])) {
              $html .= '<h4>' . $sectionReport['support'] . '</h4>' . PHP_EOL;
            }
            if (isset($sectionReport['categories'])) {
              foreach ($sectionReport['categories'] as $category => $categoryInfo) {
                $html .= '<div>' . PHP_EOL;
                $html .= '<h4 id="' . str_replace(['_', '-', '/'], '-', $category) . '">' . ucwords(str_replace(['_', '-', '/'], [' ', ' ', ': '], $category)) . '</h4>' . PHP_EOL;
                foreach ($categoryInfo as $categoryInfoName => $items) {
                  $html .= '<div>';
                  $html .= '<h5 id="' . str_replace(['_', '-', '/'], '-', $categoryInfoName) . '">' . ucwords(str_replace(['_', '-', '/'], [' ', ' ', ': '], $categoryInfoName)) . '</h5>';
                  foreach ($items as $item) {
                    if (!isset($item['severity'])) {
                      $item['severity'] = 'notice';
                    }
                    $html .= '<div class="' . $item['severity'] . '">' . PHP_EOL;
                    $html .= '<h5>' . $item['title'] . '</h5>' . PHP_EOL;
                    if (!empty($item['description'])) {
                      $html .= '<p>' . $item['description'] . '</p>' . PHP_EOL;
                    }
                    if (!empty($item['version'])) {
                      $html .= '<p><i>Version ' . $item['version'] . '</i></p>' . PHP_EOL;
                    }
                    if (!empty($item['solutions'])) {
                      if (count($item['solutions']) == 1) {
                        $html .= '<strong>Solution</strong>';
                        $html .= '<p>' . current($item['solutions']) . '</p>' . PHP_EOL;
                      }
                      else {
                        $html .= '<strong>Solutions</strong>';
                        $html .= '<ul>';
                        foreach ($item['solutions'] as $solution) {
                          $html .= '<li>' . $solution . '</li>' . PHP_EOL;
                        }
                        $html .= '</ul>';
                      }
                    }
                    if (!empty($item['type'])) {
                      $html .= '<strong>Type</strong>';
                      $html .= '<p>' . $item['type'] . '</p>' . PHP_EOL;
                    }
                    if (!empty($item['cve'])) {
                      $html .= '<strong>CVE</strong>';
                      $html .= '<p>' . implode(', ', $item['cve']) . '</p>' . PHP_EOL;
                    }
                    if (!empty($item['authors'])) {
                      $html .= '<strong>Authors</strong>' . PHP_EOL;
                      $html .= '<ul>' . PHP_EOL;
                      foreach ($item['authors'] as $author => $authorLinks) {
                        $authorHtml = $author;
                        if (count($authorLinks)) {
                          $sep = ' ';
                          foreach($authorLinks as $authorLinkTitle => $authorLink) {
                            $authorHtml .= $sep . '<a href="' . $authorLink . '" target="_blank">' . $authorLinkTitle . '</a>';
                            $sep = '|';
                          }
                        }
                        $html .= '<li>' . $authorHtml . '</li>' . PHP_EOL;
                      }
                      $html .= '</ul>' . PHP_EOL;
                    }
                    if (!empty($item['dependencies'])) {
                      $html .= '<strong>Dependencies</strong>' . PHP_EOL;
                      $html .= '<ul>' . PHP_EOL;
                      foreach ($item['dependencies'] as $dependency => $dependencyConstraint) {
                        $html .= '<li><a href="#' . str_replace(['_', '-', '/'], '-', $dependency) . '">' . $dependency . ':' . $dependencyConstraint . '</a></li>';
                      }
                      $html .= '</ul>' . PHP_EOL;
                    }
                    if (!empty($item['url'])) {
                      $html .= '<p><a href="' . $item['url'] . '" target="_blank">more</a>...</p>' . PHP_EOL;
                    }
                    $html .= '</div>' . PHP_EOL;
                  }
                  $html .= '</div>' . PHP_EOL;
                }
                $html .= '</div>' . PHP_EOL;
              }
            }
          }
          $html .= '</section>' . PHP_EOL;
        }
        $html .= '</body></html>';

        $reportsPath = 'local/reports';
        $file = $reportsPath . '/' . date('Y-m-d-H-i-s'). '-security-report.html';
        file_put_contents($file, $html);
        $output->writeln($file);
        return Command::SUCCESS;
    }

    private function getMinStability(): string
    {
      return $this->getComposerLockData()['minimum-stability'];
    }

    private function isMinStabilityTooLow(): bool
    {
      switch ($this->getMinStability()) {
        case 'alpha':
        case 'beta':
        case 'dev':
          return TRUE;

        return FALSE;
      }
    }

    private function getDrupalSecurityReviewReport(): array
    {
      $report = [];
      $process = new Process(['drush', 'security:review', '--format=string']);
      $process->run();
      $lines = explode(PHP_EOL, $process->getOutput());
      foreach ($lines as $line) {
        $parts = explode("\t", $line);
        if (empty($parts[1])) {
          continue;
        }
        if ($parts[1] != 'success') {
          $report['categories']['security_review']['default'][] = [
            'title' => $parts[0],
            'severity' => str_contains($parts[0], 'danger') ? 'critical' : 'warning',
          ];
        }
      }
      return $report;
    }

    public function getDrupalSecuritySupportInfo(string $version): array
    {
        $version = $this->getMajorMinorVersion($version);
        $html = $this->httpClient->request('GET', self::DRUPAL_RELEASE_URL)->getContent();
        if (!$html) {
            return null;
        }
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $info = [];
        $tables = $xpath->query("//table");
        foreach ($tables as $table) {
            $rows = $xpath->query(".//tr", $table);
            foreach ($rows as $i => $row) {
                $tdElements = $xpath->query(".//td", $row);
                if ($tdElements->length >= 2) {
                    $dateText = str_replace('week of', '', trim(strtolower($tdElements->item(0)->textContent)));
                    $eventText = trim($tdElements->item(1)->textContent);
                    if (strpos($eventText, 'End of security support') !== FALSE) {
                        if (preg_match('/End of security support for (\d+\.\d+)/', $eventText, $matches)) {
                            if ($version == $matches[1]) {
                              $info['supported_until'] = $dateText;
                              $info['supported'] = time() < strtotime($info['supported_until']);
                            }
                        }
                    }
                }
            }
        }
        return $info;
    }

    private function getComposerLockData(): array
    {
      $composerLockPath = __DIR__ . '/../../../../composer.lock';
      if (!file_exists($composerLockPath)) {
          throw new \Exception("composer.lock file not found at $composerLockPath");
      }
      return json_decode(file_get_contents($composerLockPath), TRUE);
    }

    private function getPhpReport(): array
    {
      $process = new Process(['php', '--version']);
      $process->run();
      if (!$process->isSuccessful()) {
          throw new ProcessFailedException($process);
      }
      preg_match('/\d+\.\d+\.\d+/', $process->getOutput(), $matches);
      $info = $this->getPhpInfo($matches[0]);
      $report = [
        'version' => $matches[0],
      ];

      if (empty($info['supported'])) {
        $report['support'] = 'Unsupported release!';
        $report['categories']['support']['expired'][] = [
          'title' => 'This version of PHP is no longer supported!',
          'solutions' => ['Upgrade to a <a href="https://www.php.net/supported-versions.php" target="_blank">supported version</a> of PHP.'],
          'severity' => 'critical',
        ];
      }
      else {
        $report['support'] = 'Supported until ' . date(self::DATE_FORMAT, strtotime($info['supported_until']));
        if ($this->isComingSoon($info['supported_until'])) {
          $report['categories']['support']['expiring'][] = [
            'title' => 'This version of PHP will expire soon!',
            'solutions' => ['Upgrade to a supported version of PHP soon.'],
            'severity' => 'warning',
          ];
        }
      }

      return $report;
    }

    public function isComingSoon(string $date): bool
    {
        $targetDate = strtotime($date);
        if ($targetDate === false) {
            throw new \InvalidArgumentException("Invalid date format: $date");
        }
        $currentDate = time();
        $sixMonthsLater = strtotime('+6 months', $currentDate);
        return $targetDate <= $sixMonthsLater && $targetDate >= $currentDate;
    }

    public function getPhpInfo(string $version): array
    {
        $url = self::PHP_SUPPORTED_VERSIONS_URL . $version;
        $data = $this->httpClient->request('GET', $url)->toArray();
        $data = array_merge($data, $this->getPhpVersionSupportInfo($version));
        return $data;
    }

    private function getPhpEolData(): array
    {
        $response = $this->httpClient->request('GET', self::PHP_EOL_URL)->getContent();

        $eolData = [];
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $branchHeaders = $xpath->query('//th[contains(text(), "Branch")]');
        $table = null;

        if ($branchHeaders->length > 0) {
            $header = $branchHeaders->item(0);
            $table = $header->parentNode;
            while ($table && $table->nodeName !== 'table') {
                $table = $table->parentNode;
            }
        }

        if ($table === null) {
            throw new Exception('Table with the "Branch" header not found.');
        }

        $rows = $xpath->query('.//tbody/tr', $table);
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            if ($cells->length >= 3) {
                $version = trim($cells->item(0)->textContent);
                $activeSupport = trim($cells->item(3)->textContent);
                $eol = trim($cells->item(4)->textContent);
                $eolData[$version] = [
                    'active_support_until' => $activeSupport,
                    'eol' => $eol,
                ];
            }
        }

        return $eolData;
    }

    private function getMajorVersion(string $version): string
    {
      return implode('.', array_slice(explode('.', $version), 0, 1));
    }

    private function getMajorMinorVersion(string $version): string
    {
      return implode('.', array_slice(explode('.', $version), 0, 2));
    }

    public function getPhpVersionSupportInfo(string $version): array
    {
        $eolData = $this->getPhpEolData();

        $majorMinorVersion = $this->getMajorMinorVersion($version);
        if (!isset($eolData[$majorMinorVersion])) {
            return 'No information available for PHP version ' . $version;
        }

        $info['eol'] = $eolData[$majorMinorVersion]['eol'];
        $info['supported_until'] = $eolData[$majorMinorVersion]['active_support_until'];
        $info['supported'] = strtotime($info['supported_until']) > time();

        return $info;
    }

    private function getComposerReport(): array
    {
        $process = new Process(['composer', '--version']);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        preg_match('/\d+\.\d+\.\d+/', $process->getOutput(), $matches);
        $report = [
          'version' => $matches[0],
        ];
        $process = new Process(['composer', 'audit', '--format=json']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if ($this->isMinStabilityTooLow()) {
          $report['categories']['project']['stability'][] = [
            'title' => 'Project Stability Too Low',
            'description' => 'The project stability is set to "' . $this->getMinStability() . '". It should not be set that low.',
            'solutions' => ['Set the project minimum stability to `stable` & run `composer install`. Resolve any related issues.'],
            'severity' => 'critical',
          ];
        }

        $auditOutput = json_decode($process->getOutput());
        foreach ($auditOutput as $type => $items) {
          foreach ($items as $item) {
            $report['categories']['audit'][] = [
              'title' => $item,
              'severity' => 'warning',
            ];
          }
        }

        foreach ($this->getComposerLockData()['packages'] as $info) {
          $authorLinks = [];
          if (isset($info['authors'])) {
            foreach ($info['authors'] as $authorInfo) {
              if (isset($authorInfo['homepage'])) {
                $authorLinks[$authorInfo['name']]['site'] = $authorInfo['homepage'];
              }
              if (isset($authorInfo['email'])) {
                $authorLinks[$authorInfo['name']]['email'] = 'mailto:' . $authorInfo['email'];
              }
            }
          }
          $report['categories']['installed_packages'][$info['name']][] = [
            'title' => $info['name'],
            'description' => isset($info['description']) ? $info['description'] : '',
            'version' => $info['version'],
            'url' => isset($info['homepage']) ? $info['homepage'] : 'https://packagist.org/packages/' . $info['name'],
            'authors' => $authorLinks,
            'dependencies' => isset($info['require']) ? $info['require'] : [],
          ];
        }

        return $report;
    }

    private function getDrupalReport(): array
    {
        $report = [];
        $composerLockData = $this->getComposerLockData();
        $drupalVersion = null;
        foreach ($composerLockData['packages'] as $package) {
            if ($package['name'] === 'drupal/core') {
                $drupalVersion = $package['version'];
                break;
            }
        }

        if ($drupalVersion === null) {
            throw new \Exception('Drupal version not found in composer.lock');
        }
        $drupalVersion = ltrim($drupalVersion, 'v');
        $support = $this->getDrupalSecuritySupportInfo($drupalVersion);
        $report['version'] = $drupalVersion;
        if (empty($support['supported'])) {
          $report['support'] = 'Unsupported release!';
          $report['categories']['support']['expired'][] = [
            'title' => 'This version of Drupal is no longer supported!',
            'solutions' => [
              'Upgrade to a <a href="https://www.drupal.org/project/drupal/releases?version=' . $this->getMajorVersion($drupalVersion) . '" target="_blank">supported version</a> of Drupal.'
            ],
            'severity' => 'critical',
          ];
        }
        else {
          $report['support'] = 'Supported until ' . date(self::DATE_FORMAT, strtotime($support['supported_until']));
          if ($this->isComingSoon($support['supported_until'])) {
            $report['categories']['support']['expiring'][] = [
              'title' => 'This version of Drupal will expire soon!',
              'solutions' => ['Upgrade to a supported version of Drupal soon.'],
              'severity' => 'warning',
            ];
          }
        }
        foreach ($this->getDrupalCoreSecurityAdvisories($drupalVersion) as $advisory) {
          $report['categories']['core']['general'][] = [
            'title' => $advisory['title'],
            'description' => $advisory['field_sa_description']['value'],
            'solutions' => [$advisory['field_sa_solution']['value']],
            'type' => $advisory['field_sa_type'],
            'cve' => $advisory['field_sa_cve'],
            'url' => $advisory['url'],
            'severity' => $this->getSeverity($advisory['title']),
          ];
        }
        foreach ($composerLockData['packages'] as $package) {
          if (!str_starts_with($package['name'], 'drupal/')) {
            continue;
          }
          $module = substr($package['name'], 7);
          $meta = $this->getModuleMetaData($module);
          foreach ($meta as $item) {
            switch ($item['type']) {
              case 'project_module':
                if ($item['field_project_type'] != 'full') {
                  $report['categories']['modules'][$module][] = [
                    'title' => 'Risky project type: ' . $item['field_project_type'],
                    'severity' => 'warning',
                  ];
                }
                if ($item['field_security_advisory_coverage'] != 'covered') {
                  $report['categories']['modules'][$module][] = [
                    'title' => 'Security Advisory Coverage: ' . $item['field_security_advisory_coverage'],
                    'solutions' => [
                      'Uninstall this module if not in use.',
                      'Find a suitable replacement module',
                      'Help support module maintainers by offering to provide security coverage.',
                      'Fork the module & manually review the code for security issues until the contrib module has Security Advisory Coverage.',
                    ],
                    'url' => 'https://drupal.org/project/' . $module,
                    'severity' => 'warning',
                  ];
                }
                break;

              case 'sa':
                if ($this->isIssueActive($item['field_issue_status'])) {
                  $report['categories']['modules'][$module]['critical'][] = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                  ];
                }
            }
          }
        }

        $report = array_merge_recursive($report, $this->getDrupalSecurityReviewReport());
        return $report;
    }

    private function getSeverity(string $string): string
    {
      $string = strtolower($string);
      if (str_contains($string, 'moderately critical')) {
        return 'moderately-critical';
      }
      if (str_contains($string, 'critical')) {
        return 'critical';
      }
      return 'warning';
    }

    private function getDrupalCoreSecurityAdvisories(string $version, bool $reset = FALSE): array
    {
      $list = [];
      $coreNid = 3060;
      $raw = $this->getRawSecurityAdvisories($reset);
      foreach ($raw as $item) {
        if ($item['field_project']['id'] == $coreNid &&
          $this->isWithin($version, $item['field_affected_versions'])) {
            $list[] = $item;
        }
      }
      return $list;
    }

    private function getRawSecurityAdvisories(bool $reset = FALSE): array
    {
      $advisoriesFilePath = 'local/cache';
      $advisoriesFile = $advisoriesFilePath . '/advisories.json';
      if (!$reset && !$this->isExpired($advisoriesFile)) {
        return json_decode(file_get_contents($advisoriesFile), JSON_PRETTY_PRINT);
      }
      $baseUrl = "https://www.drupal.org/api-d7/node.json?type=sa";
      $response = $this->httpClient->request('GET', $baseUrl)->toArray();
      $list = $response['list'];
      if ($response['first'] != $response['last']) {
        $queryString = parse_url($response['last'], PHP_URL_QUERY);
        parse_str($queryString, $query);
        for ($p = 1; $p <= $query['page']; $p++) {
          $pageUrl = $baseUrl . '&page=' . $p;
          $response = $this->httpClient->request('GET', $pageUrl)->toArray();
          foreach ($response['list'] as $item) {
            $list[] = $item;
          }
        }
      }
      foreach ($list as $key => $item) {
        if (empty($item['field_affected_versions'])) {
          unset($list[$key]);
        }
      }

      $list = array_values($list);
      $this->createDirectories($advisoriesFilePath);
      file_put_contents($advisoriesFile, json_encode($list, JSON_PRETTY_PRINT));
      return $list;
    }

    public function isWithin(string|array $versions, $constraint): bool
    {
      if (!is_array($versions)) {
        $versions = [$versions];
      }
      foreach ($versions as $version) {
        $ranges = explode('||', $constraint);
        foreach ($ranges as $range) {
            $range = trim($range);
            preg_match_all('/([<>]=?)([0-9\.]+)/', $range, $matches, PREG_SET_ORDER);
            $lower_bound = null;
            $upper_bound = null;
            foreach ($matches as $match) {
                $operator = $match[1];
                $versionMatch = $match[2];
                if ($operator === '>=' && version_compare($version, $versionMatch, '>=')) {
                    $lower_bound = true;
                }
                if ($operator === '<' && version_compare($version, $versionMatch, '<')) {
                    $upper_bound = true;
                }
            }
            if ($lower_bound && $upper_bound) {
                return true;
            }
          }
        }
        return false;
    }

    private function isIssueActive(int $status): bool
    {
      // @see https://www.drupal.org/drupalorg/docs/apis/rest-and-other-apis#s-filtering-on-issue-data
      return in_array($status, [1, 4, 8, 13, 14, 15, 16]);
    }

    private function getModuleMetaData(string $module, bool $reset = FALSE): array
    {
      $moduleMetaPath = 'local/cache/modules';
      $this->createDirectories($moduleMetaPath);

      $file = $moduleMetaPath . '/' . $module . '.json';
      if (!$reset && file_exists($file)) {
        return json_decode(file_get_contents($file), JSON_PRETTY_PRINT);
      }

      $url = "https://www.drupal.org/api-d7/node.json?field_project_machine_name=$module";
      $response = $this->httpClient->request('GET', $url)->toArray();

      $list = [];
      if ($response['first'] != $response['last']) {
        // @todo fetch any additional pages of metadata
        throw new BadMethodCallException();
      }

      foreach ($response['list'] as $item) {
        $list[] = $item;
      }
      file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT));
      return $list;
    }

    private function createDirectories($path) {
        if (empty($path)) {
            throw new \InvalidArgumentException("The path cannot be empty.");
        }
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new \RuntimeException("Failed to create directories: $path");
            }
        }
    }

    private function isExpired(string $file): bool
    {
        if (!file_exists($file)) {
            return true;
        }
        $lastModifiedTime = filemtime($file);
        if ($lastModifiedTime === false) {
            return true;
        }
        $currentTime = time();
        $fileAge = $currentTime - $lastModifiedTime;
        return $fileAge > $this->cacheFileMaxAge;
    }

}
