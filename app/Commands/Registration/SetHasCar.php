<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\UserCar;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetHasCar extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::HAS_CAR) {
            if ($this->update->getMessage()->getText() == $this->text['yes']) {
                $this->triggerCommand(SetCarBrand::class);
            } else {
                $this->triggerCommand(MainMenu::class);
            }
        } else {
            if ($this->update->getMessage()->getText() == $this->text['back']) {
                UserCar::where('user_id', $this->user->id)->delete();
            }

            $this->user->status = UserStatusService::HAS_CAR;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_user_car'], new ReplyKeyboardMarkup([
                [$this->text['yes']], [$this->text['no']],
                [$this->text['back']]
            ], false, true));
        }
    }

}