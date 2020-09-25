<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Models\UserCar;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

class SetCarBrand extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::CAR_BRAND) {
            UserCar::create([
                'user_id' => $this->user->id,
                'brand' => $this->update->getMessage()->getText()
            ]);
            $this->triggerCommand(SetCarModel::class);
        } else {
            $this->user->status = UserStatusService::CAR_BRAND;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['car_brand'], new ReplyKeyboardRemove());
        }
    }

}