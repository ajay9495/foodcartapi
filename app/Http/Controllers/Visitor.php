<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Visitor extends BaseController{

    function postVisitorData(Request $request){

        $result = DB::table('visitors')
                ->insert([
                    'user_id' => $request['user_id'],
                    'store_id' =>  $request['store_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($result){

            return response()->json([
                "status" => "success"
            ]);

        }
        else{
            return response()->json([
                "status" => "failed"
            ]);
        }

        
    }

    function processQueryResult($result){

        if($result){
            return "query success";
        }
        else{
            return "query failed";
        }

    }


}

