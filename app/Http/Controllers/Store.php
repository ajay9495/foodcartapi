<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Store extends BaseController{

    function PostStoreData(Request $request){

        $result = DB::table('store')
                    ->insert([
                        'name'=> $request['name'],
                        'is_active'=> true,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

        return $this->processQueryResult($result);

    }

    

    function processQueryResult($result){

        if($result){
            return "PostStoreData query success";
        }
        else{
            return "query failed";
        }

    }


}