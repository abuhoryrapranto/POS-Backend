<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AccessControll;
use App\Models\Currency;
use App\Models\Country;
use App\Models\Timezone;
use App\Models\Branch;
use App\Models\Admin;
use Auth;
use Hash;
use DB;

class AdminAuthController extends Controller
{
    public function checkShopOpen() {
        if($this->get_my_shop_id() == 0 || $this->get_my_shop_id() == null) {
            return $this->getResponse(200, 'success', 'shop open page', ['shop' => 'open']);
        }

        $accessControll = AccessControll::where('admin_id', Auth::user()->id)->first();
        $accessControll = json_decode($accessControll->access_controll, true);
        $branch_data = explode(',', $accessControll['branch_id']);
        $totalBranch = count(explode(',', $accessControll['branch_id']));

        if(Auth::user()->is_super == 1 && $totalBranch > 1 && !$request->session()->has('branch_id')) {
            return $this->getResponse(200, 'success', 'shop open page', ['shop' => 'chooseBranch']);
        }
        
        return $this->getResponse(200, 'success', 'shop open page', ['shop' => 'access']);
    }

    public function getAllCountry() {
        $countries = Country::where('status', 1)->get();
        if($countries->isEmpty())
            return $this->getResponse(404, 'failed', 'Countries not found!', null);
        return $this->getResponse(200, 'success', 'Country list', $countries);
    }

    public function getAllCurrency() {
        $currencies = Currency::where('status', 1)->get();
        if($currencies->isEmpty())
            return $this->getResponse(404, 'failed', 'Currencies not found!', null);
        return $this->getResponse(200, 'success', 'Currency list', $currencies);
    }

    public function getAllTimezone() {
        $timezones = Timezone::where('status', 1)->get();
        if($timezones->isEmpty())
            return $this->getResponse(404, 'failed', 'Timezones not found!');
        return $this->getResponse(200, 'success', 'Timezone list', $timezones);
    }

    public function getMyCountry() {
        $country = Branch::select('countries.name', 'countries.iso', 'countries.isd')
                            ->leftJoin('countries', 'countries.id', '=', 'branches.country_id')
                            ->where('branches.id', $this->get_my_branch_id())
                            ->first();
        if(!$country)
            return $this->getResponse(404, 'failed', 'Country not found!');
        return $this->getResponse(200, 'success', 'Country list', $country);
    }

    public function adminList() {
        $admins = Admin::whereIn('id', $this->get_admin_ids())->get();
        if(!$admins)
            return $this->getResponse(404, 'failed', 'Admins not found!');
        return $this->getResponse(200, 'success', 'Admin list', $admins);

    }

    public function saveNewAdmin(Request $request) {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|string|unique:admins',
            'email'      => 'required|email|unique:admins',
            'password'   => 'required|min:6'
        ]);

        DB::beginTransaction();

        try {

            $data = new Admin;
            $data->uuid = Str::uuid();
            $data->first_name  = $request->first_name;
            $data->last_name   = $request->last_name;
            $data->phone       = $request->phone;
            $data->email       = $request->email;
            $data->password    = Hash::make($request->password, ['rounds' => 12]);
            $data->is_super   = 0;
            $data->save();

            $accessControll = new AccessControll;
            $accessControll->admin_id = $data->id;
            $accessControll->access_controll = '{"shop_id":"'.$this->get_my_shop_id().'","branch_id":"'.$this->get_my_branch_id().'"}';
            $accessControll->save();

            DB::commit();

            return $this->getResponse(200, 'success', 'Admin Added Successfully.');

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->getResponse(500, 'failed', 'Something went wrong!', $e->getMessage());
        }
    }

    public function adminActiveToggle($uuid) {

        $data = Admin::where('uuid', $uuid)->first();

        $field = null;
        if($data->status == 0) {
            $field = 1;
        } else {
            $field = 0;
        }

        $data->status = $field;

        $data->save();

        if($field == 1) {
            return $this->getResponse(200, 'success', 'Admin Activated Successfylly.');
        }
        else if($field == 0) {
            return $this->getResponse(200, 'success', 'Admin Deactivated Successfylly.');
        } else {
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        }
    }

    public function getAdminprofile($uuid) {
        $admin = Admin::where('uuid', $uuid)->first();
        if(!$admin)
            return $this->getResponse(404, 'failed', 'Admins not found!');
        return $this->getResponse(200, 'success', 'Admin found.', $admin);
    }

    public function updateAdminProfile(Request $request, $uuid) {

        $data = Admin::where('uuid', $uuid)->first();

         $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'nullable|unique:admins,phone,'.$data->id,
            'email'      => 'required|email|unique:admins,email,'.$data->id
        ]);

        $data->first_name  = $request->first_name;
        $data->last_name   = $request->last_name;
        $data->phone       = $request->phone;
        $data->email       = $request->email;

        $data->save();

        if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Admin profile updated successfully.');
    }
}
