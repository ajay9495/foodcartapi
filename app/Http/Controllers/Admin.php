<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Admin extends BaseController{

    function Login(Request $request){
        
        $req = $request->all(); 


        $payload = [
            'admin_id'=> $req['admin_id'],
            'password' => $req['password']
        ];

        
        $result =  DB::table('admin')
        ->select('id','password','name','is_active','store_id')   
        ->where('id',$payload['admin_id'])
        ->get();


        if(count($result) > 0){

            $dbPassword = $result[0]->password;
            $userPassword = $payload['password'];

            if($dbPassword ==  $userPassword){

                if($result[0]->is_active){

                    return response()->json([
                        "status" => "success",
                        "message" => "User is Active",
                        "data" => $result[0]
                    ]);
                }
                else{
                    return response()->json([
                        "status" => "failed",
                        "message" => "User is inActive"
                    ]);
                }
            }
            else{
                return response()->json([
                    "status" => "failed",
                    "message" => "Password did not match"
                ]);
            }
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "User not found"
            ]);
        }

        
         
    }

    function Register(Request $request){

        $req = $request->all();

        $payload = [

            'name'=> $req[0]['value'],
            'email'=> $req[1]['value'],
            'phone'=> $req[2]['value'],
            'password' => $req[3]['value'],
            'store_id' => '',
            'is_active'=> false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        ];

        $result = DB::table('admin')
                    ->insertGetId($payload);

        if($result){

            $responsePayload = [
                "admin_id" => $result,
                "status"   => "success"
            ];

            return response()->json($responsePayload);
        }
        else{

            $responsePayload = [
                "status"   => "failed"
            ];
            
            return response()->json($responsePayload);
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