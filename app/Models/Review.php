<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    protected $table = 'review';
    protected $fillable = ['user_id', 'reviewed_user_id'];
    const UPDATED_AT = null;

}