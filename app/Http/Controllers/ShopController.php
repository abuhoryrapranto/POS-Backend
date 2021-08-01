<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use App\Models\Branch;
use App\Models\AccessControll;
use App\Models\Currency;
use App\Models\Country;
use App\Models\Timezone;
use Auth;
use Session;

class ShopController extends Controller
{
    public function openShop(Request $request) {

    	$this->validate($request, [
            'name' 		   => 'required',
            'address'    => 'required|string',
            'country'     => 'required',
            'currency'     => 'required',
            'timezone'    => 'required'
        ]);

        DB::beginTransaction();

        try{

            $shop = new Shop;
            $shop->uuid = Str::uuid();
            $shop->name = $request->name;
            $shop->save();

            $branch = new Branch;
            $branch->uuid = Str::uuid();
            $branch->shop_id = $shop->id;
            $branch->name = "Main Branch";
            $branch->address = $request->address;
            $branch->country_id = $request->country;
            $branch->currency_id = $request->currency;
            $branch->timezone_id = $request->timezone;
            $branch->save();

            $request->session()->put('branch_id', $branch->id);

            AccessControll::where('admin_id', Auth::user()->id)
                            ->update([
                                'access_controll' => '{"shop_id":"'.$shop->id.'","branch_id":"'.$branch->id.'"}'
                            ]);
            DB::commit();

            return $this->getResponse(201, 'success', 'Shop created successfully.');

        }catch(\Exception $e) {

            DB::rollBack();
            //throw $e;
            return $this->getResponse(400, 'failed', 'Something went wrong.', $e->getMessage());
        } 
    }
}
