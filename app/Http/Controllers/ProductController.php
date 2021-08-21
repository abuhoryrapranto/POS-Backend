<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Models\Type;
use App\Models\Unit;
use App\Models\Products;

class ProductController extends Controller
{
    public function getAllBrands() {
    	$brands = Brand::where('branch_id', $this->get_my_branch_id())->get();
        if($brands->isEmpty())
            return $this->getResponse(404, 'failed', 'Brands not found!');
        return $this->getResponse(200, 'success', 'All Brands', $brands);
    }

    public function saveBrand(Request $request) {
    	$this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'The brand name field is required.'
        ]);

    	$data = new Brand;
        $data->uuid = Str::uuid();
        $data->branch_id = $this->get_my_branch_id();
    	$data->name = $request->name;

    	$data->save();

    	if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(201, 'success', 'Brand added succesfully.');

    }

    public function updateBrand(Request $request, $uuid) {
    	$this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'The brand name field is required.'
        ]);

    	$data = Brand::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();

    	$data->name = $request->name;

    	$data->save();

    	if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Brand updated succesfully.');

    }

    public function brandActiveChange($uuid) {
    	$data = Brand::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();

        $field = null;
        if($data->status == 0) {
            $field = 1;
        } else {
            $field = 0;
        }

    	$data->status = $field;

    	$data->save();

        if(!$data) {
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        } else {
            if($field == 1) {
                return $this->getResponse(200, 'success', 'Brand activated succesfully.'); 
            } else {
                return $this->getResponse(200, 'success', 'Brand deactivated succesfully.'); 
            }
        }
    }

    public function deleteBrand($uuid) {
        $brand = Brand::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();
        $exist = Products::where('brand_id', $brand->id)->where('branch_id', $this->get_my_branch_id())->first();
        if($exist) {
            return $this->getResponse(400, 'failed', 'You can not delete this brand. Because this brand is associated with one or many products. You may force delete this brand. But all the product associated with this brand will be deleted.');
        } else {
            $brand->where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->delete();
            return $this->getResponse(200, 'success', 'Brand deleted succesfully.');
        }  
    }

    public function forceDeleteBrand($uuid) {
        $brand = Brand::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();
        $brand->where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->delete();
        $data = Products::where('brand_id', $brand->id)->delete();
        if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Brand deleted succesfully.');
    }

    public function getAllTypes() {
    	$types = Type::where('branch_id', $this->get_my_branch_id())->get();
        if($types->isEmpty())
            return $this->getResponse(404, 'failed', 'Types not found!');
        return $this->getResponse(200, 'success', 'All Types', $types);
    }

    public function saveType(Request $request) {
    	$this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'The type name field is required.'
        ]);

    	$data = new Type;
        $data->uuid = Str::uuid();
        $data->branch_id = $this->get_my_branch_id();
    	$data->name = $request->name;

    	$data->save();

    	if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(201, 'success', 'Type added succesfully.');

    }

    public function updateType(Request $request, $uuid) {
    	$this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'The type name field is required.'
        ]);

    	$data = Type::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();

    	$data->name = $request->name;

    	$data->save();

    	if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Type updated succesfully.');

    }

    public function typeActiveChange($uuid) {
    	$data = Type::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();

        $field = null;
        if($data->status == 0) {
            $field = 1;
        } else {
            $field = 0;
        }

    	$data->status = $field;

    	$data->save();

        if(!$data) {
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        } else {
            if($field == 1) {
                return $this->getResponse(200, 'success', 'Type activated succesfully.'); 
            } else {
                return $this->getResponse(200, 'success', 'Type deactivated succesfully.'); 
            }
        }
    }

    public function deleteType($uuid) {
        $brand = Type::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();
        $exist = Products::where('brand_id', $brand->id)->where('branch_id', $this->get_my_branch_id())->first();
        if($exist) {
            return $this->getResponse(400, 'failed', 'You can not delete this type. Because this type is associated with one or many products. You may force delete this type. But all the product associated with this type will be deleted.');
        } else {
            $brand->where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->delete();
            return $this->getResponse(200, 'success', 'Type deleted succesfully.');
        }  
    }

    public function forceDeleteType($uuid) {
        $type = Type::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();
        $type->where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->delete();
        $data = Products::where('brand_id', $type->id)->delete();
        if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Type deleted succesfully.');
    }

    public function getAllUnits() {
    	$units = Unit::where('branch_id', $this->get_my_branch_id())->get();
        if($units->isEmpty())
            return $this->getResponse(404, 'failed', 'Units not found!');
        return $this->getResponse(200, 'success', 'All Units', $units);
    }

    public function saveUnit(Request $request) {
    	$this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'The unit name field is required.'
        ]);

    	$data = new Unit;
        $data->uuid = Str::uuid();
        $data->branch_id = $this->get_my_branch_id();
    	$data->name = $request->name;

    	$data->save();

    	if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(201, 'success', 'Unit added succesfully.');

    }

    public function updateUnit(Request $request, $uuid) {
    	$this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'The unit name field is required.'
        ]);

    	$data = Unit::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();

    	$data->name = $request->name;

    	$data->save();

    	if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Unit updated succesfully.');

    }

    public function unitActiveChange($uuid) {
    	$data = Unit::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();

        $field = null;
        if($data->status == 0) {
            $field = 1;
        } else {
            $field = 0;
        }

    	$data->status = $field;

    	$data->save();

        if(!$data) {
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        } else {
            if($field == 1) {
                return $this->getResponse(200, 'success', 'Unit activated succesfully.'); 
            } else {
                return $this->getResponse(200, 'success', 'Unit deactivated succesfully.'); 
            }
        }
    }

    public function deleteUnit($uuid) {
        $brand = Unit::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();
        $exist = Products::where('brand_id', $brand->id)->where('branch_id', $this->get_my_branch_id())->first();
        if($exist) {
            return $this->getResponse(400, 'failed', 'You can not delete this unit. Because this unit is associated with one or many products. You may force delete this unit. But all the product associated with this unit will be deleted.');
        } else {
            $brand->where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->delete();
            return $this->getResponse(200, 'success', 'Unit deleted succesfully.');
        }  
    }

    public function forceDeleteUnit($uuid) {
        $type = Unit::where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->first();
        $type->where('uuid', $uuid)->where('branch_id', $this->get_my_branch_id())->delete();
        $data = Products::where('brand_id', $type->id)->delete();
        if(!$data)
            return $this->getResponse(500, 'failed', 'Something went wrong!');
        return $this->getResponse(200, 'success', 'Unit deleted succesfully.');
    }
}
