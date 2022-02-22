<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use App\Helpers\Utility;
use DB;
use Log;

class AuthController extends Controller {

    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt) {
        $this->jwt = $jwt;
    }

    public function register(Request $request, $data = []) {
        try {
            DB::beginTransaction();
            if (!count($data)) {
                $validator = Validator::make($request->all(), [
                            'fname' => 'required|min:3|max:50',
                            'lname' => 'required|min:3|max:50',
                            'email' => 'required|email|max:100',
                            'password' => 'required',
                            'mobile' => 'required|min:10|max:10',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    DB::rollBack();
                    return Utility::genErrResp("validator_error", $errors);
                }
            }

            if (!count($data)) {
                DB::rollBack();
                return Utility::genErrResp("user_registration_not_allowed");
            }
            if (!count($data))
                $data = $request->all();

            $data['email'] = strtolower($data['email']);
            $exist = User::where('email', $data['email'])
                            ->where('status', 'A')->count();
            if ($exist > 0) {
                DB::rollBack();
                return Utility::genErrResp("user_already_exist");
            }
            $exist = User::where('email', $data['email'])
                    ->where('status', '!=', 'E');
            if ($exist->count() > 0) {
                $user = User::find($exist->get()[0]->id);
            } else {
                $user = new User();
            }
            $user->email = $data['email'];
            $user->password = app('hash')->make($data['password']); //bcrypt();
            $user->mobile = $data['mobile'];
            $user->fname = $data['fname'];
            $user->lname = $data['lname'];
            $user->status = 'A';
            $user->save();
            DB::commit();
            return Utility::genSuccessResp('register_success', $user, false);
        } catch (Exception $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("internal_err");
        }
    }

    public function login(Request $request) {
        try {
            DB::beginTransaction();
            $authData['email'] = strtolower($request->get('email'));
            $authData['password'] = $request->get('password');
            $authData['status'] = 'A';
            if (!$token = $this->jwt->attempt($authData)) {
                DB::rollBack();
                return Utility::genErrResp("user_name_or_password_not_match");
            }
            $user_id = User::where('email', $authData['email'])->get()[0]->id;
            $user = User::find($user_id);
            $user->token = $token;
            $user->save();

            $return_data = ['app_token' => $token];
            $return_data['user'] = strtolower($request->get('email'));

            DB::commit();
            return Utility::genSuccessResp('login_success', $return_data, false);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("token_expired");
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("token_invalid");
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("token_absent");
        } catch (Exception $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("internal_err");
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        $token = $this->jwt->getToken();
        if ($token) {
            $this->jwt->setToken($token)->invalidate();
        }
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

}
