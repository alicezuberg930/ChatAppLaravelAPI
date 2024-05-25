<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (empty($user)) {
                return response()->json(["message" => "Email doesn't exist"], 500);
            } else {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
                    Device::firstOrCreate(
                        ['device_id' => $request->device_id],
                        [
                            'user_id' => $user->id,
                            'fcm_id' => $request->fcm_id,
                            'device_id' => $request->device_id,
                            'device_model' => $request->device_model,
                        ]
                    );
                    $device = Device::where('device_id', $request->device_id)->first();
                    if ($device->fcm_id != $request->fcm_id) $device->update(['fcm_id' => $request->fcm_id]);
                    return response()->json([
                        "message" => "Logged in successfully",
                        "bearer_token" => $user->createToken($user->name)->plainTextToken,
                        "data" => $user,
                    ], 200);
                } else {
                    return response()->json(["message" => "Incorrect password"], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $user = new User();
            $user->name = $request->get('fullname');
            $user->email = $request->get('email');
            $user->password = Hash::make($request->password);
            $user->ip_address = $request->get('ip_address');
            if (($request->avatar)) {
                $user->addMedia($request->file('avatar'))->toMediaCollection('avatar');
            }
            if ($user->save() == true) {
                $response["status"] = "success";
                $response["message"] = "User registered successfully";
                $response["data"] = $user;
            } else {
                $response["status"] = "failed";
                $response["message"] = "Unable to create message";
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function logout()
    {
        try {
            $devices = Device::where('user_id', Auth::id())->get();
            foreach ($devices as $device) {
                $device->delete();
            }
            auth()->user()->tokens()->delete();
            return response()->json(["message" => "Logout successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $searchUsers = User::where([['name', 'like', '%' . $request->fullname . '%'], ['id', '!=', Auth::id()]])->get();
            if (!$searchUsers) {
                return response()->json(["message" => "No user found"], 500);
            } else {
                return response()->json(["message" => "User data found", "data" => $searchUsers], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function updateUserAvatar(Request $request)
    {
        try {
            $id = $request->get('id');
            if (empty($id)) {
                $response["status"] = "failed";
                $response["message"] = "Missing user id";
                return response()->jsovn($response);
            }
            $user = User::find($id);
            if ($request->avatar) {
                $user->clearMediaCollection('avatar');
                $user->addMedia($request->file('avatar'))->toMediaCollection('avatar');
            }
            // if ($user->save()) {    
            $response["status"] = "success";
            $response["message"] = "Avatar updated successfully";
            $response["data"] = $user;
            // } else {
            //     $response["status"] = "failed";
            //     $response["message"] = "Unable to update avatar";
            // }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function updateUserStatus(Request $request)
    {
        try {
            $id = $request->get('id');
            if (empty($id)) {
                $response["status"] = "failed";
                $response["message"] = "Missing user id";
                return response()->jsovn($response);
            }
            $user = User::find($id);
            $user->status = $request->get('status');
            if ($user->save() == true) {
                $response["status"] = "success";
                $response["message"] = "Status updated successfully";
                $response["data"] = $user;
            } else {
                $response["status"] = "failed";
                $response["message"] = "Unable to update status";
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function messageOtherUser(Request $request)
    {
    }
}
