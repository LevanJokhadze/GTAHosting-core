<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Servers;
use Illuminate\Http\JsonResponse;
use App\Models\UserServerStatus;
use App\Models\Ports;
use App\Models\Server_config;
use App\Services\ConfigurationService;
use App\Services\HttpRequestService; 
class ServersController extends Controller
{
public function store(Request $request, HttpRequestService $apiService, ConfigurationService $confService):JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'node' => 'required|string|max:255',
            'server' => 'required|string|max:255',
            'playerCount' => 'required|integer',
            'csharpEnabled' => 'required|bool'
        ]);

        $device = Servers::create($validated);
        UserServerStatus::create([
        'user_id' => auth()->id(), 
        'server_id' => $device->id,
        'server_name' => $device->name,
         'is_active' => false,
    ]);
    $ip = "165.22.93.250";
    $maxPort = Ports::where('server_ip', $ip)
    ->max('port');

    if ($maxPort < 22003)
    {
        $maxPort = 22003;
    }

    // Ports table
    $name = $request->name;
    $newPort = $maxPort+= 2;

    $serverDaemon = $apiService->createServer($device->name, "oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ");



    // Create Port

    $port = Ports::create([
        "server_name" => $name,
        "server_ip" => $ip,
        "port" => $newPort
    ]);

    $payload = [
        "maxplayers" => $request->playerCount,
        "name" => $request->name,
        "gamemode" => "freeroam",
        "stream-distance" => 500.0,
        "announce" => false,
        "csharp" => false,
        "port" => $newPort,
        "voice-chat" => false,
        "voice-chat-sample-rate" => 48000,
        "bind" => "0.0.0.0"
    ];

    // Create Config
    $conf = Server_config::create($payload);

    $confAnswer = $confService->updateConfig("oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ", $request->name, $payload);


        return response()->json([
            "success" => true,
            "message"=> $device,
            "daemon"=> $serverDaemon,
            "port" => $port,
            "configuration" => $confAnswer
        ],
             201);
    }
    public function index(): JsonResponse
    {
        $devices = Servers::all();
        return response()->json($devices);
    }


    public function update(Request $request, $id): JsonResponse
    {
        $device = Servers::findOrFail($id);
if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'node' => 'required|string|max:255',
            'server' => 'required|string|max:255',
            'playerCount' => 'required|integer',
            'csharpEnabled' => 'required|bool'
        ]);

        $device->update($validated);

        return response()->json($device, 200);
    }

    public function show($id): JsonResponse
    {
        $device = Servers::where("name", $id)->first();
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        return response()->json($device);
    }
    public function destroy($id): JsonResponse
    {
        $device = Servers::findOrFail($id);
        $device->delete();
        return response()->json(
            ['message' => 'Device deleted successfully']
        );
    }



public function start(Request $request, HttpRequestService $httpRequestService)
{
    $token = $request->bearerToken();
    $serverId = $request->input('server_id');

    $response = $httpRequestService->startServer($serverId, "oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ");

    return response()->json($response);
}

public function setConfig(Request $request, $name, HttpRequestService $httpRequestService)
{
    $user = $request->user();
    $server = UserServerStatus::where('server_name', $name)->firstOrFail();

    if ($user->id === $server->user_id)
    {
        $config = $request->config;
        $response = $httpRequestService->setConf($name, $config, "oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ");

        return response()->json($response);
    }

    return response()->json(
        [   
            'status' => "error",
            'message' => 'You do not have permission to access this server'
        ]
    );
}
}