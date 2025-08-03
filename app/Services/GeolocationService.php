<?php

namespace App\Services;

class GeolocationService
{
    /**
     * Calcular la distancia entre dos puntos usando la fórmula de Haversine
     *
     * @param float $lat1 Latitud del primer punto
     * @param float $lon1 Longitud del primer punto
     * @param float $lat2 Latitud del segundo punto
     * @param float $lon2 Longitud del segundo punto
     * @return float Distancia en metros
     */

    public function calculateDistance($lat1, $lon1, $lat2, $lon2){
        // Radio de la Tierra en metros
        $earthRadius = 6371000;

        // Convertir grados a radianes
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Diferencias
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        // Fórmula de Haversine
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distancia en metros
        $distance = $earthRadius * $c;

        return $distance;
    }

    /**
     * Verificar si un punto está dentro del radio permitido
     *
     * @param float $userLat Latitud del usuario
     * @param float $userLon Longitud del usuario
     * @param float $eventLat Latitud del evento
     * @param float $eventLon Longitud del evento
     * @param int $allowedRadius Radio permitido en metros
     * @return bool
     */
    public function isWithinRadius($userLat, $userLon, $eventLat, $eventLon, $allowedRadius)
    {
        $distance = $this->calculateDistance($userLat, $userLon, $eventLat, $eventLon);
        return $distance <= $allowedRadius;
    }

    /**
     * Validar coordenadas GPS
     *
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public function validateCoordinates($latitude, $longitude)
    {
        return $latitude >= -90 && $latitude <= 90 &&
               $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Formatear coordenadas para mostrar
     *
     * @param float $latitude
     * @param float $longitude
     * @return string
     */
    public function formatCoordinates($latitude, $longitude)
    {
        return sprintf("%.6f, %.6f", $latitude, $longitude);
    }

}
