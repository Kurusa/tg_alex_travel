<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetPhoneNumber extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::PHONE_NUMBER) {
            if ($this->update->getMessage()->getContact()) {
                $this->user->phone_number = $this->update->getMessage()->getContact()->getPhoneNumber();
            } else {
                $this->user->phone_number = $this->update->getMessage()->getText();
            }
            $this->user->save();

            $this->triggerCommand(AskDetails::class);
        } else {
            $this->user->status = UserStatusService::PHONE_NUMBER;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_phone'], new ReplyKeyboardMarkup([
                [['text' => $this->text['click'], 'request_contact' => true]],
                [['text' => $this->text['back']]],
            ], false, true));
        }
    }

}