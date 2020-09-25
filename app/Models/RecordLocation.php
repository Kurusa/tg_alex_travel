<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordLocation extends Model
{

    protected $table = 'record_location';
    protected $fillable = ['record_id', 'arrival_city_name', 'arrival_city_id', 'arrival_street_id', 'arrival_coo', 'departure_city_name', 'departure_city_id', 'departure_street_id', 'departure_coo',
        'arrival_street_number', 'departure_street_number'
    ];

}