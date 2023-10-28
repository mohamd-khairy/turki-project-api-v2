<?php

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

if (!function_exists('Paginator')) {
    /**
     * array manually pagination helper.
     *
     * @param Request $request
     * @param int $perPage
     * @param array $array
     * @return LengthAwarePaginator
     */
    function Paginator(Request $request, $array = [])
    {
        $page = isset($request->page) ? $request->page : 1; // Get the page=1 from the url
        $perPage = isset($request->per_page) ? $request->per_page : 6; // Number of items per page
        $offset = ($page * $perPage) - $perPage;

        $entries = new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true),
            count($array), // Total items
            $perPage, // Items per page
            $page, // Current page
            // this can keep all old query parameters from the url
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return $entries;
    }
}

if (!function_exists('PerPage')) {
    /**
     * array manually pagination helper.
     *
     * @param Request $request
     * @param int $perPage
     * @param array $array
     * @return LengthAwarePaginator
     */
    function PerPage(Request $request)
    {
        $perPage = 6;
        if ($request->has('per_page'))
            $perPage = $request->get('per_page');

        if ($perPage == 0)
            $perPage = 6;
        return $perPage;
    }
}

if (!function_exists('GetNextOrderRefNo')) {
    /**
     * @return string
     */
    function GetNextOrderRefNo($countryCode, $id)
    {
        $genId = str_pad($id, 9, "0", STR_PAD_LEFT);
        return $countryCode . 'O'. $genId;
    }
}

if (!function_exists('GetNextPaymentRefNo')) {
    /**
     * @return string
     */
    function GetNextPaymentRefNo($countryCode, $id)
    {
        $genId = str_pad($id, 9, "0", STR_PAD_LEFT);
        return $countryCode . 'P' . $genId;
    }
}


if (!function_exists('trackingIdGenerator')) {
    /**
     * trackingId Generator.
     * @param $validated
     * @return string
     */
    function trackingIdGenerator($validated)
    {
        $t = Carbon::now()->unix();
        $t3 = $validated->id . ($t / $validated->receiver_phone);
        return floor($t3 * 100000);
    }
}

if (!function_exists('OTPGenerator')) {
    /**
     * OTP Generator.
     * @return string
     * @throws Exception
     */
    function OTPGenerator()
    {
        return random_int(0000, 9999);
    }
}

if (!function_exists('UIDGenerator')) {
    /**
     * Uniq Generator.
     * @return string
     * @throws Exception
     */
    function UIDGenerator()
    {
        return (string)Str::orderedUuid();
    }
}

if (!function_exists('HaversineGreatCircleDistance')) {
    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    function HaversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}

