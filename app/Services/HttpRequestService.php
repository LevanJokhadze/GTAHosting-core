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

    $response = Http::withHeaders([
        'x-api-key' => $token,
        'content-type' => 'application/json',
        'Accept' => 'application/json',
    ])->post($url, $data);

    if ($response->successful()) {
        return $response->json();
    }

    return [
        'error' => 'Failed to start server',
        'status' => $response->status(),
        'body' => $response->body()
    ];
}
    public function stopServer(string $serverId, string $token)
{
    $url = 'http://165.22.93.250:8080/api/server/stop'; 

    $data = [
        'server_id' => $serverId,
    ];

        $response = Http::withHeaders([
        'x-api-key' => $token,
    ])->post($url, $data);


    if ($response->successful()) {
        return $response->json();
    }

    return [
        'error' => 'Failed to stop server',
        'status' => $response->status(),
        'body' => $response->body()
    ];
}
   public function createServer(string $serverId, string $token)
{
    $url = 'http://165.22.93.250:8080/api/server/create'; 

    $data = [
        'server_id' => $serverId,
    ];

        $response = Http::withHeaders([
        'x-api-key' => $token,
    ])->post($url, $data);


    if ($response->successful()) {
        return $response->json();
    }

    return [
        'error' => 'Failed to stop server',
        'status' => $response->status(),
        'body' => $response->body()
    ];
}

   public function getServerLogs(string $serverId, string $token)
{
    $url = 'http://165.22.93.250:8080/api/server/'.$serverId.'/logs'; 

    $response = Http::withHeaders([
        'x-api-key' => $token,
        'content-type' => 'application/json',
        'Accept' => 'application/json',
    ])->get($url);


    if ($response->successful()) {
        return $response->json();
    }

    return [
        'error' => 'Failed to stop server',
        'status' => $response->status(),
        'body' => $response->body()
    ];
}

   public function getServerStatus(string $serverId, string $token)
{
    $url = 'http://165.22.93.250:8080/api/server/'.$serverId.'/status'; 

    $response = Http::withHeaders([
        'x-api-key' => $token,
        'content-type' => 'application/json',
        'Accept' => 'application/json',
    ])->get($url);


    if ($response->successful()) {
        return $response->json();
    }

    return [
        'error' => 'Failed to stop server',
        'status' => $response->status(),
        'body' => $response->body()
    ];
}
    }
