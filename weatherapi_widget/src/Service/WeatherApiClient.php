<?php

namespace Drupal\weatherapi_widget\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * {@inheritdoc}
 */
final class WeatherApiClient {

  public function __construct(
    private readonly ClientInterface $httpClient,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly CacheBackendInterface $cache,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Fetch current weather for a query (e.g., "Paris", "48.8567,2.3508", "10001").
   */
  public function current(string $query) {
    $config = $this->configFactory->get('weatherapi_widget.settings');

    $api_key = (string) $config->get('api_key');
    $base_url = (string) ($config->get('base_url') ?: 'https://api.weatherapi.com/v1');
    $ttl = (int) ($config->get('cache_ttl') ?: 600);

    if ($api_key === '') {
      return [
        'error' => TRUE,
        'message' => 'WeatherAPI key is missing. Configure it at /admin/config/services/weatherapi-widget',
      ];
    }

    $cid = 'weatherapi_widget:' . sha1($query);

    if ($cache = $this->cache->get($cid)) {
      return $cache->data;
    }

    // WeatherAPI current endpoint: /current.json with key and q parameters. [1](https://www.weatherapi.com/docs/)
    $url = rtrim($base_url, '/') . '/current.json?' . http_build_query([
      'key' => $api_key,
      'q' => $query,
    ]);

    try {
      $response = $this->httpClient->get($url, [
        'timeout' => 5,
        'headers' => ['Accept' => 'application/json'],
      ]);
      $data = json_decode((string) $response->getBody(), TRUE) ?: [];

      // Cache successful responses to reduce API calls.
      $this->cache->set($cid, $data, time() + $ttl);

      return $data;
    }
    catch (\Throwable $e) {
      $this->logger->error('WeatherAPI request failed: @msg', ['@msg' => $e->getMessage()]);
      return [
        'error' => TRUE,
        'message' => 'WeatherAPI request failed. Check logs.',
      ];
    }
  }

}
