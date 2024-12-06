<?php

namespace Bektigalan\OsmAddress;

use Geocoder\Query\GeocodeQuery;
use Geocoder\Provider\Nominatim\Nominatim;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Geocoder\StatefulGeocoder;
use Psr\Log\LoggerInterface;

class Main
{
    protected $geocoder;
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $httpClient = new GuzzleAdapter();
        $provider = Nominatim::withOpenStreetMapServer(
            $httpClient,
            'Mozilla/5.0 (Linux; U; Android 4.1.1; en-gb; Build/KLP) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30'
        );

        $this->geocoder = new StatefulGeocoder($provider, 'id');
        $this->logger = $logger;
    }

    /**
     * Mendapatkan kota dari string alamat
     *
     * @param string $address
     * @return string
     */
    public function getCity(string $address): string
    {
        try {
            $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

            if ($result->isEmpty()) {
                return 'Tidak ada hasil yang ditemukan';
            }

            $location = $result->first();
            $city = $this->extractCity($location);

            return $city ?? 'Kota tidak ditemukan';
        } catch (\Exception $e) {
            $this->logError($e);
            return 'Kesalahan: ' . $e->getMessage();
        }
    }

    /**
     * Mendapatkan provinsi dari string alamat
     *
     * @param string $address
     * @return string
     */
    public function getProvince(string $address): string
    {
        try {
            $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

            if ($result->isEmpty()) {
                return 'Tidak ada hasil yang ditemukan';
            }

            $location = $result->first();
            $province = $this->extractProvince($location);

            return $province ?? 'Provinsi tidak ditemukan';
        } catch (\Exception $e) {
            $this->logError($e);
            return 'Kesalahan: ' . $e->getMessage();
        }
    }

    /**
     * Mendapatkan kode pos dari string alamat
     *
     * @param string $address
     * @return string
     */
    public function getPostalCode(string $address): string
    {
        try {
            $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

            if ($result->isEmpty()) {
                return 'Tidak ada hasil yang ditemukan';
            }

            $location = $result->first();
            $postalCode = $location->getPostalCode();

            return $postalCode ?? 'Kode pos tidak ditemukan';
        } catch (\Exception $e) {
            $this->logError($e);
            return 'Kesalahan: ' . $e->getMessage();
        }
    }

    /**
     * Mendapatkan detail lokasi lengkap
     *
     * @param string $address
     * @return array
     */
    public function getFullDetails(string $address): array
    {
        try {
            $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

            if ($result->isEmpty()) {
                return ['error' => 'Tidak ada hasil yang ditemukan'];
            }

            $location = $result->first();

            return [
                'city' => $this->extractCity($location),
                'province' => $this->extractProvince($location),
                'country' => $location->getCountry()?->getName(),
                'postal_code' => $location->getPostalCode(),
                'latitude' => $location->getCoordinates()?->getLatitude(),
                'longitude' => $location->getCoordinates()?->getLongitude(),
                'formatted_address' => $location->getDisplayName(),
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Mengekstrak kota dari lokasi.
     *
     * @param $location
     * @return string|null
     */
    private function extractCity($location): ?string
    {
        foreach ($location->getAdminLevels() as $level) {
            if ($level->getLevel() == 2) {
                return $level->getName();
            }
        }

        return $location->getLocality() ?? $location->getSubLocality() ?? null;
    }

    /**
     * Mengekstrak provinsi dari lokasi.
     *
     * @param $location
     * @return string|null
     */
    private function extractProvince($location): ?string
    {
        foreach ($location->getAdminLevels() as $level) {
            if ($level->getLevel() == 1) {
                return $level->getName();
            }
        }

        return null;
    }

    /**
     * Mencatat pesan kesalahan jika logger tersedia.
     *
     * @param \Exception $e
     */
    private function logError(\Exception $e): void
    {
        if ($this->logger) {
            $this->logger->error('Kesalahan geocoding: ' . $e->getMessage());
        }
    }
}
