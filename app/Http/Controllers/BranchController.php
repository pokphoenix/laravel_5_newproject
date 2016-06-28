<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use BF;

class BranchController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
    }

    public function store()
    {
        $data = Input::all();
        $data = array_diff_key($data, array_flip(['id','_method','deleted_at','deleted_by','updated_at','created_at']));
        //$data["created_by"] = Session::get('user_id');
        try {
            $status = Branch::create($data);
            if($status === NULL) {
                return BF::result(false, 'failed!');
            }
        } catch ( \Illuminate\Database\QueryException $e) {
            if($e->getCode() == 23000) {
                return BF::result(false, "ชื่อซ้ำ: {$data['name']}");
            }
            return BF::result(false, $e->getMessage());
        }
        return BF::result(true, ['action' => 'create', 'id' => $status->id]);
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $data =  Branch::find($id);
        return BF::result(true, ['action' => 'edit', 'data' => $data]);
    }

    public function update($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $data = Input::all();
        $data = array_diff_key($data, array_flip(['id','_method','deleted_at','deleted_by','updated_at','created_at']));
        //$data["updated_by"] = Session::get('user_id');
        try {
            $status = Branch::whereId($id)->update($data);
            if($status == 1) {
                return BF::result(true, ['action' => 'update', 'id' => $id]);
            }
        } catch ( \Illuminate\Database\QueryException $e) {
            if($e->getCode() == 23000) {
                return BF::result(false, "ชื่อซ้ำ: {$data['name']}");
            }
            return BF::result(false, $e->getMessage());
        }
        return BF::result(false, 'failed!');
    }

    public function destroy($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $customer = Branch::find($id);
        if (is_null($customer)) {
            Branch::withTrashed()->whereId($id)->first()->restore();
            return BF::result(true, ['action' => 'restore', 'id' => $id]);
        }else{
            $customer->delete();
            return BF::result(true, ['action' => 'delete', 'id' => $id]);
        }
    }
}
