# Road-Based Routing System Setup

## Overview
This project now includes a road-based routing system that calculates actual driving routes between postal offices instead of straight-line distances. The system uses OSRM (Open Source Routing Machine) to calculate routes via roads.

## Key Changes Made

### 1. Updated get_route.php
- Changed OSRM API parameters to use `geometries=polyline` and `steps=false` for optimal route data
- Improved error handling with fallback to straight-line distance when OSRM is unavailable
- Added proper coordinate formatting for OSRM (longitude, latitude order)

### 2. Enhanced JavaScript (in calculator.php)
- Added polyline decoding function to properly interpret OSRM geometry data
- Implemented robust route display logic with multiple fallback options
- Added proper coordinate transformation from OSRM format to Leaflet format

### 3. Database Integration
- Routes are cached in the `calculated_routes` table to avoid repeated API calls
- New routes are calculated only once and reused for subsequent requests

## How to Use

### 1. Clear Existing Cache
Before using the new road-based routing, clear the old cached routes:
```bash
php reset_routes.php
```

### 2. Using the Calculator
1. Navigate to the calculator page
2. Select a carrier
3. Select "from" and "to" offices
4. Click "Показать маршрут" (Show Route)
5. The system will display the actual road-based route instead of a straight line

### 3. Expected Behavior
- First time calculating a route between two offices: May take a few seconds as it fetches from OSRM
- Subsequent calculations between the same offices: Instant, using cached data
- Routes will follow actual roads instead of straight lines
- Distance and duration will reflect real driving conditions

## Troubleshooting

### If routes still appear as straight lines:
1. Make sure the `calculated_routes` table is empty or has been cleared
2. Verify that the database connection in `db.php` is correct
3. Check browser console for JavaScript errors
4. Ensure that the Leaflet.PolylineUtil library is loaded (already included in calculator.php)

### If OSRM API fails:
- The system will automatically fall back to straight-line distance calculation
- Check your internet connection
- The OSRM public server may be temporarily unavailable

### Alternative Routing APIs

If the default OSRM server is not accessible, you can configure alternative routing services:

#### 1. OpenRouteService (ORS)
- Register at https://openrouteservice.org/ for an API key
- Update `get_route.php` to use ORS API instead of OSRM

#### 2. GraphHopper
- Register at https://www.graphhopper.com/ for an API key
- Update `get_route.php` to use GraphHopper API instead of OSRM

#### 3. Google Maps API
- Get an API key from Google Cloud Console
- Use Google Maps Directions API
- Note: This service is not free beyond certain usage limits

Example of how to modify `get_route.php` for OpenRouteService:
```php
// Replace the OSRM URL with OpenRouteService
$ors_url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=YOUR_API_KEY&start={$from_office['lng']},{$from_office['lat']}&end={$to_office['lng']},{$to_office['lat']}";

// Update headers for ORS
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: YOUR_API_KEY',
    'Accept: application/json',
    'Content-Type: application/json'
]);
```

## Technical Details

### OSRM API Parameters Used
- `overview=full`: Provides complete geometry for the route
- `geometries=polyline`: Returns geometry in Google polyline encoding format
- `steps=false`: Simplifies response by excluding turn-by-turn instructions

### Coordinate Order
- OSRM API expects: longitude, latitude (lng, lat)
- Leaflet expects: latitude, longitude (lat, lng)
- The system handles proper conversion between formats

## Files Modified
- `get_route.php`: Backend route calculation and caching
- `calculator.php`: Frontend JavaScript with enhanced routing function
- `reset_routes.php`: Utility to clear cached routes