<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class AskDetails extends BaseCommand
{

    function processCommand($par = false)
    {
        $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_details'], new ReplyKeyboardMarkup([
            [$this->text['now']], [$this->text['later']]
        ], false, true));
    }
}