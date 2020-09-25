<?php

namespace App\Commands;

use App\Commands\CreateRecord\ArrivalCity;
use App\Commands\CreateRecord\ArrivalStreet;
use App\Commands\CreateRecord\DepartureCity;
use App\Commands\Registration\AskDetails;
use App\Commands\Registration\SetAboutMe;
use App\Commands\Registration\SetAge;
use App\Commands\Registration\SetCarBrand;
use App\Commands\Registration\SetCarModel;
use App\Commands\Registration\SetCarNumber;
use App\Commands\Registration\SetHasCar;
use App\Commands\Registration\SetHobby;
use App\Commands\Registration\SetPhoto;
use App\Commands\Registration\SetSurname;
use App\Commands\Registration\SetUserName;
use App\Services\Status\UserStatusService;

class Back extends BaseCommand
{

    function processCommand()
    {
        switch ($this->user->status) {
            case UserStatusService::EDIT_USER_NAME:
            case UserStatusService::EDIT_SURNAME:
            case UserStatusService::EDIT_AGE:
            case UserStatusService::EDIT_ABOUT_ME:
            case UserStatusService::EDIT_HOBBY:
            case UserStatusService::EDIT_PHOTO:
                $this->triggerCommand(MainMenu::class);
                break;
            case UserStatusService::SURNAME:
                $this->triggerCommand(SetUserName::class);
                break;
            case UserStatusService::AGE:
                $this->triggerCommand(SetSurname::class);
                break;
            case UserStatusService::PHONE_NUMBER:
                $this->triggerCommand(SetAge::class);
                break;
            case UserStatusService::ABOUT_ME:
                $this->triggerCommand(AskDetails::class);
                break;
            case UserStatusService::HOBBY:
                $this->triggerCommand(SetAboutMe::class);
                break;
            case UserStatusService::PHOTO:
                $this->triggerCommand(SetHobby::class);
                break;
            case UserStatusService::HAS_CAR:
                $this->triggerCommand(SetPhoto::class);
                break;
            case UserStatusService::CAR_BRAND:
                $this->triggerCommand(SetHasCar::class);
                break;
            case UserStatusService::CAR_MODEL:
                $this->triggerCommand(SetCarBrand::class);
                break;
            case UserStatusService::CAR_NUMBER:
                $this->triggerCommand(SetCarModel::class);
                break;
            case UserStatusService::CAR_COLOR:
                $this->triggerCommand(SetCarNumber::class);
                break;


            case UserStatusService::ARRIVAL_STREET:
                $this->triggerCommand(ArrivalCity::class);
                break;
            case UserStatusService::DEPARTURE:
                $this->triggerCommand(ArrivalStreet::class);
                break;
            case UserStatusService::DEPARTURE_STREET:
                $this->triggerCommand(DepartureCity::class);
                break;
        }
    }

}