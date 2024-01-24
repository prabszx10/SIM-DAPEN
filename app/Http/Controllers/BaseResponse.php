<?php

namespace App\Http\Controllers;

class BaseResponse
{
    public function response($data=''){
        if($data){
            $operation= array(
                'status' => true,
                'message'=>'Success To Load Data',
                'total' => is_array($data)?count($data):1,
                'data'=>$data
            );
        } else{
            $operation= array(
                'status' => false,
                'message'=> 'Failed To Load Data',
            );
        }

        return $operation;
    }

    public function responseCreate($data='',$show=false){
        if(isset($data->wasRecentlyCreated) || isset($data['success'])){
            $operation= array(
                'status' => true,
                'message'=>'Success To Save Data',
            );

            if($show){
                $operation['data'] = $data;
            }
        } else{
            $operation= array(
                'status' => false,
                'message'=> $show?$data:'Failed To Save Data',
            );
        }

        return $operation;
    }

    public function responseUpdate($data='',$show=false){
        if($data==1){
            $operation= array(
                'status' => true,
                'message'=>'Success To Update Data',
            );

        } else{
            $operation= array(
                'status' => false,
                'message'=> $show?$data:'Failed To Update Data',
            );
        }

        return $operation;
    }

    public function responseDelete($data='',$show=false){
        if($data==1 || isset($data['success'])){
            $operation= array(
                'status' => true,
                'message'=>'Success To Delete Data',
            );

        } else{
            $operation= array(
                'status' => false,
                'message'=> $show?$data:'Failed To Delete Data',
            );
        }

        return $operation;
    }
}
