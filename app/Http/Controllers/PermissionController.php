<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Input;
use BF;

class PermissionController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
    }

    public function store()
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $data = Permission::find($id);
        if (is_null($data)) {
            Permission::withTrashed()->whereId($id)->first()->restore();
            return BF::result(true, ['action' => 'restore', 'id' => $id]);
        }else{
            $data->delete();
            return BF::result(true, ['action' => 'delete', 'id' => $id]);
        }
    }
}
