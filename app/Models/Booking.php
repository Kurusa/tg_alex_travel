<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $table = 'booking';
    protected $fillable = ['user_id', 'record_id'];
    const UPDATED_AT = null;

}