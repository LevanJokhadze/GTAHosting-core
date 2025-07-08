<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ConfigurationService
{
public ?string $lastError = null;

public function updateConfig(string $apiKey, string $serverSlug, array $daemonConfigPayload): bool
{
    // Clear the last error on a new attempt
    $this->lastError = null;

    try {
        $response = Http::withHeaders([
            'X-Api-Key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->timeout(15) // Good practice: add a timeout
        ->put(
            // Use the provided server slug (name)
            "http://165.22.93.250:8080/api/server/{$serverSlug}/config",
            $daemonConfigPayload
        );

        // Check for client or server errors (4xx or 5xx status codes)
        if ($response->failed()) {
            // Store the error message from the daemon's JSON response
            $this->lastError = $response->json('error', 'An unknown error occurred on the daemon.');
            
            // Log the detailed error for debugging
            \Log::error("Daemon config update failed for server {$serverSlug}: " . $response->body());
            
            return false; // Indicate failure
        }

        // If we get here, the response was successful (2xx status code)
        return true; // Indicate success

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        // This catches network errors, like the daemon being offline or a timeout
        $this->lastError = "Could not connect to the game server node. It may be offline.";
        \Log::error("ConnectionException during daemon config update for server {$serverSlug}: " . $e->getMessage());
        return false;
    } catch (\Exception $e) {
        // Catch any other unexpected exceptions
        $this->lastError = "An unexpected error occurred while communicating with the node.";
        \Log::error("Generic exception during daemon config update for server {$serverSlug}: " . $e->getMessage());
        return false;
    }
}
}
