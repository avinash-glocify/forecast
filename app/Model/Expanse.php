<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Expanse extends Model
{
    protected $fillable = ['type', 'price', 'description', 'duration','start_time','user_id'];

    public $expenseType = [
        '1' => 'Income',
        '2' => 'Expense',
    ];

    public $duration = [
        '1' => 'One-Time',
        '2' => 'Weekely',
        '3' => 'Bi-Weekely',
        '4' => 'Monthly',
    ];

    public function getTypeAttribute($value)
    {
        return $this->expenseType[$value];
    }
    public function getDurationAttribute($value)
    {
        return $this->duration[$value];
    }
}
