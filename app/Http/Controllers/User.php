<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class User extends BaseController{

    function LoginUser(Request $request){

        
        $req = $request->all(); 

        $payload = [
            'phone'=> $req[0]['value'],
            'password' => $req[1]['value']
        ];


        $result =  DB::table('users')
        ->select('id','password','store_id','address','landmark','location')   
        ->where('phone',$payload['phone'])
        ->get();




        if(count($result) > 0){

            $isUserValid = false;
            $dbPassword = $result[0]->password;
            $userPassword = $payload['password'];



            if($dbPassword ==  $userPassword){

                return response()->json([

                    'status' => 'success',
                    'message' => 'Successfully Logged in',
                    'payload' => [
                        'user_id' => $result[0]->id,
                        'store_id' => $result[0]->store_id,
                        'address' => $result[0]->address,
                        'landmark' => $result[0]->landmark,
                        'location' => $result[0]->location
                    ]
                ]);

            }
            else{


                return response()->json([
                    'status' => "failed",
                    'message' => 'Password is Invalid'
                ]);
            }
        }
        else{

            return response()->json([
                'status' => "failed",
                'message' => 'Phone number is Invalid'
            ]);
        }
        
         
    }

    function RegisterUser(Request $request){

        $req = $request->all();

        $userStoreId = intval($req[3]['value']);
        $userPhone = $req[0]['value'];



        $storeDetails = DB::table('store')
        ->where('id',$userStoreId)
        ->get();

        if (count($storeDetails) > 0) {

            $userDetails = DB::table('users')
            ->where('phone',$userPhone)
            ->get();

            if(count($userDetails) > 0){
                
                return response()->json([
                    'status' => "failed",
                    'message' => 'Phone number already exists, try another one'
                ]);
            }
            else{

                $payload = [
                    'name'=> $req[2]['value'],
                    'phone'=> $req[0]['value'],
                    'password' => $req[1]['value'],
                    'store_id' => $userStoreId,
                    'address' => '',
                    'landmark' => '',
                    'location' => json_encode(['lat'=>'','lng'=>'']),         
                    'is_active'=> true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $result = DB::table('users')
                ->insertGetId($payload);

                return response()->json([
                    'status' => "success",
                    'message' => 'succcesfully created user',
                    'payload' => $result
                ]);

            }

        }
        else{
            return response()->json([
                'status' => "failed",
                'message' => 'Invalid store id'
            ]);
        }

    }

    function SetToken(Request $request){

        $req = $request->all();

        $payload = [
            "user_id" => $req["user_id"],
            "token" => $req["token"],
            "is_active" => true,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s')
        ];

        $result = DB::table("fcm_token")
                    ->insert($payload);

        return $this->processQueryResult($result);
    }

    function postAddressData(Request $request){
        
        $req = $request->all();
        $landmark = "na";

        if($req['data'][1]['value'] == ""){
            $landmark = "na";
        }
        else{
            $landmark = $req['data'][1]['value'];
        }

        $payload = [
            'address' => $req['data'][0]['value'],
            'landmark' => $landmark,
            'location' => json_encode($req['data'][2]['value']),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('users')
        ->where('id',$req['user_id'])
        ->update($payload);

        return $this->processQueryResult($result);

    }


    function processQueryResultWithId($result){

        if($result){
            return response()->json(["status"=>"success","user_id"=>$result]);
        }
        else{
            return response()->json(["status"=>"failed"]);
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