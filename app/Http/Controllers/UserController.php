<?php
namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\JWTManager as JWT;
class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return response()->json($users);
    }
    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        return response()->json($user, 201);
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user, 200);
    }
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }
    public function register(Request $request){
        $validator = Validator::make($request->json()->all() , [
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'id_rol' => 'required', 
        ]);
        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'nombre' => $request->json()->get('nombre'),
            'email' => $request->json()->get('email'),
            'password' => Hash::make($request->json()->get('password')),
            'id_rol' => $request->json()->get('id_rol'),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }
    public function login(Request $request)
    {
        $credentials = $request->json()->all();
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json( compact('token') );
    }
    
    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }
}