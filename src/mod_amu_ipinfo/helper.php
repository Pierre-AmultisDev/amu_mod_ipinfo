<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_amu_ipinfo
 *
 * @copyright   Copyright (C) 2024 Amultis. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;

/**
 * Helper class for mod_amu_ipinfo
 */
class ModAmuIPInfoHelper
{
    /**
     * Detect the real client IP address, supporting IPv4, IPv6, and dual-stack.
     *
     * @return string
     */
    public static function getClientIP(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',   // Cloudflare
            'HTTP_X_REAL_IP',          // Nginx proxy
            'HTTP_X_FORWARDED_FOR',    // Load balancers / proxies
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            $value = $_SERVER[$header] ?? '';
            if (empty($value)) {
                continue;
            }
            // X-Forwarded-For can contain a comma-separated list; take the first (client) IP
            $ips = array_map('trim', explode(',', $value));
            foreach ($ips as $ip) {
                if (self::isValidIP($ip)) {
                    return $ip;
                }
            }
        }

        return 'Unknown';
    }

    /**
     * Validate an IP address (IPv4 or IPv6).
     *
     * @param   string  $ip
     * @return  bool
     */
    public static function isValidIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false
            || filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Detect IP version.
     *
     * @param   string  $ip
     * @return  string  'IPv4', 'IPv6', or 'Unknown'
     */
    public static function getIPVersion(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 'IPv4';
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 'IPv6';
        }
        return 'Unknown';
    }

    /**
     * Check if IP is private/reserved.
     *
     * @param   string  $ip
     * @return  bool
     */
    public static function isPrivateIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
            && filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Retrieve Geo IP data from the selected provider.
     *
     * @param   string              $ip
     * @param   string              $provider
     * @param   \Joomla\Registry\Registry  $params
     * @return  array  [data|null, error|null]
     */
    public static function getGeoIP(string $ip, string $provider, $params): array
    {
        // Don't look up private/reserved IPs
        if (self::isPrivateIP($ip) || $ip === 'Unknown') {
            return [null, 'private_ip'];
        }

        $cacheKey = 'geoip_' . md5($ip . $provider);

        // Try cache first (10-minute TTL)
        $app = Factory::getApplication();
        try {
            /** @var \Joomla\CMS\Cache\Controller\CallbackController $cache */
            $cache = Factory::getContainer()
                ->get(CacheControllerFactoryInterface::class)
                ->createCacheController('callback', ['defaultgroup' => 'mod_amu_ipinfo', 'lifetime' => 10]);

            $cached = $cache->get(function () use ($ip, $provider, $params) {
                return self::fetchGeoIP($ip, $provider, $params);
            }, [], $cacheKey);

            return $cached;
        } catch (\Exception $e) {
            return self::fetchGeoIP($ip, $provider, $params);
        }
    }

    /**
     * Actually fetch Geo IP data from external API.
     *
     * @param   string  $ip
     * @param   string  $provider
     * @param   mixed   $params
     * @return  array
     */
    private static function fetchGeoIP(string $ip, string $provider, $params): array
    {
        $http = HttpFactory::getHttp();
        $timeout = 5;

        try {
            switch ($provider) {
                case 'ip-api':
                    // Free, no key needed, 45 req/min
                    $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query";
                    $response = $http->get($url, [], $timeout);
                    $body = json_decode($response->body, true);
                    if (!is_array($body)) {
                        return [null, 'Invalid response from ip-api.com'];
                    }
                    if (($body['status'] ?? '') === 'success') {
                        return [self::normalizeGeoData($body, 'ip-api'), null];
                    }
                    return [null, $body['message'] ?? 'API error'];

                case 'ipwhois':
                    // Free up to 10,000 req/month, no key
                    $url = "https://ipwhois.app/json/{$ip}";
                    $response = $http->get($url, [], $timeout);
                    $body = json_decode($response->body, true);
                    if (!is_array($body)) {
                        return [null, 'Invalid response from ipwhois.app'];
                    }
                    if ($body['success'] ?? false) {
                        return [self::normalizeGeoData($body, 'ipwhois'), null];
                    }
                    return [null, $body['message'] ?? 'API error'];

                case 'ipapi-co':
                    // Free up to 1,000 req/day, no key
                    $url = "https://ipapi.co/{$ip}/json/";
                    $response = $http->get($url, [], $timeout);
                    $body = json_decode($response->body, true);
                    if (!is_array($body)) {
                        return [null, 'Invalid response from ipapi.co'];
                    }
                    if (!isset($body['error'])) {
                        return [self::normalizeGeoData($body, 'ipapi-co'), null];
                    }
                    return [null, $body['reason'] ?? 'API error'];

                case 'ipgeolocation':
                    // Free tier: 1,000 req/day, API key required
                    $apiKey = trim($params->get('ipgeolocation_key', ''));
                    if (empty($apiKey)) {
                        return [null, 'missing_key'];
                    }
                    $url = "https://api.ipgeolocation.io/ipgeo?apiKey={$apiKey}&ip={$ip}";
                    $response = $http->get($url, [], $timeout);
                    $body = json_decode($response->body, true);
                    if (!is_array($body)) {
                        return [null, 'Invalid response from ipgeolocation.io'];
                    }
                    if (!isset($body['message'])) {
                        return [self::normalizeGeoData($body, 'ipgeolocation'), null];
                    }
                    return [null, $body['message']];

                case 'abstractapi':
                    // Free tier: 20,000 req/month, API key required
                    $apiKey = trim($params->get('abstractapi_key', ''));
                    if (empty($apiKey)) {
                        return [null, 'missing_key'];
                    }
                    $url = "https://ipgeolocation.abstractapi.com/v1/?api_key={$apiKey}&ip_address={$ip}";
                    $response = $http->get($url, [], $timeout);
                    $body = json_decode($response->body, true);
                    if (!is_array($body)) {
                        return [null, 'Invalid response from abstractapi.com'];
                    }
                    if (!isset($body['error'])) {
                        return [self::normalizeGeoData($body, 'abstractapi'), null];
                    }
                    return [null, $body['error']['message'] ?? 'API error'];

                default:
                    return [null, 'unknown_provider'];
            }
        } catch (\Exception $e) {
            return [null, $e->getMessage()];
        }
    }

    /**
     * Normalize different API responses to a consistent format.
     *
     * @param   array   $data
     * @param   string  $provider
     * @return  array
     */
    private static function normalizeGeoData(array $data, string $provider): array
    {
        switch ($provider) {
            case 'ip-api':
                return [
                    'country'      => $data['country'] ?? '',
                    'country_code' => strtolower($data['countryCode'] ?? ''),
                    'region'       => $data['regionName'] ?? '',
                    'city'         => $data['city'] ?? '',
                    'postal'       => $data['zip'] ?? '',
                    'latitude'     => $data['lat'] ?? '',
                    'longitude'    => $data['lon'] ?? '',
                    'timezone'     => $data['timezone'] ?? '',
                    'isp'          => $data['isp'] ?? '',
                    'org'          => $data['org'] ?? '',
                    'asn'          => $data['as'] ?? '',
                ];

            case 'ipwhois':
                return [
                    'country'      => $data['country'] ?? '',
                    'country_code' => strtolower($data['country_code'] ?? ''),
                    'region'       => $data['region'] ?? '',
                    'city'         => $data['city'] ?? '',
                    'postal'       => $data['postal'] ?? '',
                    'latitude'     => $data['latitude'] ?? '',
                    'longitude'    => $data['longitude'] ?? '',
                    'timezone'     => $data['timezone'] ?? '',
                    'isp'          => $data['isp'] ?? '',
                    'org'          => $data['org'] ?? '',
                    'asn'          => $data['asn'] ?? '',
                ];

            case 'ipapi-co':
                return [
                    'country'      => $data['country_name'] ?? '',
                    'country_code' => strtolower($data['country_code'] ?? ''),
                    'region'       => $data['region'] ?? '',
                    'city'         => $data['city'] ?? '',
                    'postal'       => $data['postal'] ?? '',
                    'latitude'     => $data['latitude'] ?? '',
                    'longitude'    => $data['longitude'] ?? '',
                    'timezone'     => $data['timezone'] ?? '',
                    'isp'          => $data['org'] ?? '',
                    'org'          => $data['org'] ?? '',
                    'asn'          => $data['asn'] ?? '',
                ];

            case 'ipgeolocation':
                return [
                    'country'      => $data['country_name'] ?? '',
                    'country_code' => strtolower($data['country_code2'] ?? ''),
                    'region'       => $data['state_prov'] ?? '',
                    'city'         => $data['city'] ?? '',
                    'postal'       => $data['zipcode'] ?? '',
                    'latitude'     => $data['latitude'] ?? '',
                    'longitude'    => $data['longitude'] ?? '',
                    'timezone'     => $data['time_zone']['name'] ?? '',
                    'isp'          => $data['isp'] ?? '',
                    'org'          => $data['organization'] ?? '',
                    'asn'          => $data['asn'] ?? '',
                ];

            case 'abstractapi':
                return [
                    'country'      => $data['country'] ?? '',
                    'country_code' => strtolower($data['country_code'] ?? ''),
                    'region'       => $data['region'] ?? '',
                    'city'         => $data['city'] ?? '',
                    'postal'       => $data['postal_code'] ?? '',
                    'latitude'     => $data['latitude'] ?? '',
                    'longitude'    => $data['longitude'] ?? '',
                    'timezone'     => $data['timezone']['name'] ?? '',
                    'isp'          => $data['connection']['isp_name'] ?? '',
                    'org'          => $data['connection']['organization_name'] ?? '',
                    'asn'          => $data['connection']['autonomous_system_number'] ?? '',
                ];
        }

        return [];
    }
}
