WeatherAPI Widget Module (Drupal 10 / 11)
========================================

Overview
--------
This module integrates the WeatherAPI.com service to display real-time weather
information in a Drupal site using a configurable Block.

It follows modern Drupal best practices:
- Dependency Injection (DI)
- Service-based architecture
- Twig theming
- Cache integration
- Configuration via admin UI


API Used
--------
This module uses the WeatherAPI.com REST API.

Endpoint:
---------
GET /v1/current.json

Base URL:
---------
https://api.weatherapi.com/v1

Example Request:
----------------
https://api.weatherapi.com/v1/current.json?key=YOUR_API_KEY&q=Paris

Parameters:
-----------
- key (required) : Your WeatherAPI API Key
- q (required)   : Location query
                   Examples:
                     - City name (Paris)
                     - Lat/Long (48.8567,2.3508)
                     - Zip code (10001)
                     - IP-based lookup (auto:ip)

Response Structure:
-------------------
The API returns JSON containing:
- location
    - name
    - country
    - localtime
- current
    - temp_c (temperature Celsius)
    - feelslike_c
    - humidity
    - wind_kph
    - condition
        - text
        - icon
    - last_updated


Example Response:
-----------------
{
  "location": {
    "name": "Paris",
    "country": "France",
    "localtime": "2026-05-24 12:00"
  },
  "current": {
    "temp_c": 20,
    "feelslike_c": 21,
    "humidity": 60,
    "wind_kph": 12,
    "condition": {
      "text": "Partly cloudy",
      "icon": "//cdn.weatherapi.com/weather/64x64/day/116.png"
    }
  }
}


Module Features
---------------
- Drupal Block displaying weather information
- Configurable API Key and default location
- Clean UI using Twig templates
- Service-based backend logic
- Caching to reduce API calls
- Easily extendable to support other APIs


Installation
------------
1. Place module under:
   modules/custom/weatherapi_widget

2. Enable module:
   drush en weatherapi_widget -y

3. Configure API:
   Navigate to:
   /admin/config/services/weatherapi-widget

4. Place Block:
   Structure → Block Layout → Place block → "WeatherAPI Widget"


Configuration
-------------
Admin settings include:
- API Key (required)
- Default location (e.g., Paris)
- Cache TTL (seconds)
- Base API URL


Security Notes
--------------
- Never hardcode API keys in code
- Store API key via Drupal configuration
- Avoid committing config files with API keys to Git repository


Customization
-------------
- Modify Twig template for UI changes:
  templates/weatherapi-widget.html.twig

- Adjust styles:
  css/weatherapi_widget.css

- Extend service for additional APIs:
  src/Service/WeatherApiClient.php


Extensibility
-------------
The service `weatherapi_widget.client` is globally available and can be used
in other modules via Dependency Injection.

Example:
--------
$container->get('weatherapi_widget.client')->current('Lodz');


License
-------
Custom internal module for Drupal project usage.

Author
------
Drupal Custom Module (WeatherAPI Integration)