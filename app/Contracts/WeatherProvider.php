<?php

namespace App\Contracts;

interface WeatherProvider
{
    /**
     * Get current weather for a location
     *
     * @param string|null $location
     * @return array|null
     */
    public function getCurrentWeather(?string $location): ?array;

    /**
     * Get weather forecast for a location
     *
     * @param string|null $location
     * @return array|null
     */
    public function getForecast(?string $location): ?array;

    /**
     * Get provider specific alerts if any
     *
     * @param string|null $location
     * @return array
     */
    public function getAlerts(?string $location): array;

    /**
     * Get marine weather data if available
     *
     * @param string|null $location
     * @return array|null
     */
    public function getMarineWeather(?string $location): ?array;

    /**
     * Validate if the provider supports this location
     *
     * @param string $location
     * @return bool
     */
    public function supportsLocation(string $location): bool;
} 