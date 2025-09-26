<?php
/**
 * Admin Settings for Weather Widget
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class MS_Weather_Admin_Settings {
    
    private $option_name = 'ms_weather_options';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function register_settings_page() {
        add_options_page(
            'Weather Widget',
            'Weather Widget',
            'manage_options',
            'ms-weather-widget',
            array($this, 'render_settings_page')
        );
    }
    
    public function register_settings() {
        register_setting(
            'ms_weather_options_group',
            $this->option_name,
            array($this, 'sanitize_options')
        );
    }
    
    public function sanitize_options($input) {
        $output = $this->get_options();
        $new_address = isset($input['address']) ? sanitize_text_field($input['address']) : '';
        $new_latitude = isset($input['latitude']) ? sanitize_text_field($input['latitude']) : '';
        $new_longitude = isset($input['longitude']) ? sanitize_text_field($input['longitude']) : '';
        $interval = isset($input['interval_minutes']) ? intval($input['interval_minutes']) : 60;
        if ($interval < 1) { $interval = 60; }
        
        // Check if coordinates or address changed
        $address_changed = $new_address !== $output['address'];
        $coords_changed = ($new_latitude !== $output['latitude']) || ($new_longitude !== $output['longitude']);
        
        if ($address_changed || $coords_changed) {
            $output['cached_lat'] = null;
            $output['cached_lon'] = null;
            $output['last_fetched_at'] = 0;
            $output['last_weather_json'] = '';
        }
        
        $output['address'] = $new_address;
        $output['latitude'] = $new_latitude;
        $output['longitude'] = $new_longitude;
        $output['interval_minutes'] = $interval;
        
        // If address is provided and coordinates are not, geocode the address
        if (!empty($new_address) && (empty($new_latitude) || empty($new_longitude))) {
            $this->geocode_and_save_coordinates($new_address, $output);
        }
        
        return $output;
    }
    
    private function geocode_and_save_coordinates($address, &$output) {
        try {
            $encoded = urlencode($address);
            $url = "https://nominatim.openstreetmap.org/search?q={$encoded}&format=json&limit=1";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'WordPress Weather Widget');
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                add_settings_error('ms_weather_options', 'geocoding_error', 'Error geocoding address: ' . curl_error($ch));
                curl_close($ch);
                return;
            }
            curl_close($ch);
            $data = json_decode($response, true);
            if ($data && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $output['latitude'] = $data[0]['lat'];
                $output['longitude'] = $data[0]['lon'];
                add_settings_error('ms_weather_options', 'geocoding_success', 'Address geocoded successfully: ' . $data[0]['display_name'], 'updated');
            } else {
                add_settings_error('ms_weather_options', 'geocoding_error', 'Unable to geocode the provided address.');
            }
        } catch (Exception $e) {
            add_settings_error('ms_weather_options', 'geocoding_error', 'Geocoding error: ' . $e->getMessage());
        }
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
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) { return; }
        $options = $this->get_options();
        ?>
        <div class="wrap">
            <h1>Weather Widget Settings</h1>
            <?php settings_errors('ms_weather_options'); ?>
            <form method="post" action="options.php">
                <?php settings_fields('ms_weather_options_group'); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="msww_address">Address</label></th>
                        <td>
                            <input name="<?php echo esc_attr($this->option_name); ?>[address]" id="msww_address" type="text" class="regular-text" value="<?php echo esc_attr($options['address']); ?>" placeholder="e.g., Berlin, DE" />
                            <p class="description">City, address, or place name to fetch weather for. Leave empty if using coordinates below.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="msww_latitude">Latitude</label></th>
                        <td>
                            <input name="<?php echo esc_attr($this->option_name); ?>[latitude]" id="msww_latitude" type="number" step="any" class="regular-text" value="<?php echo esc_attr($options['latitude']); ?>" placeholder="e.g., 52.5200" />
                            <p class="description">Latitude coordinate (optional if address is provided).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="msww_longitude">Longitude</label></th>
                        <td>
                            <input name="<?php echo esc_attr($this->option_name); ?>[longitude]" id="msww_longitude" type="number" step="any" class="regular-text" value="<?php echo esc_attr($options['longitude']); ?>" placeholder="e.g., 13.4050" />
                            <p class="description">Longitude coordinate (optional if address is provided).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="msww_interval">Update Interval (minutes)</label></th>
                        <td>
                            <input name="<?php echo esc_attr($this->option_name); ?>[interval_minutes]" id="msww_interval" type="number" min="1" step="1" value="<?php echo esc_attr(intval($options['interval_minutes'])); ?>" />
                            <p class="description">Minimum time between weather API refreshes. Defaults to 60 minutes.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
        <?php
    }
}

// Initialize admin settings
new MS_Weather_Admin_Settings();
