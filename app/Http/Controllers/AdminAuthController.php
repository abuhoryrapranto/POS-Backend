<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessControll;
use App\Models\Currency;
use App\Models\Country;
use Auth;

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
}
