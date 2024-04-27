<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
            $user = User::where('email', $request->get('email'))->first();
            if (empty($user)) {
                $response["status"] = "failed";
                $response["message"] = "Email doesn't exist";
            } else {
                if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
                    $response["status"] = "success";
                    $response["message"] = "Logged in successfully";
                    $response["data"] = $user;
                    $response["bearer_token"] = $user->createToken($user->name)->plainTextToken;
                } else {
                    $response["status"] = "failed";
                    $response["message"] = "Incorrect password";
                }
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
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

    public function searchUser(Request $request)
    {
        try {
            $searchUsers = User::where('name', 'like', '%' . $request->get('fullname') . '%')->get();
            if (!count($searchUsers)) {
                $response["status"] = "failed";
                $response["message"] = "no data found";
            } else {
                $response["status"] = "success";
                $response["message"] = "data fetched successfully";
                $response["data"] = $searchUsers;
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
