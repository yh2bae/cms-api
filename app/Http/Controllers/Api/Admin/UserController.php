<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'User Data Saved Successfully!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User Data Saved Failed!', null);
    }

    public function show($id)
    {
        $user = User::whereId($id)->first();
        
        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'User Data Details!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User Data Details Not Found!', null);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,'.$user->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($request->password == "") {

            //update user without password
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);

        }

        //update user with new password
        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'User Data Updated Successfully!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User Data Updated Failed!', null);
    }

    public function destroy(User $user)
    {
        if($user->delete()) {
            //return success with Api Resource
            return new UserResource(true, 'User Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User Data Deleted Failed!', null);
    }
}
