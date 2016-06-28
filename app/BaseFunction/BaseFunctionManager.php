<?php namespace App\BaseFunction;

use App\BaseFunction\BaseFunctionManager as BF;

class BaseFunctionManager {

    public function doSomething()
    {
        echo 'Do something!';
    }

    public static function result($success = true, $message) {
        if($success) {
            return ['status' => 'success', 'datas' => $message];
        }
        return ['status' => 'error', 'message' => $message];
    }


}