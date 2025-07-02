<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HttpRequestService
{
    public function startServer(string $serverId, string $token)
{
    $url = 'http://165.22.93.250:8080/api/server/start'; 

    $data = [
        'server_id' => $serverId,
    ];

    $response = Http::withToken($token)->post($url, $data);

    if ($response->successful()) {
        return $response->json();
    }

    return [
        'error' => 'Failed to start server',
        'status' => $response->status(),
        'body' => $response->body()
    ];
}

    }
