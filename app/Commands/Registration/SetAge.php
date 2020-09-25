<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\Profile;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetAge extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::AGE || $this->user->status == UserStatusService::EDIT_AGE) {
            if (intval($this->update->getMessage()->getText()) > 5) {
                $this->user->age = $this->update->getMessage()->getText();
                $this->user->save();
                $this->user->status == UserStatusService::EDIT_AGE ? $this->triggerCommand(Profile::class) : $this->triggerCommand(SetPhoneNumber::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['wrong_age']);
            }
        } else {
            $this->user->status = $this->user->status == UserStatusService::DONE ? UserStatusService::EDIT_AGE : UserStatusService::AGE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_age'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}