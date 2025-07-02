<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Servers;
use Illuminate\Http\JsonResponse;
use App\Models\UserServerStatus;
use App\Services\HttpRequestService; 
class ServersController extends Controller
{
public function store(Request $request):JsonResponse
    {
        $validated = $request->validate([
            'serverId' => 'required|string|max:255',
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

        return response()->json($device, 201);
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
            'serverId' => 'required|string|max:255',
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
        $device = Servers::where("id", $id)->first();
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

    $response = $httpRequestService->startServer($serverId, $token);

    return response()->json($response);
}

}