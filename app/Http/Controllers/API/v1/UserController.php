<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $userData = User::where('email', $request->get('email'))->first();
            if (empty($userData)) {
                $response["status"] = "failed";
                $response["message"] = "Email doesn't exist";
            } else {
                if ($userData->password != $request->get('password')) {
                    $response["status"] = "failed";
                    $response["message"] = "Incorrect password";
                } else {
                    $response["status"] = "success";
                    $response["message"] = "Logged in successfully";
                    $response["data"] = $userData;
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
            $user->password = $request->get('password');
            $avatar = $request->file('avatar');
            if (!empty($avatar)) {
                $extenstion = $avatar->getClientOriginalExtension();
                $filename = 'avatar_' .  time() . '.' . $extenstion;
                $avatar->move(public_path('assets/images/avatar/'), $filename);
                $user->avatar = asset('assets/images/avatar/') . '/' . $filename;
            } else {
                $user->avatar = asset('assets/images/avatar/') . '/default_avatar.png';
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
            $avatar = $request->file('avatar');
            $extenstion = $avatar->getClientOriginalExtension();
            $filename = 'avatar_' .  time() . '.' . $extenstion;
            $avatar->move(public_path('assets/images/avatar/'), $filename);
            $user->avatar = asset('assets/images/avatar/') . '/' . $filename;
            if ($user->save() == true) {
                $response["status"] = "success";
                $response["message"] = "Avatar updated successfully";
                $response["data"] = $user;
            } else {
                $response["status"] = "failed";
                $response["message"] = "Unable to update avatar";
            }
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
}
