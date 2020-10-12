<?php

namespace  App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Expanse;
use App\User;
use Auth;
use DB,Carbon\Carbon;
class ExpenseController extends Controller
{
    public function create(Request $request)
    {
        $user     = Auth::user();

        $rules    = [
          'type'       => 'required',
          'price'      => 'required|integer',
          'duration'   => 'required',
          'start_time' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = array();
            foreach ($validator->messages()->all() as $message){
              array_push($errors,$message);
            }
            return response([
                'success' => false,
                'errors' => $errors
              ], 200)->header('Content-Type', 'application/json');
        }

        $schduleDate = Carbon::parse($request->start_time);

        $request->request->add(['user_id' => $user->id, 'scheduled_on' => $schduleDate, 'seen_at' => date('Y-m-d')]);
        $expens = Expanse::create($request->only('user_id','type','price','duration','start_time','description','scheduled_on','seen_at', 'end_time'));

        return response ([
            'success'   => true,
            'message'   => 'Expense Fetched Successfully',
            'data'      => $expens,
          ],200)->header('Content-Type', 'application/json');
    }

    public function list()
    {
        $user     = Auth::user();
        $expenses = Expanse::where('user_id', $user->id)->get();

        return response ([
            'success'   => true,
            'message'   => 'Expense Fetched Successfully',
            'data'      => $expenses,
          ],200)->header('Content-Type', 'application/json');
    }

    public function listByMonth($month)
    {
        $user       = Auth::user();
        $month      = $month ?? '2020-09';
        $date       = explode('-', $month);
        $searchDate = date('Y-m',strtotime($month));

        if(count($date) == 3) {
          $searchDate = date('Y-m-d',strtotime($month));
          $expenses = Expanse::where([
            ['user_id','=', $user->id],
          ])
          ->whereDate('scheduled_on', date('Y-m-d',strtotime($searchDate)))
          ->get();
        } else {
        $expenses = Expanse::where([
            ['user_id','=', $user->id],
            ['start_time','LIKE','%'.date('Y-m',strtotime($month)).'%'],
          ])->get();
        }

        return response ([
            'success'   => true,
            'message'   => 'Expense Fetched Successfully',
            'data'      => $expenses,
          ],200)->header('Content-Type', 'application/json');
    }

    public function forecastAmount($date)
    {
        $user        = Auth::user();

        if($user) {
          $expense         = new Expanse();
          $types           = array_flip($expense->expenseType);
          $incomeType      = $types['Income'];
          $expanseType     = $types['Expense'];
          $durationExpense = $expense->durationExpense;

          $profile         = $user->profile;
          $budget          = $profile->budget ?? '';

          $expenseQuery    = Expanse::where(['user_id' => $user->id])
                                    ->orderBy('id', 'desc')
                                    ->get();

          $expansePreviousAmount = 0;
          $incomePreviousAmount  = 0;
          $income                = 0;
          $expense               = 0;
          $expenseTra            = 0;
          $incomeTra             = 0;

          foreach ($expenseQuery as $key => $expanse) {

            $date = strtotime($date) > strtotime($expanse->end_time) ? $expanse->end_time : $date;
            if(strtotime($expanse->start_time) <= strtotime($date)) {
              $endTime     = $expanse->end_time;
              $searchDate  = Carbon::parse($date);
              $startTime   = Carbon::parse($expanse->start_time);
              $diffInDays  = $searchDate->diffInDays($startTime);
              $duration    = $durationExpense[$expanse->duration];
              $period      = $duration == 'Monthly' ? 31 : ($duration == 'Weekly' ? 7 : ($duration == 'Bi-Weekly' ? 15 : 1) );
              $circle      = 1;
              $searchTrans = false;

              if($duration != 'One-Time') {
                if($period < $diffInDays) {
                  $mode   = fmod($diffInDays, $period);
                  $cal    = intdiv($diffInDays, $period);
                  if($mode >= 0) {
                    $circle  = $cal+1;
                    if($mode == 0) {
                      $searchTrans = true;
                    }
                  }
                }
              }

              if($expanse->type == 1) {
                $income                = $searchTrans ? $expanse->price : 0;
                $incomePreviousAmount  = $incomePreviousAmount+($circle*$expanse->price);
                $incomeTra             = $incomeTra+$circle;
              } else {
                $expenseTra            = $expenseTra+$circle;
                $expense               = $searchTrans ? $expanse->price : 0;
                $expansePreviousAmount = $expansePreviousAmount+($circle*$expanse->price);
              }
            }
          }

          $data  = [
            'income'           => $income,
            'expanse'          => $expense,
            'budget'           => $budget,
            'income_trans'     => $incomeTra,
            'expense_trans'    => $expenseTra,
            'forecastAmount'   => $budget + ($incomePreviousAmount - $expansePreviousAmount)
          ];
          // 'previous_income'  => $incomePreviousAmount,
          // 'previous_expense' => $expansePreviousAmount,

          return response ([
            'success'   => true,
            'message'   => 'Expense Fetched Successfully',
            'data'      => $data,
          ],200)->header('Content-Type', 'application/json');
        }
    }


    public function getSpecificUserForecastAmount($id,$date)
    {
        $user        = User::find($id);
        $token       = Auth::user();
        $expense     = new Expanse();
        $types       = array_flip($expense->expenseType);
        $incomeType  = $types['Income'];
        $expanseType = $types['Expense'];

        $profile     = $user->profile;
        $budget      = $profile->budget ?? '';

        $expenseQuery         = Expanse::where(['user_id' => $user->id, 'type' => $expanseType]);

        $expensePreviousQuery = clone $expenseQuery;

        $expensePrevious      = $expensePreviousQuery
                                  ->whereDate('scheduled_on', '<=', date('Y-m-d',strtotime($date)))
                                  ->pluck('price')->sum();

        $expense               = $expenseQuery
                                   ->whereDate('scheduled_on', date('Y-m-d',strtotime($date)))
                                   ->pluck('price')->sum();

        $incomeQuery           = Expanse::where(['user_id' => $user->id, 'type' => $incomeType]);
        $previousIncomeQuery   = clone $incomeQuery;

        $previousIncome        = $previousIncomeQuery
                                  ->whereDate('scheduled_on', '<=', date('Y-m-d',strtotime($date)))
                                  ->pluck('price')->sum();

        $income                 = $incomeQuery
                                  ->whereDate('scheduled_on', date('Y-m-d',strtotime($date)))
                                  ->pluck('price')->sum();

        $data  = [
          'income'         => $income,
          'expanse'        => $expense,
          'budget'         => $budget,
          'forecastAmount' => $budget + ($previousIncome - $expensePrevious)
        ];

        return response ([
            'success'   => true,
            'message'   => 'Expense Fetched Successfully',
            'data'      => $data,
            'user'      => $token,
          ],200)->header('Content-Type', 'application/json');
    }

    public function notificationList()
    {
        $user          = Auth::user();
        $notifications = [];
        $now           = Carbon::now();
        $expenses = Expanse::where(['user_id' => $user->id, 'seen' => 0])
                            ->where(function($query) {
                                $query->where('seen_at', '!=', date('Y-m-d'));
                                $query->orWhereNull('seen_at');
                            })->get();
        foreach ($expenses as $key => $expens) {
          $schedule = Carbon::parse($expens->scheduled_on);
          $diff     = $schedule->diffInDays($now);

          if($diff == 1) {
            $message = $expens->expense_type == 'Income' ? 'You got paid today' : 'Your Payment is due in '.$diff. ' days';
          } elseif($diff == 7) {
            $message  = $expens->expense_type == 'Income' ? 'You get paid in 1 week' : 'Your Payment is due in 1 week';
          } else {
            $message  = $expens->expense_type == 'Income' ? 'You get paid in '.$diff. ' days' : 'Your Payment is due in '.$diff. ' days';
          }

          $notifications[$key] = [
            'id'      => $expens->id,
            'type'    => $expens->expense_type,
            'message' => $message
          ];
        }

        return response ([
            'success'   => true,
            'message'   => 'Notifications Fetched Successfully',
            'data'      => $notifications,
          ],200)->header('Content-Type', 'application/json');
    }

    public function removeNotification($id)
    {
       $expense = Expanse::find($id);
       if($expense) {
         $today = Carbon::now()->format('Y-m-d');
         $expense->seen_at = $today;
         $expense->save();

         return response ([
             'success'   => true,
             'message'   => 'Notifications Removed Successfully',
           ],200)->header('Content-Type', 'application/json');
       }

       return response ([
           'success'   => false,
           'message'   => 'Notifications not Found',
         ],200)->header('Content-Type', 'application/json');
    }

    public function removeExpanse($id)
    {
       $expense = Expanse::find($id);

       if($expense) {
         $expense->delete();
         return response ([
             'success'   => true,
             'message'   => 'Transaction Removed Successfully',
           ],200)->header('Content-Type', 'application/json');
       }

       return response ([
           'success'   => false,
           'message'   => 'Transaction not Found',
         ],200)->header('Content-Type', 'application/json');
    }
}
