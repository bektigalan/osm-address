# Bektigalan\OsmAddress

Bektigalan\OsmAddress is a PHP package that provides geocoding capabilities using the Nominatim service from OpenStreetMap. It allows you to extract city, province, postal code, and other location details from an address string.

## Installation

To use this package in your project, you need to have Composer installed. Then, you can require the package in your project.

```bash
composer require bektigalan/osm-address
```

# Usage
Here is how you can use the Bektigalan\OsmAddress package in your project:

## Basic Setup

```php
require 'vendor/autoload.php';

use Bektigalan\OsmAddress\Main;

// Initialize Main class
$geocoder = new Main();

// Example address
$address = 'Jalan Ahmad Yani Nganjuk';

// Get city
$city = $geocoder->getCity($address);
echo "City: $city\n";

// Get province
$province = $geocoder->getProvince($address);
echo "Province: $province\n";

// Get postal code
$postalCode = $geocoder->getPostalCode($address);
echo "Postal Code: $postalCode\n";

// Get full details
$details = $geocoder->getFullDetails($address);
print_r($details);
```

## Methods
`getCity(string $address): string:` Returns the city name from the given address.
`getProvince(string $address): string:` Returns the province or state from the given address.
`getPostalCode(string $address): string:` Returns the postal code from the given address.
`getFullDetails(string $address): array:` Returns an array with full location details including city, province, country, postal code, latitude, longitude, and formatted address.

## Error Handling
The package logs errors using a PSR-3 compatible logger if provided. Ensure that you have a logger set up to capture these logs for debugging purposes.

## Requirements
PHP 7.4 or higher

## License
This package is open-source and available under the MIT License. See LICENSE for more details.


### Explanation

- **Installation**: Instructions on how to install the package using Composer.
- **Usage**: Provides example code on how to initialize and use the package, including setting up a logger.
- **Methods**: Describes the available methods and their purposes.
- **Error Handling**: Mentions the use of a PSR-3 logger for capturing errors.
- **Requirements**: Lists the PHP version and other dependencies.
- **License**: MIT License.

This `README.md` serves as a comprehensive guide for users to quickly understand and implement the package in their projects.
