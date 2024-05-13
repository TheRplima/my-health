<?php

namespace App\Http\Controllers;

use Asantibanez\LaravelSubscribableNotifications\NotificationSubscriptionManager;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use Carbon\Carbon;

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
        if ($request['image']) {
            $extension  = explode(':', substr($request['image'], 0, strpos($request['image'], ';')));
            $extension = explode('/', $extension[count($extension) - 1])[1];
            $format = $extension == 'jpeg' ? 'jpg' : $extension;
            $name = md5($request['name'] . Carbon::now()) . '.' . $format;
            $filePath = 'images/users/' . $name;
            $image = str_replace(' ', '+', str_replace(substr($request['image'], 0, strpos($request['image'], ',') + 1), '', $request['image']));
            Storage::disk('public')->put($filePath, base64_decode($image), 'public');
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
        } else {
            $user = User::findOrFail($id);
        }

        return (new UserResource($user))->response();
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        if ($request['image']) {
            Storage::disk('public')->delete($user->image);
            $extension  = explode(':', substr($request['image'], 0, strpos($request['image'], ';')));
            $extension = explode('/', $extension[count($extension) - 1])[1];
            $format = $extension == 'jpeg' ? 'jpg' : $extension;
            $name = md5($user->name . Carbon::now()) . '.' . $format;
            $filePath = 'images/users/' . $name;
            $image = str_replace(' ', '+', str_replace(substr($request['image'], 0, strpos($request['image'], ',') + 1), '', $request['image']));
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

    //fuction to generate telegram deeplink to subscribe user to receive notify via telegram
    public function generateTelegramDeeplink(User $user): JsonResponse
    {
        $user->telegram_user_deeplink = Uuid::uuid4();
        $user->save();

        $deeplink = 'https://t.me/' . config('services.telegram-bot-api.bot_name') . '?start=' . $user->telegram_user_deeplink;

        return response()->json([
            'status' => 'success',
            'message' => 'Telegram deeplink generated successfully',
            'deeplink' => $deeplink,
        ]);
    }

    public function getNotificationChannelsList(): JsonResponse
    {
        $subscribeManagement = new NotificationSubscriptionManager();
        $channels = $subscribeManagement->subscribableNotifications();

        $list = [];
        foreach ($channels as $channel) {
            $list[] = [
                'type' => explode('\\', $channel)[count(explode('\\', $channel)) - 1],
                'description' => $channel::subscribableNotificationTypeDescription(),
            ];
        }

        return response()->json([
            'status' => 'success',
            'channels' => $list,
        ]);
    }

    public function subscribeNotificationChannel(Request $request, User $user, $channel): JsonResponse
    {
        //check if channel contains App\\Notifications\\, if not add it
        if (!str_contains($channel, 'App\\Notifications\\')) {
            $channel = 'App\\Notifications\\' . $channel;
        }
        $subscribeManagement = new NotificationSubscriptionManager();
        $subscribeManagement->subscribe($user, $channel);

        return response()->json([
            'status' => 'success',
            'message' => 'User subscribed to ' . $channel,
        ]);
    }

    public function unsubscribeNotificationChannel(User $user, $channel): JsonResponse
    {
        //check if channel contains App\\Notifications\\, if not add it
        if (!str_contains($channel, 'App\\Notifications\\')) {
            $channel = 'App\\Notifications\\' . $channel;
        }
        $subscribeManagement = new NotificationSubscriptionManager();
        $subscribeManagement->unsubscribe($user, $channel);

        return response()->json([
            'status' => 'success',
            'message' => 'User unsubscribed from ' . $channel,
        ]);
    }
}
