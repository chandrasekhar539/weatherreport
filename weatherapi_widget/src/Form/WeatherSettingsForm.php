<?php

namespace Drupal\weatherapi_widget\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
final class WeatherSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'weatherapi_widget_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['weatherapi_widget.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('weatherapi_widget.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('WeatherAPI key'),
      '#description' => $this->t('Paste your WeatherAPI.com key. (Do not commit to Git if config is exported.)'),
      '#default_value' => $config->get('api_key') ?: '',
      '#required' => TRUE,
    ];

    $form['base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base URL'),
      '#default_value' => $config->get('base_url') ?: 'https://api.weatherapi.com/v1',
      '#description' => $this->t('WeatherAPI base URL. Default is https://api.weatherapi.com/v1'),
    ];

    $form['default_query'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default location query'),
      '#default_value' => $config->get('default_query') ?: 'Paris',
      '#description' => $this->t('Examples: Paris, 48.8567,2.3508, 10001. WeatherAPI uses q parameter for lookup.'),
      '#required' => TRUE,
    ];

    $form['cache_ttl'] = [
      '#type' => 'number',
      '#title' => $this->t('Cache TTL (seconds)'),
      '#default_value' => $config->get('cache_ttl') ?: 600,
      '#min' => 0,
      '#description' => $this->t('Cache response for N seconds. Use 0 to disable caching (not recommended).'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $this->configFactory->getEditable('weatherapi_widget.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('base_url', $form_state->getValue('base_url'))
      ->set('default_query', $form_state->getValue('default_query'))
      ->set('cache_ttl', (int) $form_state->getValue('cache_ttl'))
      ->save();
  }

}
