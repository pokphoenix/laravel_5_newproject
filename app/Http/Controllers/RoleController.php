<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use BF;

class RoleController extends Controller
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
            $status = Role::create($data);
            $status->permissions()->sync($data['permissions']);
        } catch ( \Illuminate\Database\QueryException $e) {
            if($e->getCode() == 23000) {
                return BF::result(false, "ชื่อซ้ำ: {$data['name']}");
            }
            return BF::result(false, $e->getMessage());
        }
        if($status === NULL) {
            return BF::result(false, 'failed!');
        }
        return BF::result(true, ['action' => 'create', 'id' => $status->id]);
    }
    
    public function show($id)
    {
    }
    
    public function edit($id)
    {
    }
    
    public function update($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $data = Input::all();
        $data = array_diff_key($data, array_flip(['id','_method','deleted_at','deleted_by','updated_at','created_at']));
        try {
            $role = Role::find($id);
            //$data["updated_by"] = Session::get('user_id');
            $role->update($data);
            $role->permissions()->sync($data['permissions']);
        } catch ( \Illuminate\Database\QueryException $e) {
            if($e->getCode() == 23000) {
                return BF::result(false, "ชื่อซ้ำ: {$data['name']}");
            }
            return BF::result(false, $e->getMessage());
        }
        return BF::result(true, ['action' => 'update', 'id' => $id]);
    }
    
    public function destroy($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $data = Role::find($id);
        if (is_null($data)) {
            Role::withTrashed()->whereId($id)->first()->restore();
            return BF::result(true, ['action' => 'restore', 'id' => $id]);
        }else{
            $data->delete();
            return BF::result(true, ['action' => 'delete', 'id' => $id]);
        }
    }
}
