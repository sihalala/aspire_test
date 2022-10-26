<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
   
class AuthController extends BaseController
{
    public function login(LoginRequest $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user();
            $user->token =  $user->createToken('TestAspire')->plainTextToken;

            return $this->sendResponse($user, 'User Logged in');
        } 
        else{ 
            return $this->sendError('Username or password did not match', [],401);
        } 
    }
    public function register(RegisterRequest $request)
    {
        $request->request->add(['password' => bcrypt($request->password)]);

        $user = User::create($request->all());
        $user->token =  $user->createToken('TestAspire')->plainTextToken;
   
        return $this->sendResponse($user, 'User has been created');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse('User Logout successfully.', 200);
    }
   
}