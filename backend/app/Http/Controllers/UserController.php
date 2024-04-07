<?php

namespace App\Http\Controllers;

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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUser $request)
    {
        try{
            $user = $this->user
            ->create($request->all());
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.store',null,$e);
        }

        return new UserResource($user,array('type' => 'store','route' => 'users.store'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
