<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Updater;
use Illuminate\Database\Eloquent\SoftDeletes;
use VG;
class Branch extends Model {

    use Updater, SoftDeletes;

    protected $table = 'branches';
    public $timestamps = true ;
    protected $fillable = array(
        'name',
        'email',
        'phone',
        'fax',
        'address',
        'distinct',
        'amphur',
        'province',
        'country_id',
        'zipcode',
        'desc',
    );

}