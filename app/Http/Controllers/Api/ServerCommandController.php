<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servers;
use App\Models\UserServerStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\HttpRequestService; // Assuming you have a service for HTTP requests

class ServerCommandController extends Controller
{
    public function handle(Request $request, $id, HttpRequestService $apiService): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'command' => 'required|string|in:start,stop',
            ]);

            // Find the server
            $server = Servers::where('name', $id)->firstOrFail();
            $user = Auth::user(); // or $request->user()
            // Determine if server should be active
            $isActive = $validated['command'] === 'start';

            // Update or create the user server status
            $userServerStatus = UserServerStatus::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'server_id' => $server->id
                ],
                [
                    'server_name' => $server->name,
                    'is_active' => $isActive,
                ]
            );

            $staticToken = "oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ";

            if ($isActive) {
                // If starting the server, call the API to start it
                $response = $apiService->startServer($server->name, $staticToken);
            } else {
                // If stopping the server, call the API to stop it
                $response = $apiService->stopServer($server->name, $staticToken);
            }

            // Log for debugging
            Log::info('Server status updated', [
                'user_id' => $user->id,
                'server_id' => $server->id,
                'command' => $validated['command'],
                'is_active' => $isActive,
                'updated_record' => $userServerStatus->toArray()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Server command executed successfully",
                "daemon_response" => $response,
                'data' => [
                    'server_id' => $server->id,
                    'server_name' => $server->name,
                    'is_active' => $userServerStatus->is_active,
                    'status' => $isActive ? 'started' : 'stopped'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Server command failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'server_id' => $id,
                'command' => $request->input('command')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to execute server command',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method to get current server status
    public function getStatus(Request $request, $name, HttpRequestService $apiService): JsonResponse
    {
        try {
            $server = Servers::where('name', $name)->firstOrFail();
            $user = Auth::user();
            $staticToken = "oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ";


            $userServerStatus = UserServerStatus::where('user_id', $user->id)   
                ->where('server_name', $server->name)
                ->first();
            
            $daemonResponse = $apiService->getServerStatus($name, $staticToken);
            

            return response()->json([
                'success' => true,
                'data' => [
                    'server_id' => $server->id,
                    'server_name' => $server->name,
                    'is_active' => $userServerStatus ? $userServerStatus->is_active : false,
                    'status' => $userServerStatus && $userServerStatus->is_active ? 'started' : 'stopped'
                ],
                "daemon" => $daemonResponse
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get server status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

        public function getLogs(Request $request, $name, HttpRequestService $apiService): JsonResponse
    {
        try {
            $server = Servers::where('name', $name)->firstOrFail();
            $user = Auth::user();
            $staticToken = "oKHLuxNXRmDeBYsZhmikSLxKUcGNhgqZ";


            $userServerStatus = UserServerStatus::where('user_id', $user->id)   
                ->where('server_name', $server->name)
                ->first();

            $daemonResponse = $apiService->getServerLogs($name, $staticToken);
            
            return response()->json([
                'success' => true,
                'data' => $daemonResponse
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get server status',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getUserServers(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $userServers = UserServerStatus::with('server')
                ->where('user_id', $user->id) 
                ->get()
                ->map(function ($userServer) {
                    return [
                        'server_id' => $userServer->server_id,
                        'server_name' => $userServer->server_name,
                        'is_active' => $userServer->is_active,
                        'status' => $userServer->is_active ? 'started' : 'stopped'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $userServers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user servers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getUserServerById(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $userServer = UserServerStatus::with('server')
                ->where('user_id', $user->id)
                ->where('server_name', $id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'server_id' => $userServer->server_id,
                    'server_name' => $userServer->server_name,
                    'is_active' => $userServer->is_active,
                    'status' => $userServer->is_active ? 'started' : 'stopped'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCommand(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $userServerStatus = UserServerStatus::where('user_id', $user->id)
                ->where('server_name', $id)
                ->firstOrFail();

            // Delete the user server status
            $userServerStatus->delete();

            return response()->json([
                'success' => true,
                'message' => 'Server command deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete server command',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}