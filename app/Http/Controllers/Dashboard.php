<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use Carbon\Carbon;


class Dashboard extends BaseController{


    function getAdminDashboardData(Request $request){

        return response()->json("test response");

        $storeId = $request["store_id"];

        $result = [
            'dailyVisitorsBreakdown'  => $this->getDailyVisitorsBreakDown($storeId),
            'dailySalesBreakdown' => $this->getDailySalesBreakDown($storeId),
            'monthlyVisitors' => $this->getTotalMonthlyVisit($storeId),
            'monthlySales' => $this->getTotalMonthlySales($storeId)
        ];


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
                "message" => "Failed to get data from the server"
            ]);
        }

    }


    function getDailyVisitorsBreakDown($storeId){

        $startDate = date('Y-m-d',strtotime('-9 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        return DB::table('visitors')
        ->where('store_id',$storeId)
        ->whereBetween('created_at',[$startDate,$endDate])
        ->select(DB::raw('DATE(created_at) as date'),DB::raw('count(visitors.user_id) as visitors'))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get();

    }


    function getTotalMonthlyVisit($storeId){

        $startDate = date('Y-m-01').' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $result = DB::table('visitors')
        ->where('store_id',$storeId)
        ->whereBetween('created_at',[$startDate,$endDate])
        ->count();

        if($result == null){
            return 0;
        }
        else{
            return $result;
        }

    }


    function getDailySalesBreakDown($storeId){


        $startDate = date('Y-m-d',strtotime('-10 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $ordersResult =  DB::table('orders')
        ->where('store_id',$storeId)
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

    
    function getTotalMonthlySales($storeId){

        $startDate = date('Y-m-01').' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $result =  DB::table('orders')
        ->where('store_id',$storeId)
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

        if($result == null){
            return 0;
        }
        else{
            return $result;
        }


    }

    function getSalesSum($item){

        $total = 0;
        for($i = 0; $i < count($item); $i++){

            $total = $total + $item[$i]->selling_price * $item[$i]->quantity;
        }

        return $total;
    }

    function getTotalSales($item){

        $sellingPrice = json_decode($item->selling_price);
        $unitPrice = intval($sellingPrice->value->price);
        $unitQuantity = intval($sellingPrice->value->quantity);
        $itemQuantity = $item->quantity;

        return $itemQuantity * $unitPrice * $unitQuantity;
    }

}