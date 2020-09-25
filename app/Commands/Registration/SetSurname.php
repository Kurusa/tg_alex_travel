<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\Profile;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetSurname extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::SURNAME || $this->user->status == UserStatusService::EDIT_SURNAME) {
            $this->user->surname = $this->update->getMessage()->getText();
            $this->user->save();
            $this->user->status == UserStatusService::EDIT_SURNAME ? $this->triggerCommand(Profile::class) : $this->triggerCommand(SetAge::class);
        } else {
            $this->user->status = $this->user->status == UserStatusService::DONE ? UserStatusService::EDIT_SURNAME : UserStatusService::SURNAME;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_surname'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}