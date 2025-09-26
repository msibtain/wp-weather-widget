<?php
/*
Plugin Name: Weather Widget
Plugin URI: https://github.com/msibtain/wp-weather-widget
Description: Weather Widget for WordPress 
Author: msibtain
Version: 1.0.0
Author URI: https://github.com/msibtain/wp-weather-widget
*/

// Include admin settings
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
}

class clsWeatherData {

    private $option_name = 'ms_weather_options';

    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        add_shortcode('ms_weather_widget', array($this, 'weather_widget'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles() {
        // wp_add_inline_style('wp-block-library', $this->get_weather_widget_styles());
        // wp_add_inline_style('wp-block-library', $this->weather_style());
    }

    private function get_weather_widget_styles() {
        return '
        .weather-widget {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 25px;
            margin: 15px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: white;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            gap: 15px;
            min-height: 80px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .weather-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }
        
        .weather-widget::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .weather-widget .weather-icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .weather-widget .weather-icon img {
            width: 32px;
            height: 32px;
        }
        
        .weather-widget .weather-temp {
            font-size: 2.2em;
            font-weight: 700;
            line-height: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .weather-widget .weather-desc {
            font-size: 1.1em;
            font-weight: 500;
            opacity: 0.9;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .weather-widget .weather-separator {
            font-size: 1.5em;
            opacity: 0.6;
            margin: 0 8px;
        }
        
        @media (max-width: 768px) {
            .weather-widget {
                padding: 20px;
                gap: 12px;
                min-height: 70px;
            }
            
            .weather-widget .weather-temp {
                font-size: 1.8em;
            }
            
            .weather-widget .weather-desc {
                font-size: 1em;
            }
            
            .weather-widget .weather-icon {
                width: 45px;
                height: 45px;
            }
            
            .weather-widget .weather-icon img {
                width: 28px;
                height: 28px;
            }
        }
        
        @media (max-width: 480px) {
            .weather-widget {
                flex-direction: column;
                text-align: center;
                padding: 20px 15px;
                gap: 10px;
            }
            
            .weather-widget .weather-temp {
                font-size: 1.6em;
            }
        }
        ';
    }

    private function weather_style() {
        return '
        .weather-widget {
            display: flex;
            gap: 15px;
            justify-content: center;
            color: #fff;
        }
        .weather-icon img {
            width: 32px;
        }
        .weather-separator {
            margin-right: 8px;
        }
        .weather-forecast-link {
            margin-left: 8px;
            color: #fff !important;
        }
        ';
    }

    public function weather_widget() {
        ob_start();
        $options = $this->get_options();
        $address = isset($options['address']) ? trim($options['address']) : '';
        $latitude = isset($options['latitude']) ? trim($options['latitude']) : '';
        $longitude = isset($options['longitude']) ? trim($options['longitude']) : '';

        if (empty($address) && (empty($latitude) || empty($longitude))) {
            echo 'Please set an Address or Latitude/Longitude in Settings → Weather Widget.';
            return ob_get_clean();
        }

        try {
            $weather = $this->get_weather_for_location();
            

            ?>
            <div class="weather-widget">
                <div class="weather-icon">
                    <?php $image = $this->getWeatherIcon($weather['current_weather']['weathercode']); ?>
                    <img src="<?php echo plugin_dir_url(__FILE__) . 'icons/' . $image; ?>" alt="<?php echo $this->getWeatherDescription($weather['current_weather']['weathercode']); ?>"> | 
                </div>
                <div class="weather-temp">
                    <?php
                    $temperature = $weather['current_weather']['temperature'];
                    $temperature = $this->celsiusToFahrenheit($temperature);
                    echo round($temperature);
                    ?>
                    <span class="weather-separator">°F</span> | 
                </div>
                <div class="weather-desc"><?php echo $this->getWeatherDescription($weather['current_weather']['weathercode']); ?></div>
                <div>
                    | <a class="weather-forecast-link" href="https://weather.com/weather/today/l/3e97be086875e7cef17ae1290de6a70ec5773282a61fd11259451de678fb4bb8" target="_blank">View Extended Forecast</a>
                </div>
            </div>
            <?php
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        return ob_get_clean();
    }

    private function celsiusToFahrenheit($celsius) {
        return ($celsius * 9/5) + 32;
    }

    private function getWeatherIcon($code) {
        $icons = array(
            0 => 'w-0.png',
            1 => 'w-1-2-3.png',
            2 => 'w-1-2-3.png',
            3 => 'w-1-2-3.png',
            45 => 'w-45-48.png',
            48 => 'w-45-48.png',
            51 => 'w-51-53-55.png',
            53 => 'w-51-53-55.png',
            55 => 'w-51-53-55.png',
            56 => 'w-56-57.png',
            57 => 'w-56-57.png',
            61 => 'w-61-63-65.png',
            63 => 'w-61-63-65.png',
            65 => 'w-61-63-65.png',
            66 => 'w-66-67.png',
            67 => 'w-66-67.png',
            71 => 'w-71-73-75-77-85-86.png',
            73 => 'w-71-73-75-77-85-86.png',
            75 => 'w-71-73-75-77-85-86.png',
            77 => 'w-71-73-75-77-85-86.png',
            80 => 'w-80-81-82.png',
            81 => 'w-80-81-82.png',
            82 => 'w-80-81-82.png',
            85 => 'w-71-73-75-77-85-86.png',
            86 => 'w-71-73-75-77-85-86.png',
            95 => 'w-95-96-99.png',
            96 => 'w-95-96-99.png',
            99 => 'w-95-96-99.png',
        );
        return $icons[$code];
    }

    private function getWeatherDescription($code) {
        $descriptions = array(
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45 => 'Fog and depositing rime fog',
            48 => 'Fog and depositing rime fog',
            51 => 'Drizzle: Light intensity',
            53 => 'Drizzle: Moderate intensity',
            55 => 'Drizzle: Dense intensity',
            56 => 'Freezing Drizzle: Light intensity',
            57 => 'Freezing Drizzle: Dense intensity',
            61 => 'Rain: Slight intensity',
            63 => 'Rain: Moderate intensity',
            65 => 'Rain: Heavy intensity',
            66 => 'Freezing Rain: Light intensity',
            67 => 'Freezing Rain: Heavy intensity',
            71 => 'Snow fall: Slight intensity',
            73 => 'Snow fall: Moderate intensity',
            75 => 'Snow fall: Heavy intensity',
            77 => 'Snow grains',
            80 => 'Rain showers: Slight intensity',
            81 => 'Rain showers: Moderate intensity',
            82 => 'Rain showers: Violent intensity',
            85 => 'Snow showers: Slight intensity',
            86 => 'Snow showers: Heavy intensity',
            95 => 'Thunderstorm: Slight intensity',
            96 => 'Thunderstorm with slight hail',
            99 => 'Thunderstorm with heavy hail',
        );
        return $descriptions[$code];
    }

    function getWeather($lat, $lon) {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        return $data;
    }

    private function get_options() {
        $defaults = array(
            'address' => '',
            'latitude' => '',
            'longitude' => '',
            'interval_minutes' => 60,
            'cached_lat' => null,
            'cached_lon' => null,
            'last_fetched_at' => 0,
            'last_weather_json' => ''
        );
        $saved = get_option($this->option_name, array());
        if (!is_array($saved)) { $saved = array(); }
        return array_merge($defaults, $saved);
    }

    private function update_options($options) {
        update_option($this->option_name, $options);
    }

    private function geocode_address($address) {
        $encoded = urlencode($address);
        // $url = "https://geocoding-api.open-meteo.com/v1/search?count=1&language=en&format=json&name={$encoded}";
        $url = "https://nominatim.openstreetmap.org/search?q={$encoded}&format=json&limit=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WordPress Weather Widget');
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if (!$data || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
            throw new Exception('Unable to geocode the provided address.');
        }
        $first = $data[0];
        return array('lat' => $first['lat'], 'lon' => $first['lon']);
    }

    private function should_refresh($options) {
        $interval = isset($options['interval_minutes']) ? intval($options['interval_minutes']) : 60;
        if ($interval < 1) { $interval = 60; }
        $last = isset($options['last_fetched_at']) ? intval($options['last_fetched_at']) : 0;
        return (time() - $last) >= ($interval * 60);
    }

    private function get_weather_for_location() {
        $options = $this->get_options();
        $address = trim($options['address']);
        $latitude = trim($options['latitude']);
        $longitude = trim($options['longitude']);

        // Determine if we have direct coordinates or need to geocode
        $has_direct_coords = !empty($latitude) && !empty($longitude);
        $has_cached_coords = !empty($options['cached_lat']) && !empty($options['cached_lon']);

        if ($this->should_refresh($options) || !$has_cached_coords || empty($options['last_weather_json'])) {
            $lat = null;
            $lon = null;

            if ($has_direct_coords) {
                // Use direct coordinates
                $lat = floatval($latitude);
                $lon = floatval($longitude);
            } elseif (!empty($address)) {
                // Geocode address
                $coords = $this->geocode_address($address);
                $lat = $coords['lat'];
                $lon = $coords['lon'];
            } else {
                throw new Exception('No location configured.');
            }

            $weather = $this->getWeather($lat, $lon);
            $options['last_weather_json'] = json_encode($weather);
            $options['last_fetched_at'] = time();
            $options['cached_lat'] = $lat;
            $options['cached_lon'] = $lon;
            $this->update_options($options);
            return $weather;
        }

        $decoded = json_decode($options['last_weather_json'], true);
        if (!$decoded) {
            // Fallback: force refresh once if cache is invalid
            $options['last_fetched_at'] = 0;
            $this->update_options($options);
            return $this->get_weather_for_location();
        }
        return $decoded;
    }

    
}

new clsWeatherData();