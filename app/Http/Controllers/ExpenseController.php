<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Service\CustomReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = [];
        $userId=auth()->user()->id;
        DB::table('expenses')->where('user_id',$userId)->orderBy('id')->limit(5000)
            ->select(['id','amount','category','description','date'])
            ->chunk(1000, function ($rows) use (&$datas) {
                $datas = [...$datas,...$rows];
                flush();
            });
        return response()->json([
            'status' => true,
            'expenses' => $datas
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return app('CustomReturn')->jsonResponse(function () use($request){
            $validate =  app('CustomReturn')->validate($request,[
                'amount' => 'required|numeric|min:1',
                'category' => 'required|string|min:1',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);
            if($validate){
                return $validate;
            }
            $userId=auth()->user()->id;

            $expense = Expense::create([
                'user_id'=>$userId,
                'date'=>Carbon::parse($request->date),
                'amount'=>(double)$request->amount,
                'category'=>ucfirst($request->category),
                'description'=>$request->description,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Expense Created successfully',
                'Expense' => $expense
            ], 201);
        },500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return app('CustomReturn')->jsonResponse(function () use($expense){
            $userId=auth()->user()->id;
            if($userId != $expense->user_id){
                throw new \Error("Unauthorized, Expense not belongs to this user.");
            }
            return response()->json([
                'status' => true,
                'expense' => $expense->only(['id','date','amount','category','description'])
            ], 200);
        },401);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        return app('CustomReturn')->jsonResponse(function () use($request,$expense){
            $userId=auth()->user()->id;
            if($userId != $expense->user_id){
                throw new \Error("Unauthorized, Expense not belongs to this user.");
            }

            $validate =  app('CustomReturn')->validate($request,[
                'amount' => 'required|numeric|min:1',
                'category' => 'required|string|min:1',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);
            if($validate){
                return $validate;
            }

            $expense->update([
                'date'=>Carbon::parse($request->date),
                'amount'=>(double)$request->amount,
                'category'=>ucfirst($request->category),
                'description'=>$request->description,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Expense Updated successfully',
                'Expense' => $expense
            ], 201);
        },500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        return app('CustomReturn')->jsonResponse(function () use($expense){
            $userId = auth()->user()->id;
            if($userId != $expense->user_id){
                throw new \Error("Unauthorized, Expense not belongs to this user.");
            }
            $expense->delete();

            return response()->json([
                'status' => true,
                'message' => 'Expense deleted successfully',
            ], 200);
        },401);
    }
}
