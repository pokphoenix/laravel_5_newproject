<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Updater;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model {

    use Updater, SoftDeletes;

    protected $table = 'roles';
    public $timestamps = true;
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = array(
        'name',
    );

    public function permissions() {
        return $this->belongsToMany('App\Models\Permission','perm_role');
    }
    
}