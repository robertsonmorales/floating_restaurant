<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRecipe extends Model
{
    use HasFactory;

    protected $table = 'menu_recipes';

    protected $fillable = ['menu_id', 'menu_name', 'product_id', 'product_name', 'stock_out', 'created_by', 'updated_by', 'created_at', 'updated_at'];
}
