<?php

require_once BASEPATH . 'lib/LIB_http.php';
require_once BASEPATH . 'core/func/get_client_ip.php';


function GetLocationByIP($ip = null)
{
    $ip = $ip ?: GetClientIP();
    return Env('DEBUG_MODE') ? _GetLocationByIPMockup($ip) : _GetLocationByIP($ip);
}

function _GetLocationByIPMockup($ip)
{
    return [
        'status'      => 'success',
        'country'     => 'Россия',
        'countryCode' => 'RU',
        'region'      => 'KGD',
        'regionName'  => 'Калининградская область',
        'city'        => 'Калининград',
        'zip'         => '236006',
        'lat'         => 54.705,
        'lon'         => 20.5105,
        'timezone'    => 'Europe/Kaliningrad',
        'isp'         => 'PJSC "Rostelecom" North-West region',
        'org'         => 'OJSC North-West Telecom',
        'as'          => 'AS12389 PJSC Rostelecom',
        'query'       => '95.52.118.100',
    ];
}

function _GetLocationByIP($ip)
{
    $ret                      = array_map(
        fn($v) => is_string($v) ? '' : null,
        _GetLocationByIPMockup($ip)
    );
    $GLOBALS['_lastGeoError'] = null;

    try {
        $cacheKey = md5(__FUNCTION__ . $ip);
        if (MemoryHas($cacheKey)) {
            $ipInfo = MemoryGet($cacheKey);
        } else {
            $ipInfo = file_get_contents("http://ip-api.com/json/{$ip}?lang=" . LANG);
            $ipInfo = json_decode($ipInfo, true);
            MemorySet($cacheKey, $ipInfo);
        }
        $ret = isset($ipInfo['status']) && $ipInfo['status'] !== 'fail' ? $ipInfo : [];
    } catch (\Throwable $e) {
        $GLOBALS['_lastGeoError'] = $e;
    }
    return $ret;
}

function GetCountries()
{
    $json = FileSys::readFile(BASEPATH . "i/data/countries.json");
    return json_decode($json, true);
}

function GetCountryByCode($code)
{
    return GetCountries()[$code] ?? null;
}

function GetCoordinatesByAddress($address)
{
    $ret                      = ['lat' => 0, 'lon' => 0];
    $GLOBALS['_lastGeoError'] = null;

    try {
        $response = http_get('https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address), "https://nominatim.openstreetmap.org/");
        if (isset($response['FILE'])) {
            $response = $response['FILE'];
            $response = json_decode($response, true);
            $ret      = [
                'lat' => $response[0]['lat'] ?? 0,
                'lon' => $response[0]['lon'] ?? 0,
            ];
        }
    } catch (\BadMethodCallException $e) {
        $GLOBALS['_lastGeoError'] = $e;
    }
    return $ret;
}

function LastGeoError()
{
    return $GLOBALS['_lastGeoError'] ?? null;
}
