<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expanse extends Model
{
    protected $fillable = ['type', 'price', 'description', 'duration','start_time','user_id','seen', 'scheduled_on','seen_at','end_time'];
    protected $appends  = ['expense_type', 'duration_type'];

    public $expenseType = [
        '1' => 'Income',
        '2' => 'Expense',
    ];

    public $durationExpense = [
        '1' => 'One-Time',
        '2' => 'Weekly',
        '3' => 'Bi-Weekly',
        '4' => 'Monthly',
    ];

    public function getExpenseTypeAttribute()
    {
        return $this->expenseType[$this->type];
    }

    public function getDurationTypeAttribute()
    {
        return $this->durationExpense[$this->duration];
    }


    public static function schedule($duration)
    {
       $durationExpense = [
          '1' => 'One-Time',
          '2' => 'Weekely',
          '3' => 'Bi-Weekely',
          '4' => 'Monthly',
        ];

        $scheduledType = $durationExpense[$duration];
        $schedule      = Carbon::now();

        if($scheduledType == 'Monthly') {
          $schedule  = $schedule->addMonths(1);
        } elseif($scheduledType == 'Bi-Weekely'){
          $schedule  = $schedule->addDays(15);
        } elseif($scheduledType == 'Weekely'){
          $schedule  = $schedule->addDays(7);
        }
        return $schedule;
    }
}
