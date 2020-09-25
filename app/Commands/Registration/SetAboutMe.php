<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\Profile;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

class SetAboutMe extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::ABOUT_ME || $this->user->status == UserStatusService::EDIT_ABOUT_ME) {
            if (strlen($this->update->getMessage()->getText()) <= 150) {
                $this->user->about_me = $this->update->getMessage()->getText();
                $this->user->save();
                $this->user->status == UserStatusService::EDIT_ABOUT_ME ? $this->triggerCommand(Profile::class) : $this->triggerCommand(SetHobby::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['too_much_symbols']);
            }
        } else {
            $this->user->status = $this->user->status == UserStatusService::DONE ? UserStatusService::EDIT_ABOUT_ME : UserStatusService::ABOUT_ME;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_user_about'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}