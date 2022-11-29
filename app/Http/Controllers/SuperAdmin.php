<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdmin extends BaseController{




    function SuperLogin(Request $request){

        
        $req = $request->all(); 

        $payload = [
            'phone'=> $req['phone'],
            'password' => $req['password']
        ];


        $result =  DB::table('super_admin')
        ->select('id','password','phone')   
        ->where('phone',$payload['phone'])
        ->get();

        if(count($result) > 0){

            $dbPassword = $result[0]->password;
            $userPassword = $payload['password'];

            if($dbPassword ==  $userPassword){

                return response()->json([
                    "status" => "success",
                    "message" => "User is Active",
                    "data" => [
                        "id" => $result[0]->id
                    ]
                ]);
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



        if($isUserValid == true){
            
            $data = [
                'status' => 'success',
                'data' => [
                    'id' => $result[0]->id,
                    'name' => $result[0]->name
                ]
            ];
            return response()->json($data);
        }
        else{

            $data = [
                'status' => 'fail',
                'data' => 'test'
            ];
            return response()->json($data);
        }

        
         
    }

    function SuperReg(Request $request){

        $req = $request->all();

        $payload = [

            'phone'=> $req['phone'],
            'password' => $req['password'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        ];


        $result = DB::table('super_admin')
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

    function getApprovalsData(Request $request){

        $result = DB::table("admin")
        ->where("is_active",0)
        ->select('id','name')
        ->get();

        return response()->json($result);
    }

    function approveAdmin(Request $request){

        $store_payload = [
            "name" => $request["name"],
            "is_active" => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table("store")
        ->insertGetId($store_payload);


        if($result){

            $admin_payload = [
                "store_id" => $result,
                "is_active" => true
            ];


            $admin_result = DB::table("admin")
            ->where('id',$request["id"])
            ->update($admin_payload);

            if($admin_result){
                return response()->json([
                    "status" => "success",
                    "message" => "Successfully activated the admin"
                ]);
            }
            else{
                return response()->json([
                    "status" => "failed",
                    "message" => "Could not update admin"
                ]);
            }

        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Could not create the Store"
            ]);
        }



    }

    function getDashboardData(Request $request){


        $storeId = $request["store_id"];

        $data = [
            'DailyUniqueVisitorsBreakDown' => $this->getDailyUniqueVisitorsBreakDown(),
            'dailyVisitorsBreakdown'  => $this->getDailyVisitorsBreakDown(),
            'dailySalesBreakdown' => $this->getDailySalesBreakDown(),
            'monthlySales' => $this->getTotalMonthlySales(),
            'monthlyVisitors' => $this->getTotalMonthlyVisit(),
            'uniqueMonthlyVisits' => $this->getUniqueMonthlyVisit()
        ];

        
        return response()->json($data);
    }


    function getTotalMonthlySales(){

        $startDate = date('Y-m-01').' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        return DB::table('orders')
        ->whereBetween('orders.created_at',[$startDate,$endDate])
        ->leftJoin('order_details','orders.id','order_details.order_id')
        ->get()
        ->map(function ($item,$index){
            return [
                "price" => $this->getTotalSales($item)
            ];
        })
        ->reduce(function ($carry, $item) {
            return $carry + $item["price"];
        });


    }

    function getTotalSales($item){

        $sellingPrice = json_decode($item->selling_price);
        $unitPrice = intval($sellingPrice->value->price);
        $unitQuantity = intval($sellingPrice->value->quantity);
        $itemQuantity = $item->quantity;

        return $itemQuantity * $unitPrice * $unitQuantity;
    }

    function getTotalMonthlyVisit(){

        $startDate = date('Y-09-01').' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        return DB::table('visitors')
        ->whereBetween('created_at',[$startDate,$endDate])
        ->count();

    }

    function getUniqueMonthlyVisit(){

        $startDate = date('Y-m-01').' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        return DB::table('visitors')
        ->select("user_id")
        ->whereBetween('created_at',[$startDate,$endDate])
        ->groupBy("user_id")
        ->get()
        ->count();
    }


    function getDailySalesBreakDown(){


        $startDate = date('Y-m-d',strtotime('-30 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $ordersResult =  DB::table('orders')
        ->whereBetween('orders.created_at',[$startDate,$endDate])
        ->leftJoin('order_details','orders.id','order_details.order_id')
        ->select(DB::raw('DATE(orders.created_at) as date'),'order_details.selling_price','order_details.quantity')
        ->get()
        ->groupBy(DB::raw('date'))
        ->map(function($item,$key){

            return [
                "date" => $item[0]->date,
                "sum" => $this->getSalesSum($item)
            ];
        });

        return array_values($ordersResult->toArray());

    }


    function getSalesSum($item){

        $sum  = [];
        $sellingPrice = [];
        $total = 0;
        for($i = 0; $i < count($item); $i++){

            $sellingPrice = json_decode($item[$i]->selling_price);
            $unitPrice = intval($sellingPrice->value->price);
            $unitQuantity = intval($sellingPrice->value->quantity);

            $total = $total + $unitPrice * $unitQuantity * $item[$i]->quantity;
        }

        return $total;
    }

    function getDailyVisitorsBreakDown(){

        $startDate = date('Y-m-d',strtotime('-20 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        return DB::table('visitors')
        ->whereBetween('created_at',[$startDate,$endDate])
        ->select(DB::raw('DATE(created_at) as date'),DB::raw('count(visitors.user_id) as visitors'))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get();

    }

    function getDailyUniqueVisitorsBreakDown(){

        $startDate = date('Y-m-d',strtotime('-20 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $result = DB::table('visitors')
        ->whereBetween('created_at',[$startDate,$endDate])
        ->select(DB::raw('DATE(created_at) as date'),DB::raw('user_id'))
        ->get()
        ->groupBy(DB::raw('date'))
        ->map(function($item,$key){

            return [
                "date" => $item[0]->date,
                "visitors" => $item->unique("user_id")->count()
            ];
        });

        return(array_values($result->toArray()));

    }


}