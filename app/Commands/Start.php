<?php

namespace App\Commands;

use App\Commands\Registration\SetUserName;
use App\Services\Status\UserStatusService;

class Start extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::NEW) {
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['start_message']);
            $this->triggerCommand(SetUserName::class);
        } elseif ($this->user->status === UserStatusService::DONE) {
            $this->triggerCommand(MainMenu::class);
        }
    }

}

