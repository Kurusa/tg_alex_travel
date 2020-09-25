<?php

namespace App\Models;

use App\Commands\SearchRecord\SearchRecord;
use Illuminate\Database\Eloquent\Model;

class RecordSearch extends Model
{

    protected $table = 'record_search';
    protected $fillable = ['user_id', 'arrival_city_id', 'arrival_street_id', 'departure_city_id', 'departure_street_id', 'date', 'time', 'spots',
        'arrival_street_number', 'departure_street_number'
    ];

}