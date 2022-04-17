<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Log;
use App\Http\Controllers\Constants;
use Tymon\JWTAuth\JWTAuth;
use Exception;
use App\Helpers\Utility;
use App\Models\User;
use App\Http\Controllers\Control\AccessController;
//use Illuminate\Support\Facades\Cache;

class JwtUserAuth {

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $jwt;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(JWTAuth $jwt) {
        $this->jwt = $jwt;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        try {
            $urlArr = explode('/', $request->url());
            if (in_array('login', $urlArr) || in_array('register', $urlArr)) {
                return $next($request);
            }
            Utility::log('ppppp',config('user_id'),'----',$request->all());
            $this->jwt->parser()->setRequest($request);
            if (!$user = $this->jwt->parseToken()->authenticate()) {
                return response(Utility::genErrResp('token_expired'));
            }
            config(['user_id' => $user->id]);
            $user = User::find($user->id);

            config([
                'config.error_config.user' => $user->fname . ' ' . $user->lname,
                'config.error_config.user_id' => config('id'),
                'config.error_config.environment' => env('APP_ENV')
            ]);
            $token = $this->jwt->getToken();

            $user_id = $request->get('id');

            return $next($request);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $ex) {
            Utility::logException('token_expired');
            return response(Utility::genErrResp('token_expired'));
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $ex) {
            Utility::logException('token_expired');
            return response(Utility::genErrResp('token_expired'));
        } catch (Exception $ex) {
            Utility::logException($ex);
            return response(Utility::genErrResp('token_expired'));
        }
    }

}
