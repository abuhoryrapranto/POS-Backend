<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staffs;
use Illuminate\Support\Str;

class StaffsController extends Controller
{

    public function getAllStaffs() {
        $staffs = Staffs::where('branch_id', $this->get_my_branch_id())->get();
    	if($staffs->isEmpty())
            return $this->getResponse(404, 'failed', 'Staffs not found!');
        return $this->getResponse(200, 'success', 'All Staffs', $staffs);
    }

    public function saveStaff(Request $request) {

    	$this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'branch_id'  => 'exists:branches,id',
            'phone'      => 'required|unique:staffs',
            'email'      => 'nullable|unique:staffs',
            'address'	 => 'required|string'
        ]);
        

    	$data = new Staffs;

        $data->uuid       = (string) Str::uuid();
        $data->branch_id  = $this->get_my_branch_id();
    	$data->first_name = $request->first_name;
    	$data->last_name  = $request->last_name;
    	$data->phone 	  = $request->phone;
    	$data->email 	  = $request->email;
        $data->department = $request->department;
    	$data->nid 		  = $request->nid;
    	$data->address 	  = $request->address;

    	$data->save();

        if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(201, 'success', 'Staffs Added Successfully.');
    }
}
