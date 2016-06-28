<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermRole extends Model {

    protected $table = 'perm_role';

    protected $fillable = array(
        'role_id',
        'permission_id',
    );
    public static function DeletePermRole($id){
        static::where('role_id', '=', $id)->delete();
    }
}