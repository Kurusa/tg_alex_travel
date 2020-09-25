<?php
return [
    'record' => \App\Commands\RecordInfo\RecordInfo::class,
    'book_spot' => \App\Commands\RecordInfo\BookSpot::class,
    'spot_select' => \App\Commands\RecordInfo\BookSpot::class,
    'user_details' => \App\Commands\RecordInfo\UserDetails::class,

    'edit_first_name' => \App\Commands\Registration\SetUserName::class,
    'edit_surname' => \App\Commands\Registration\SetSurname::class,
    'edit_age' => \App\Commands\Registration\SetAge::class,
    'edit_about_me' => \App\Commands\Registration\SetAboutMe::class,
    'edit_photo' => \App\Commands\Registration\SetPhoto::class,
    'edit_hobby' => \App\Commands\Registration\SetHobby::class,

    'done_verify_profile' => \App\Commands\Verify\VerifyProfileResult::class,
    'decline_verify' => \App\Commands\Verify\VerifyProfileResult::class,
    'done_verify_car' => \App\Commands\Verify\VerifyCarResult::class,
    'decline_verify_car' => \App\Commands\Verify\VerifyCarResult::class,
];