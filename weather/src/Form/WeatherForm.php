<?php

/**
 * @file
 * Contains \Drupal\weather\Form\WeatherForm.
 */

namespace Drupal\weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Configure weather settings for this site.
 */
class WeatherForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'weather.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('weather.settings');
    $form['weather_header_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Weather Header Text'),
      '#default_value' => $config->get('weather_header_text'),
    ];
    $form['api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API URL'),
      '#default_value' => $config->get('api_url'),
    ];
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('api_key'),
    ];
    $form['location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location'),
      '#default_value' => $config->get('location'),
    ];
    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('city'),
      '#default_value' => $config->get('city'),
    ];
    $form['countrycode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('countrycode'),
      '#default_value' => $config->get('countrycode'),
    ];
    $form['zipcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('zipcode'),
      '#default_value' => $config->get('zipcode'),
    ];
    $form['weather_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Weather Image'),
      '#upload_location' => 'public://',
      '#default_value' => $config->get('weather_image'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      //save images permanently
      $weatherimage = $form_state->getValue('weather_image');
      if (!empty($weatherimage[0])) {
        $file = File::load($weatherimage[0]);
        $file->setPermanent();
        $file->save();
      }
      // Retrieve the configuration
       $this->configFactory->getEditable('weather.settings')
      ->set('weather_header_text', $form_state->getValue('weather_header_text'))
      ->set('api_url', $form_state->getValue('api_url'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('weather_image', $form_state->getValue('weather_image'))
      ->set('location', $form_state->getValue('location'))
      ->set('city', $form_state->getValue('city'))
      ->set('countrycode', $form_state->getValue('countrycode'))
      ->set('zipcode', $form_state->getValue('zipcode'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
