<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ConfigurationService
{
  public function updateConfig($apiKey, $name, $daemonConfigPayload)
  {

        $response = Http::withHeaders([
        'X-Api-Key' => $apiKey,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->put(
        "http://165.22.93.250:8080/api/server/{$name}/config",
        $daemonConfigPayload
    );

        if ($response->successful()) {
        // Optionally, you could add a button "Restart now to apply changes"
        return back()->with('success', 'Configuration saved! Please restart your server to apply changes.');
    } else {
        // Log the error and inform the user
        \Log::error("Daemon config update failed for server {$name}: " . $response->body());
        return back()->with('error', 'Failed to save configuration to the game server. Please contact support. Error: ' . $response->json('error', 'Unknown error'));
    }
  }
}
