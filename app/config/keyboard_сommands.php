<?php
return [
    'back' => \App\Commands\Back::class,
    'now' => \App\Commands\Registration\SetAboutMe::class,
    'later' => \App\Commands\MainMenu::class,
    'yes' => \App\Commands\Registration\SetHasCar::class,
    'no' => \App\Commands\Registration\SetHasCar::class,
    'create_record' => \App\Commands\CreateRecord\ArrivalCity::class,
    'cancel' => \App\Commands\MainMenu::class,
    'search_record' => \App\Commands\SearchRecord\ArrivalCity::class,
    'profile' => \App\Commands\Profile::class,
    'verify_profile' => \App\Commands\Verify\VerifyProfile::class,
    'verify_car' => \App\Commands\Verify\VerifyCarData::class,
];

