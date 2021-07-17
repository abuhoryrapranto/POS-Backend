<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherBilling extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'invoice_no', 'branch_id', 'vat', 'delivery_charge'];
}
