# Weather Widget for WordPress

A beautiful, responsive weather widget plugin for WordPress that displays current weather conditions with modern styling and caching capabilities.

## 🌟 Features

### Core Functionality
- **Real-time Weather Data**: Fetches current weather conditions using the Open-Meteo API
- **Multiple Location Input Methods**: 
  - Enter a city/address name (automatically geocoded)
  - Use direct latitude/longitude coordinates
- **Smart Caching System**: Configurable refresh intervals to reduce API calls and improve performance
- **Responsive Design**: Beautiful gradient widget that adapts to all screen sizes
- **Weather Icons**: Comprehensive set of weather condition icons for all weather types

### Weather Conditions Supported
The plugin supports all major weather conditions with appropriate icons and descriptions:

- **Clear Skies**: Clear sky, mainly clear, partly cloudy, overcast
- **Precipitation**: Drizzle (light, moderate, dense), rain (slight, moderate, heavy), freezing rain
- **Snow**: Snow fall (slight, moderate, heavy), snow grains, snow showers
- **Fog**: Fog and depositing rime fog
- **Thunderstorms**: Thunderstorms with various intensities and hail conditions
- **Rain Showers**: Light, moderate, and violent intensity rain showers

### Technical Features
- **WordPress Shortcode**: Easy integration with `[ms_weather_widget]`
- **Admin Settings Panel**: Complete configuration through WordPress admin
- **Automatic Geocoding**: Converts addresses to coordinates using OpenStreetMap Nominatim
- **Error Handling**: Graceful error handling with user-friendly messages
- **Performance Optimized**: Caching system prevents excessive API calls
- **Mobile Responsive**: Optimized display for mobile devices

## 🚀 Installation

1. **Download the Plugin**: Clone or download this repository
2. **Upload to WordPress**: 
   - Upload the `wp-weather-widget` folder to your `/wp-content/plugins/` directory
   - Or install via WordPress admin → Plugins → Add New → Upload Plugin
3. **Activate**: Go to Plugins in your WordPress admin and activate "Weather Widget"

## ⚙️ Configuration

### Admin Settings
Navigate to **Settings → Weather Widget** in your WordPress admin to configure:

#### Location Settings
- **Address**: Enter a city, address, or place name (e.g., "Berlin, DE", "New York, NY")
- **Latitude/Longitude**: Use direct coordinates for precise location control
- **Auto-geocoding**: If you provide an address, the plugin automatically converts it to coordinates

#### Performance Settings
- **Update Interval**: Set how often weather data refreshes (default: 60 minutes)
- **Smart Caching**: Weather data is cached and only refreshed when needed

### Usage

#### Shortcode
Add the weather widget anywhere on your site using the shortcode:
```
[ms_weather_widget]
```

#### Widget Display
The widget displays:
- **Weather Icon**: Visual representation of current conditions
- **Temperature**: Current temperature in Fahrenheit
- **Description**: Human-readable weather condition description

## 🎨 Styling

### Modern Design Features
- **Gradient Background**: Beautiful purple-blue gradient with hover effects
- **Glass Morphism**: Frosted glass effect with backdrop blur
- **Responsive Layout**: Adapts from horizontal to vertical layout on mobile
- **Smooth Animations**: Hover effects and transitions for enhanced UX
- **Typography**: Modern system fonts with proper text shadows

### Customization
The plugin includes comprehensive CSS that can be customized by:
- Adding custom CSS to your theme
- Using WordPress customizer
- Modifying the plugin's inline styles

## 📱 Responsive Design

### Desktop (768px+)
- Horizontal layout with icon, temperature, and description
- Large, prominent temperature display
- Full gradient background with hover effects

### Tablet (480px - 768px)
- Optimized spacing and sizing
- Maintains horizontal layout with adjusted proportions

### Mobile (480px and below)
- Vertical layout for better mobile experience
- Centered alignment
- Reduced font sizes for optimal readability

## 🔧 Technical Details

### API Integration
- **Weather Data**: Open-Meteo API (free, no API key required)
- **Geocoding**: OpenStreetMap Nominatim API
- **Data Format**: JSON responses with comprehensive weather information

### Caching System
- **Intelligent Caching**: Only fetches new data when interval expires
- **Location Caching**: Stores coordinates to avoid repeated geocoding
- **Error Recovery**: Automatic fallback mechanisms for failed requests

### Performance Features
- **Minimal API Calls**: Configurable refresh intervals
- **Efficient Caching**: Stores weather data in WordPress options
- **Lightweight**: No external dependencies or heavy libraries

## 🛠️ Development

### File Structure
```
wp-weather-widget/
├── init.php              # Main plugin file with widget logic
├── admin-settings.php    # Admin settings page
├── icons/               # Weather condition icons
│   ├── w-0.png         # Clear sky
│   ├── w-1-2-3.png     # Partly cloudy
│   ├── w-45-48.png     # Fog
│   ├── w-51-53-55.png  # Drizzle
│   ├── w-56-57.png     # Freezing drizzle
│   ├── w-61-63-65.png  # Rain
│   ├── w-66-67.png     # Freezing rain
│   ├── w-71-73-75-77-85-86.png # Snow
│   ├── w-80-81-82.png  # Rain showers
│   └── w-95-96-99.png  # Thunderstorms
└── README.md           # This file
```

### Hooks and Filters
The plugin uses standard WordPress hooks:
- `init` - Plugin initialization
- `wp_enqueue_scripts` - Style enqueuing
- `admin_menu` - Admin settings page
- `admin_init` - Settings registration

## 🐛 Troubleshooting

### Common Issues

**Widget not displaying:**
- Check that location is configured in Settings → Weather Widget
- Verify that either address or coordinates are provided
- Check for JavaScript errors in browser console

**Weather data not updating:**
- Verify internet connection
- Check if API services are accessible
- Review update interval settings

**Styling issues:**
- Clear any caching plugins
- Check for theme CSS conflicts
- Verify plugin is properly activated

### Error Messages
- **"Please set an Address or Latitude/Longitude"**: Configure location in admin settings
- **"Unable to geocode address"**: Try using coordinates instead of address
- **"cURL error"**: Check server cURL configuration

## 📄 License

This plugin is open source. Please check the license file for specific terms.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

## 📞 Support

For support, feature requests, or bug reports, please open an issue on the GitHub repository.

---

**Author**: msibtain  
**Version**: 1.0.0  
**Plugin URI**: https://github.com/msibtain/wp-weather-widget
