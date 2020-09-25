<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\UserCar;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetCarColor extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::CAR_COLOR) {
            UserCar::where('status', 'NEW')->where('user_id', $this->user->id)->update([
                'color' => $this->update->getMessage()->getText(),
                'status' => 'DONE'
            ]);

            $this->triggerCommand(MainMenu::class);
        } else {
            $this->user->status = UserStatusService::CAR_COLOR;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['car_color'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}