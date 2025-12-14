# Road-Based Route Calculation System

## Overview
This system now calculates actual driving routes between postal offices using real road networks instead of straight-line distances. It uses the OSRM (Open Source Routing Machine) API to determine the actual path a vehicle would take between locations.

## How It Works

1. When a user selects two offices in the calculator, the system first checks if a route has already been calculated and stored in the database
2. If not found, it makes a request to the OSRM API to calculate the actual driving route
3. The route geometry is returned in Google polyline format
4. The route, distance, and duration are stored in the database for future use
5. The route is displayed on the map following actual roads

## Key Improvements

1. **Polyline Decoding**: Added proper Google polyline decoding to handle OSRM geometry data
2. **Enhanced Error Handling**: Multiple fallback mechanisms if OSRM API fails
3. **Better API Parameters**: Using `geometries=polyline` and `steps=false` for optimal data
4. **Coordinate Transformation**: Proper conversion between OSRM (lng, lat) and Leaflet (lat, lng) formats
5. **Caching System**: Routes are stored in database to avoid repeated API calls

## Files Updated

- `get_route.php` - Backend route calculation with OSRM integration
- `calculator.php` - Frontend JavaScript with polyline decoding
- `reset_routes.php` - Utility to clear cached routes

## API Used

- OSRM Router API: `https://router.project-osrm.org/route/v1/driving/{lng1},{lat1};{lng2},{lat2}?overview=full&geometries=polyline&steps=false`
- Returns route geometry in Google polyline encoding format
- Provides distance in meters and duration in seconds

## Clearing Old Cached Routes

To ensure the new road-based routing takes effect, clear the existing cached routes:

```bash
php reset_routes.php
```

After running this, all new route requests will fetch actual road-based routes from OSRM.

## Troubleshooting

### If routes still appear as straight lines:
1. Run the reset_routes.php script to clear old cached data
2. Verify that the database connection in `db.php` is correct
3. Check browser console for JavaScript errors
4. Ensure that coordinates in the offices table are accurate

### If OSRM API fails:
- The system will automatically fall back to straight-line distance calculation
- Check your internet connection
- The OSRM public server may be temporarily unavailable