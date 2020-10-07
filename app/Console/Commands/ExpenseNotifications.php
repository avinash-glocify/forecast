<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Expanse;
use Carbon\Carbon;

class ExpenseNotifications extends Command
{
    protected $signature   = 'expense:create';
    protected $description = 'This command add new entry to expense table';

    public function handle()
    {
        $expenses = Expanse::whereDate('scheduled_on', date('Y-m-d'))->get();

        foreach ($expenses as $key => $expense) {
          $schedule = Expanse::schedule($expense->duration);
          $expense->seen = 1;
          $expense->save();

          $expense  = $expense::create([
            'user_id'      => $expense->user_id,
            'type'         => $expense->type,
            'price'        => $expense->price,
            'start_time'   => $expense->start_time,
            'duration'     => $expense->duration,
            'scheduled_on' => $schedule
          ]);
        }
    }
}
