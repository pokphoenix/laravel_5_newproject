<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTables extends Migration
{
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name') ;
            $table->string('email')->unique();
            $table->string('f_name');
            $table->string('l_name');
            $table->string('phone', 20);
            $table->string('phone_2', 20)->nullable();
            $table->string('password', 60);
            $table->integer('role_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->string('lang', 2)->default('th');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('permissions', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
        });
        Schema::create('roles', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->timestamps();
        });
        Schema::create('perm_role', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();
        });
        Schema::create('branches', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('email', 100);
            $table->string('phone', 20);
            $table->string('phone_2', 20);
            $table->string('fax', 20);
            $table->text('address');
            $table->text('desc');
            $table->timestamps() ;
        });
        Schema::create('user_type', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
        });
    }

    public function down()
    {
        Schema::drop('users');
        Schema::drop('permissions');
        Schema::drop('roles');
        Schema::drop('perm_role');
        Schema::drop('branches');
        Schema::drop('user_type');
    }
}
