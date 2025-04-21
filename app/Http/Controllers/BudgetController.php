<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Service\CustomReturn;

class BudgetController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = [];
        $userId=auth()->user()->id;
        $driver = DB::getDriverName();
        $expenseQuery = $driver === 'sqlite'
            ? "(SELECT IFNULL(SUM(amount), 0) FROM expenses WHERE expenses.user_id = budgets.user_id AND strftime('%Y-%m', expenses.date) = budgets.month )"
            : "(SELECT IFNULL(SUM(amount), 0) FROM expenses WHERE expenses.user_id = budgets.user_id AND DATE_FORMAT(expenses.date, '%Y-%m') = budgets.month )";
        Budget::where('user_id', $userId)
            ->select(['id','user_id','month','budget',
                DB::raw("$expenseQuery as total_expense"),
                DB::raw("budget - $expenseQuery as remaining_budget"),
            ])
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$datas) {
                $datas = [...$datas, ...$rows];
                flush();
            });
        return response()->json([
            'status' => true,
            'budget' => $datas
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return app('CustomReturn')->jsonResponse(function () use($request){
           $validate =  app('CustomReturn')->validate($request,[
                'month' => 'required|date',
                'budget' => 'required|numeric|min:0',
            ]);
           if($validate){
               return $validate;
           }
            $userId=auth()->user()->id;
            $oldRecord = Budget::where('user_id',$userId)->where('month',Carbon::parse($request->month)->firstOfMonth())->first();
            if($oldRecord){
                throw new \Error("For month ".Carbon::parse($request->month)->format('F')." Budget exist");
            }
            $budget = Budget::create([
                'user_id'=>$userId,
                'month'=>Carbon::parse($request->month)->firstOfMonth(),
                'budget'=>(double)$request->budget,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Budget Created successfully',
                'budget' => $budget
            ], 201);
        },400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
//        $budget->total_expense;
        return app('CustomReturn')->jsonResponse(function () use($budget){
            $userId=auth()->user()->id;
            if($userId != $budget->user_id){
                throw new \Error("Unauthorized, Budget not belongs to this user.");
            }
            $budget->remaining_budget = $budget->budget - $budget->total_expense;
            return response()->json([
                'status' => true,
                'budget' => $budget->only(['month','budget','id','total_expense','remaining_budget'])
            ], 200);
        },401);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        return app('CustomReturn')->jsonResponse(function () use($request,$budget){
            $userId=auth()->user()->id;
            if($userId != $budget->user_id){
                throw new \Error("Unauthorized, Budget not belongs to this user.");
            }
            $validate =  app('CustomReturn')->validate($request,[
                'month' => 'required|date',
                'budget' => 'required|numeric|min:0',
            ]);
            if($validate){
                return $validate;
            }
            $oldRecord = Budget::where('user_id',$userId)->where('month',Carbon::parse($request->month)->firstOfMonth())->whereNot('id' ,$budget->id)->first();
            if($oldRecord){
                throw new \Error("For month ".Carbon::parse($request->month)->format('F')." Budget exist");
            }
            $budget->update([
                'month'=>Carbon::parse($request->month)->firstOfMonth(),
                'budget'=>(double)$request->budget,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Budget updated successfully',
                'budget' => $budget
            ], 201);
        },401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        return app('CustomReturn')->jsonResponse(function () use($budget){
            $userId = auth()->user()->id;
            if($userId != $budget->user_id){
                throw new \Error("Unauthorized, Budget not belongs to this user.");
            }
            $budget->delete();

            return response()->json([
                'status' => true,
                'message' => 'Budget deleted successfully',
            ], 200);
        },401);
    }
}
