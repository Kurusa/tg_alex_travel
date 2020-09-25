<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\Profile;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

class SetHobby extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::HOBBY || $this->user->status == UserStatusService::EDIT_HOBBY) {
            if (strlen($this->update->getMessage()->getText()) <= 150) {
                $this->user->hobby = $this->update->getMessage()->getText();
                $this->user->save();
                $this->user->status == UserStatusService::EDIT_HOBBY ? $this->triggerCommand(Profile::class) : $this->triggerCommand(SetPhoto::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['too_much_symbols']);
            }
        } else {
            $this->user->status = $this->user->status == UserStatusService::DONE ? UserStatusService::EDIT_HOBBY : UserStatusService::HOBBY;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_user_hobby'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}