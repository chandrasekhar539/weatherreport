<?php
namespace Drupal\weather\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\UrlHelper;
use GuzzleHttp\Exception\RequestException;

class WeatherController extends ControllerBase {
    public function weatherReport($city = null,$zipcode = null) {
      $request = \Drupal::request();
      //retriving the configuration data
      $config = \Drupal::config('weather.settings');
      //setting the data based on the url
      $data['urlcity'] = $city;
      $data['urlzip'] = $city;
      $defaultlocation = $config->get('location');
      $paramcity = !empty($city)?$city:$config->get('city').",".$defaultlocation;
      $paramzip =  !empty($zipcode)?$paramcity.",".$zipcode:$city;
      $paramquery = !empty($city)?$paramzip:$paramcity;
      //retriving the image from configuration
      if(!empty($config->get('weather_image')[0])) {
        $file = \Drupal\file\Entity\File::load($config->get('weather_image')[0]);
        if(!empty($file)) {
          $path = $file->getFileUri();
          $weather_image = explode('public:/', $path)[1];
        }
      }
      $data['weather_image'] = "/sites/default/files/".$weather_image;
      $data['weather_header_text'] = $config->get('weather_header_text');
      $params = array('q' => $paramquery,'appid' => $config->get('api_key'));
      $query = UrlHelper::buildQuery($params);
      $url = $config->get('api_url').'?'.$query;
      //api call
      if(!empty($config->get('api_url'))) {
        try {
        $response = \Drupal::httpClient()->get($url);
        $response_array = json_decode($response->getBody()->getContents());
        }
        catch(RequestException $e) {
          $errorData = json_decode($e->getResponse()->getBody()->getContents());
          $data['code'] = $errorData->cod;
          $data['message'] = $errorData->message;
        }
      }
      $data['country'] = $response_array->sys->country;
      $data['city'] = $response_array->name;
      $details = $response_array->main;
      $data['temp'] = $details->temp;
      $data['feels_like'] = $details->feels_like;
      $data['temp_min'] = $details->temp_min;
      $data['temp_max'] = $details->temp_max;
      $data['humidity'] =  $details->humidity;
      $data['wind'] = $response_array->wind->speed;
      $data['defultcity'] = $config->get('city');
      $data['defultzip'] = $config->get('zipcode');
      foreach($response_array->weather as $key=>$value) {
        $data['descrip'] = $value->description;
      }
      //remove the cache in controller to update the dymanic data from API
      return [
          '#theme' => 'weather_page',
          '#attached' => [
             'library' => ['weather/weather_library'],
           ],
          '#content' => $data,
          '#cache' => ['max-age' => 0,]
      ];
    }
}