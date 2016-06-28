<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'role_id',
        'group_id',
        'branch_id',
        'phone',
        'phone_2',
        'f_name',
        'lang',
        'l_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
//        'password',
        'remember_token'
    ];

    protected $dates = ['created_at', 'updated_at'];
    
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

}
