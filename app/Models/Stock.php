<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "stocks";

    protected $fillable = ['product_id', 'product_name', 'product_category_id', 'product_category_name', 'stocks', 'unit', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
}
