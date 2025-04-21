<?php
namespace App\Service;
use Illuminate\Support\Facades\Validator;

class CustomReturn
{
    public function jsonResponse(callable $callback, int $responseCode = 500)
    {
        try {
            return $callback();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], $responseCode);
        }
    }

    public function validate($request,$rules)
    {
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }
        return null;
    }
}
