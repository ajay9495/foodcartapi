<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Unit extends BaseController{

    function addUnit(Request $request){

        $result = DB::table('unit')
                    ->insert([
                        'name'=> $request['name'],
                        'is_active'=> true,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

        return $this->processQueryResult($result);
                        // 'name'=> $request['name'],

    }

    function getUnitData(Request $request){

        $result = DB::table('unit')
        ->select('id','name')
        ->get();
        
        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully got data from the server",
                "payload" => $result
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "Failed to get data from the server."
            ]);
        }

    }

    function processQueryResult($result){

        if($result){
            return response()->json(["status"=>"success"]);
        }
        else{
            return response()->json(["status"=>"failed"]);
        }

    }




}