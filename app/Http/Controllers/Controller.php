<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Shop;
use App\Models\AccessControll;
use App\Models\Currency;
use App\Models\Country;
use App\Models\Branch;
use Auth;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getResponse($status_code, $status, $message, $data) {
        return response()->json([
            'status_code' => (int)$status_code,
            'status' => (string)$status,
            'message' => (string)$message,
            'data' => $data
        ], $status_code);
    }

    public function get_my_shop_id() {
    	$accessControll = AccessControll::where('admin_id', Auth::user()->id)->first();
        $accessControll = json_decode($accessControll->access_controll, true);
        return explode(',', $accessControll['shop_id'])[0];
    }
    
    public function get_my_branch_id() {
    	$accessControll = AccessControll::where('admin_id', Auth::user()->id)->first();
        $accessControll = json_decode($accessControll->access_controll, true);
        $totalBranch = count(explode(',', $accessControll['branch_id']));
        if(Auth::user()->is_super == 1 && $totalBranch > 1) {
            return (string) session('branch_id');
        } else {
            return explode(',', $accessControll['branch_id'])[0];
        }
    }

    public function get_my_shop_name() {
    	$shop_name = Shop::select('name')->where('id', $this->get_my_shop_id())->first();
    	return $shop_name['name'];
    }

    public function get_admin_ids() {
        $branch_id = $this->get_my_branch_id();
        $shop = AccessControll::where('access_controll->shop_id', $this->get_my_shop_id())->get();
        $data = [];
        foreach ($shop as $row) {
            $newData = [];
            $newData['admin_id'] = $row->admin_id;
            $newData['branch_id'] = explode(',',json_decode($row->access_controll, true)['branch_id']);
            array_push($data, $newData);
        }

        $admin_ids = [];
        foreach ($data as $row) {
            if(in_array($branch_id, $row['branch_id'])) {
                array_push($admin_ids, $row['admin_id']);
            }
            
        }

        return $admin_ids;

    }

    public function get_my_country_id() {
        $country_id = Branch::select('country_id')->where('id', $this->get_my_branch_id())->first();
        return $country_id->country_id;
    }
}
