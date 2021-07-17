<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\AccessControll;
use Hash;
use Auth;
use DB;

class AdminPublicController extends Controller
{ 
    public function signupAuth(Request $request) {

        $this->validate($request, [
            'email'      => 'required|email|unique:admins,email',
            'password'   => 'required|min:6'
        ]);

         DB::beginTransaction();

        try {

            $admin = new Admin;
            $admin->uuid = (string) Str::uuid();
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password, ['rounds' => 12]);
            $admin->save();

            $accessControll = new AccessControll;
            $accessControll->admin_id = $admin->id;
            $accessControll->access_controll = '{"shop_id":"0","branch_id":"0"}';
            $accessControll->save();

            DB::commit();

            $token  = $admin->createToken('user-token')->plainTextToken;
            return $this->getResponse(200, 'success', 'User created', [
                'token' => $token
            ]);

        } catch(\Exception $e) {

            DB::rollBack();
            // throw $e;
            return $this->getResponse(200, 'success', 'User created', $e->getMessage());
        }
    }
}
