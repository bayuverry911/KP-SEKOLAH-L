<?php

namespace App\Support;

class Geofence
{
    /**
     * Jarak dua titik koordinat dalam meter (rumus Haversine).
     */
    public static function jarakMeter(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $radiusBumi = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $radiusBumi * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
