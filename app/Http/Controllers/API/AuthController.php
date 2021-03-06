<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends BaseController

{

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['renewal_token'] = "abcxx";
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        Log::info("Login: ".json_encode($request->input()));
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            $user = $user->toArray();
            $user['access_token'] = $token;
            $user['renewal_token'] = "abcxx";

            return $this->sendResponse(new UserResource((object)$user), 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
