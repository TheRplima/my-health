<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Transformers\User\UserResourceCollection;
use App\Transformers\User\UserResource;
use App\Http\Requests\User\StoreUser;
use Illuminate\Support\Facades\Hash;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUser $request)
    {
        try {
            $user = $this->user
                ->create($request->all());
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.store', null, $e);
        }

        return new UserResource($user, array('type' => 'store', 'route' => 'users.store'));
    }

    /**
     * Login the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = $this->user
                ->login($credentials);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.login', null, $e);
        }

        return response()->json(compact('token'));
    }

    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $this->user
                ->logout($request->input('token'));
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.logout', null, $e);
        }

        return response(['status' => true, 'msg' => 'Deslogado com sucesso'], 200);
    }
}
