<?php

namespace  App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Expanse;
use Auth;

class ExpenseController extends Controller
{
    public function create(Request $request)
    {
        $user     = Auth::user();
        $rules = [
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

        $request->request->add(['user_id' => $user->id]);
        $expens = Expanse::create($request->only('user_id','type','price','duration','start_time','description'));

        return response ([
            'success'   => true,
            'message'   => 'Expense Created Successfully',
            'data'      => $expens,
          ],200)->header('Content-Type', 'application/json');
    }

    public function list()
    {
        $user     = Auth::user();
        $expenses = Expanse::where('user_id', $user->id)->get();

        return response ([
            'success'   => true,
            'message'   => 'Expense Created Successfully',
            'data'      => $expenses,
          ],200)->header('Content-Type', 'application/json');
    }
}
