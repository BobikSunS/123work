# Alternative Route API Setup Instructions

## Overview

The system is designed to calculate road-based routes between postal offices. When the primary OSRM API is not accessible, you can configure alternative routing services.

## Current Configuration

The system currently uses:
- Primary: OSRM (Open Source Routing Machine) - https://router.project-osrm.org
- Fallback: Straight-line distance calculation

## Alternative API Options

### 1. OpenRouteService (Recommended Free Option)

1. Go to https://openrouteservice.org/ and register for a free account
2. Get your API key from the dashboard
3. Update the routing function in `get_route.php` to use ORS instead of OSRM

Example implementation:
```php
// In get_route.php, replace the OSRM section with:
$ors_url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=YOUR_API_KEY&start={$from_office['lng']},{$from_office['lat']}&end={$to_office['lng']},{$to_office['lat']}";

curl_setopt($ch, CURLOPT_URL, $ors_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: YOUR_API_KEY',
    'Accept: application/json',
    'Content-Type: application/json'
]);
```

### 2. GraphHopper

1. Go to https://www.graphhopper.com/ and sign up for an API key
2. Update `get_route.php` with GraphHopper API endpoint

### 3. Google Maps Directions API

1. Go to Google Cloud Console
2. Enable the Directions API
3. Create credentials and get an API key
4. Update `get_route.php` with Google's API endpoint

## Testing the Setup

After configuring an alternative API:

1. Clear the route cache: `php reset_routes.php`
2. Test the route calculation in the calculator
3. Check browser console for any errors
4. Verify that routes follow roads instead of straight lines

## Troubleshooting

- If routes still appear as straight lines, ensure the `calculated_routes` table is empty
- Check that the API key has sufficient quota
- Verify that the API returns route geometry in a compatible format
- Make sure the polyline decoding function works with the new API's output format