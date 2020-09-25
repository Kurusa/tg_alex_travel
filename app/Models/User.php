<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'user';
    protected $fillable = ['chat_id', 'name', 'first_name', 'user_name', 'age', 'phone_number', 'about_me', 'hobby', 'photo', 'verified', 'status', 'lang'];
    public $with = ['car'];

    public function car()
    {
        return $this->hasOne(UserCar::class);
    }

}