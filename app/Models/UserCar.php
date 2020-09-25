<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCar extends Model {

    protected $table = 'user_car';
    protected $fillable = ['user_id', 'model', 'color', 'number', 'status', 'verified', 'verify_image'];
    const UPDATED_AT = null;

}