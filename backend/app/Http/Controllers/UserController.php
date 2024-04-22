<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index(): JsonResponse
    {
        $users = User::paginate();
        return (new UserCollection($users))->response();
    }

    public function store(RegisterUserRequest $request): JsonResponse
    {

        $data = $request->validated();
        if ($request->hasFile('image')) {
            $filePath = Storage::disk('public')->put('images/users', request()->file('image'));
            $data['image'] = $filePath;
        }

        $user = User::create(UserResource::make($data)->toArray($request));

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], Response::HTTP_CREATED);
    }

    public function show(User $user): JsonResponse
    {
        return (new UserResource($user))->response();
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // delete image
            Storage::disk('public')->delete($user->image);

            $filePath = Storage::disk('public')->put('images/users', request()->file('image'), 'public');
            $data['image'] = $filePath;
        }

        $user->fill($data);
        $user->save();

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => (new UserResource($user))->toArray($request),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], Response::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
            'user' => $user,
        ]);
    }
}
