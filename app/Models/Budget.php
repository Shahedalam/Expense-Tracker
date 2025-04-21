<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id','month','budget'];
    public function getMonthAttribute($value)
    {
        return Carbon::parse($value)->format('Y, F');
    }
    public function getTotalExpenseAttribute()
    {
        return Expense::where('user_id', $this->user_id)
            ->whereBetween('date', [
                Carbon::parse($this->month)->startOfMonth(),
                Carbon::parse($this->month)->endOfMonth()
            ])
            ->sum('amount');
    }
    public function monthlyExpense()
    {
        return Expense::whereBetween('date',[Carbon::parse($this->month)->firstOfMonth(),Carbon::parse($this->month)->lastOfMonth()])->get();
    }

}
