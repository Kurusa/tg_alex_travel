<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Models\UserCar;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

class SetCarModel extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::CAR_MODEL) {
            UserCar::where('status', 'NEW')->where('user_id', $this->user->id)->update([
                'model' => $this->update->getMessage()->getText()
            ]);

            $this->triggerCommand(SetCarNumber::class);
        } else {
            $this->user->status = UserStatusService::CAR_MODEL;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['car_model'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}