<?php

namespace  App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Expanse;
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

        $schedule = Expanse::schedule($request->duration);

        $request->request->add(['user_id' => $user->id, 'scheduled_on' => $schedule, 'seen_at' => date('Y-m-d')]);
        $expens = Expanse::create($request->only('user_id','type','price','duration','start_time','description','scheduled_on','seen_at'));

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
        }
        $expenses = Expanse::where([
            ['user_id','=', $user->id],
            ['start_time','LIKE','%'.$searchDate.'%'],
          ])->get();

        return response ([
            'success'   => true,
            'message'   => 'Expense Fetched Successfully',
            'data'      => $expenses,
          ],200)->header('Content-Type', 'application/json');
    }

    public function notificationList()
    {
        $user          = Auth::user();
        $notifications = [];
        $now           = Carbon::now();
        $expenses = Expanse::where(['user_id' => $user->id, 'seen' => 0])
                            ->where('seen_at', '!=', date('Y-m-d'))
                            ->orWhereNull('seen_at')
                            ->get();
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
