<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;

    protected $table = 'expenses';
    protected $fillable = ['expense_categories_id', 'name', 'cost', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
}
