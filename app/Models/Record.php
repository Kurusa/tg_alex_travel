<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{

    protected $table = 'record';
    protected $fillable = ['user_id', 'date', 'time', 'price', 'spots', 'free_spots', 'description', 'status'];
    public $with = ['location', 'user'];

    public function location()
    {
        return $this->hasOne(RecordLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}