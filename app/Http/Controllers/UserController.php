<?php

namespace App\Http\Controllers;

use App\Http\Requests;

use App\Models\Branch;
use App\Models\Role;
use App\Models\UserType;
use App\User;
use Input;
use BF;
class UserController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
        $initData = [];
        $data =  array_merge([
            'roles' => Role::all(),
            'usertypes' => UserType::all(),
            'branches' => Branch::all(),
        ], $initData);
        return BF::result(true, ['action' => 'create', 'data' => $data]);
    }

    public function store()
    {
        $data = Input::all();
        if ($data["password"] != $data["confirm_password"] ){
            return BF::result(false, 'กรุณากรอกพาสเวิดให้ตรงกันค่ะ!');
        }
        if(empty($data["email"])){
            return BF::result(false, 'กรุณากรอกอีเมล์ค่ะ!');
        }
        try {
            $chk = User::where('email', $data["email"])->first();
            if(isset($chk)){
                return BF::result(false, 'failed!'); //--- check email repeat
            }
        } catch ( \Illuminate\Database\QueryException $e) {
            if($e->getCode() == 23000) {
                return BF::result(false, "อีเมล์ซ้ำ: {$data['email']}");
            }
            return BF::result(false, $e->getMessage());
        }

        $data["password"] = bcrypt($data["password"]) ;
        $data = array_diff_key($data, array_flip(['id','_method','deleted_at','deleted_by','updated_at','created_at']));
        //$data["created_by"] = Session::get('user_id');
        try {
            $status = User::create($data);
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
        $user = User::find($id);
        $user->password = '';
        return BF::result(true, ['action' => 'edit', 'data' => $user]);
    }

    public function update($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $data = Input::all();
        $data = array_diff_key($data, array_flip(['id','confirm_password', '_method','deleted_at','deleted_by','updated_at','created_at']));

        if(isset($data["change_pass"]) && $data["change_pass"] == true) {
            if (!empty($data["password"])) {
                $data["password"] = \Hash::make($data["password"]);
            } else {
                unset($data["password"]);
            }
        } else {
            unset($data["password"]);
        }
        unset($data["change_pass"]);

        $data["updated_by"] = Session::get('user_id');
        try {
            $status = User::whereId($id)->update($data);
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
        $data = User::find($id);
        if (is_null($data)) {
            User::withTrashed()->whereId($id)->first()->restore();
            return BF::result(true, ['action' => 'restore', 'id' => $id]);
        }else{
            $data->delete();
            return BF::result(true, ['action' => 'delete', 'id' => $id]);
        }
    }

    public function duplicate($id)
    {
        if(empty($id)){
            return BF::result(false, 'ไม่พบข้อมูลนี้ค่ะ');
        }
        $user = User::find($id);
        if(is_null($user)) return BF::result(false, trans('error.not_found', ['id', $id]));
        try {
            $copy = $user->replicate();
            $email = 'copy_'.$copy->email;
            while(User::whereEmail($email)->count() > 0) {
                $email = 'copy_'.$email;
            }
            $copy->email = $email;
            $copy->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return BF::result(false, $e->errorInfo);
        }
        return BF::result(true, ['redirect' => '/app/users']);
    }

    public function postLogin()
    {
        $data = Input::all();
        $chkToken =  BF::checktoken($data['timestamp'],$data['token']) ;
        if(!$chkToken){
            return BF::result(false, 'token invalid');
        }
        try {
            if (empty($data['email'])) {
                return BF::result(false, 'ไม่พบ email');
            }
            $status = User::where('email', $data['email'])->first();
            if ($status === NULL ) {
                return BF::result(false, 'ไม่พบ email นี้ในระบบ');
            }
            if ($data['password'] != $status->password) {
                return BF::result(false, "กรุณากรอก Password ให้ถูกต้องค่ะ");
            }
            return BF::result(true, ['action' => 'create', 'name' => $status->name]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return BF::result(false, "email นี้มีในระบบอยู่แล้วค่ะ");
            }
            return BF::result(false, $e->getMessage());
        }
    }

    public function postChangePass()
    {
        $data = Input::all();
        $chkToken =  BF::checktoken($data['timestamp'],$data['token']) ;
        if(!$chkToken){
            return BF::result(false, 'token invalid');
        }

        if (empty($data['newPassword'])) {
            return BF::result(false, 'ไม่พบ รหัสผ่าน');
        }
        if (empty($data['newPasswordConfirm'])) {
            return BF::result(false, 'ไม่พบ ยืนยันรหัสผ่าน');
        }
        if (empty($data['email'])) {
            return BF::result(false, 'ไม่พบ อีเมล์');
        }

        try {
            if ($data['newPassword'] != $data['newPasswordConfirm']) {
                return BF::result(false, "กรุณากรอกพาสเวิดให้ตรงกัน");
            }
            $data['password'] = $data['newPassword'] ;

            $status = User::where('email', $data['email'])->first();
            if ($status === NULL) {
                return BF::result(false, 'ไม่พบ email นี้ในระบบ');
            }
            if ($data['oldPassword'] != $status->password) {
                return BF::result(false, "กรุณากรอก Old Password ให้ถูกต้องค่ะ");
            }
            unset($data["oldPassword"]);
            unset($data["newPassword"]);
            unset($data["newPasswordConfirm"]);
            unset($data["timestamp"]);
            unset($data["token"]);
            $statusUpdate = User::whereId($status->id)->update($data);
            if ($statusUpdate === NULL) {
                return BF::result(false, 'อัพเดทข้อมูลไม่สำเร็จ');
            }
            return BF::result(true, ['action' => 'create']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return BF::result(false, "email นี้มีในระบบอยู่แล้วค่ะ");
            }
            return BF::result(false, $e->getMessage());
        }
    }

    public function postForgotPass()
    {
        $data = Input::all();

        $chkToken =  BF::checktoken($data['timestamp'],$data['token']) ;
        if(!$chkToken){
            return BF::result(false, 'token invalid');
        }
        if (empty($data['email'])) {
            return BF::result(false, 'ไม่พบ อีเมล์');
        }
        try {

            $data['newPassword'] = User::randomPassword();
            $data['password'] = sha1($data['newPassword']);
            $status = User::where('email', $data['email'])->first();
            if ($status === NULL) {
                return BF::result(false, 'ไม่พบ email นี้ในระบบ');
            }

            Mail::send('emails.forgotpassword', ['newpassword' => $data['newPassword']], function ($message) use ($data) {
                $message->subject('Fotgot Password!');
                $message->from('app.semicolon@gmail.com', 'Masterpiece Clinic');
                $message->to($data['email']); // Recipient address
            });

            if (count(Mail::failures()) > 0) {
                return BF::result(false, 'ส่งเมล์ไม่สำเร็จ');
            } else {

                unset($data["newPassword"]);
                unset($data["timestamp"]);
                unset($data["token"]);

                $statusUpdate = User::whereId($status->id)->update($data);
                if ($statusUpdate === NULL) {
                    return BF::result(false, 'อัพเดทข้อมูลไม่สำเร็จ');
                }
                return BF::result(true, ['action' => 'update']);
            }


        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return BF::result(false, "email นี้มีในระบบอยู่แล้วค่ะ");
            }
            return BF::result(false, $e->getMessage());
        }
    }

}
