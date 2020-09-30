<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
class TestCrone extends Command
{

    protected $signature = 'test:cron';

    protected $description = 'Command description';
    public function handle()
    {
      Mail::send('welcome', ['test' => 'herer'], function ($message) {
        $message->from('avinash.glocify@gmail.com', 'Forecast');

        $message->to('test@example.com');
      });
    }
}
