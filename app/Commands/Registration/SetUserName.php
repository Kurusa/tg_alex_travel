<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\Profile;
use App\Services\Status\UserStatusService;

class SetUserName extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::USER_NAME || $this->user->status == UserStatusService::EDIT_USER_NAME) {
            $this->user->name = $this->update->getMessage()->getText();
            $this->user->save();
            $this->user->status == UserStatusService::EDIT_USER_NAME ? $this->triggerCommand(Profile::class) : $this->triggerCommand(SetSurname::class);
        } else {
            $this->user->status = $this->user->status == UserStatusService::DONE ? UserStatusService::EDIT_USER_NAME : UserStatusService::USER_NAME;
            $this->user->save();

            $this->getBot()->sendMessage($this->user->chat_id, $this->text['ask_user_name']);
        }
    }

}