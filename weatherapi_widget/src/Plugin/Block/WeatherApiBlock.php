<?php

namespace Drupal\weatherapi_widget\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\weatherapi_widget\Service\WeatherApiClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a WeatherAPI widget block.
 *
 * @Block(
 *   id = "weatherapi_widget_block",
 *   admin_label = @Translation("WeatherAPI Widget")
 * )
 */
final class WeatherApiBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly WeatherApiClient $client,
    private readonly ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   *
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('weatherapi_widget.client'),
      $container->get('config.factory'),
    );
  }

  /**
   *
   */
  public function build() {
    $config = $this->configFactory->get('weatherapi_widget.settings');
    $query = (string) ($config->get('default_query') ?: 'Paris');

    $data = $this->client->current($query);

    // WeatherAPI returns location + current objects with temp_c, humidity, condition text/icon etc. [2](https://www.weatherapi.com/)[1](https://www.weatherapi.com/docs/)
    $vars = [
      'error' => !empty($data['error']),
      'message' => $data['message'] ?? '',
      'location_name' => $data['location']['name'] ?? $query,
      'country' => $data['location']['country'] ?? '',
      'localtime' => $data['location']['localtime'] ?? '',
      'temp_c' => $data['current']['temp_c'] ?? NULL,
      'feelslike_c' => $data['current']['feelslike_c'] ?? NULL,
      'humidity' => $data['current']['humidity'] ?? NULL,
      'wind_kph' => $data['current']['wind_kph'] ?? NULL,
      'condition_text' => $data['current']['condition']['text'] ?? '',
      'condition_icon' => $data['current']['condition']['icon'] ?? '',
      'last_updated' => $data['current']['last_updated'] ?? '',
    ];

    return [
      '#theme' => 'weatherapi_widget',
      '#vars' => $vars,
      '#cache' => [
        'contexts' => ['url', 'url.query_args'],
        'tags' => ['config:weatherapi_widget.settings'],
    // Sync with your service cache.
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => ['weatherapi_widget/widget'],
      ],
    ];
  }

}
