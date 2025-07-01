<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Http\JsonResponse;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Requests\LoginRequest;
    use Illuminate\Support\Str;
    use App\Http\Requests\RegisterRequest;
    class AuthController extends Controller
    {
        public function login(LoginRequest $request): JsonResponse
    {

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        
        $token = $user->createToken($user->name, ['Auth-Token'])->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'token_type' => 'Bearer', 
            'token' => $token,
            "user" => $user
        ], 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            $token = $user->createToken($user->name, ['Auth-Token'])->plainTextToken;        
            return response()->json([
                'message' => 'Registration successful',
                'token_type' => 'Bearer',
                'token' => $token,
                
            ], 201);
        } else {
            return response()->json(['message' => 'User not created'], 500);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $tasks = $user->tasks()->get();
            return response()->json([
                'message' => 'Profile fetched successfully',
                'data' => $user,
                'tasks' => $tasks
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not Authenticated'
            ], 401);
        }
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 200);
        }

        return response()->json([
            'message' => 'User not found'
        ], 400);
    }
    }