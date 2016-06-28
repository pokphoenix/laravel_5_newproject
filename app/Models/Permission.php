<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Updater;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model {

    use Updater, SoftDeletes;

    protected $table = 'permissions';
    public $timestamps = false;

    protected $fillable = array(
        'name'
    );

    public function role() {
        return $this->belongsToMany('App\Models\Role','perm_role');
    }

}