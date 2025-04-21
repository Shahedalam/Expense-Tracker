<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id','amount','category','description','date'];
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d G:i:s');
    }
}
