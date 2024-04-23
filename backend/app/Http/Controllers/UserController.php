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
use Carbon\Carbon;
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
        unset($user['password']);
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

    public function show($id = null): JsonResponse
    {
        if (!$id) {
            $user = Auth::user();
        }else{
            $user = User::findOrFail($id);
        }

        return (new UserResource($user))->response();
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        if ($request['image']) {
            Storage::disk('public')->delete($user->image);
            $extension  =explode(':', substr($request['image'], 0, strpos($request['image'], ';')));
            $extension = explode('/', $extension[count($extension)-1])[1];
            $format = $extension == 'jpeg' ? 'jpg' : $extension;
            $name = md5($user->name.Carbon::now()).'.'.$format;
            $filePath = 'images/users/'.$name;
            $image = str_replace(' ', '+', str_replace(substr($request['image'], 0, strpos($request['image'], ',')+1), '', $request['image']));
            Storage::disk('public')->put($filePath, base64_decode($image), 'public');
            $data['image'] = $filePath;
        }
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
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
