<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\UpdateRequest;
use App\Models\Access\User\User;
use App\Repositories\Frontend\Access\User\UserRepository;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller
{
    use Helpers;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     *  API Login, on success return JWT Auth token
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('name', 'password');
        $credentials['name'] = strtolower($credentials['name']);
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'code' => 'invalid_credentials',
                    'status_code' => 401,
                    'message' => 'Invalid Credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'code' => 'could_not_create_token',
                'status_code' => 500,
                'message' => 'Could not create token'
            ], 500);
        }
        $user = User::query()->with('roles')->where('name', '=', $credentials['name'])->first();
        if ($user->hasRole(3)) {
            // all good so return the token
            return response()->json(compact('token'));
        } else {
            return response()->json([
                'code' => 'invalid_credentials',
                'status_code' => 401,
                'message' => 'Your account must be approve by administrator'
            ], 401);
        }
        // all good so return the token
    }

    /**
     *  API Login, on success return JWT Auth token
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function webAuthenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('name', 'password');
        $credentials['name'] = strtolower($credentials['name']);
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'code' => 'invalid_credentials',
                'status_code' => 401,
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $user = User::query()->with('roles')->where('name', '=', $credentials['name'])->first();
        if ($user->hasRoles([1, 2, 4, 5, 6])) {
            // all good so return the token
            return response()->json(compact('token'));
        } else {
            return response()->json([
                'status_code' => 403,
                'message' => 'You do not have permission to login system'
            ], 403);
        }
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     *
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Returns the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticatedUser()
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'code' => 'user_not_found',
                'status_code' => 404,
                'message' => 'User not found'
            ], 404);
        }

        $user->roles;
        $user->department;
        return response()->json($user);
    }

    /**
     * Refresh the token
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getToken()
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return $this->response->errorMethodNotAllowed('Token not provided');
        }

        try {
            $refreshedToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            return $this->response->errorInternal('Not able to refresh Token');
        }

        return $this->response->withArray(['token' => $refreshedToken]);
    }

    public function checkToken()
    {
        return $this->response->noContent(); // Middleware api.auth will handle logic for this method
    }


    /**
     * @param ChangePasswordRequest $request
     *
     * @return mixed
     * @throws \App\Exceptions\GeneralException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->user->changePassword($request->only('old_password', 'password'));

        return response()->json([
            'status_code' => 200,
            'message' => trans('strings.frontend.user.password_updated')
        ]);
    }
}