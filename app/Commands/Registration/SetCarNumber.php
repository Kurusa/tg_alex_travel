<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Models\UserCar;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetCarNumber extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::CAR_NUMBER) {
            UserCar::where('status', 'NEW')->where('user_id', $this->user->id)->update([
                'number' => $this->update->getMessage()->getText()
            ]);

            $this->triggerCommand(SetCarColor::class);
        } else {
            $this->user->status = UserStatusService::CAR_NUMBER;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['car_number'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}