<?php

namespace App\Commands\Registration;

use App\Commands\BaseCommand;
use App\Commands\Profile;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SetPhoto extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::PHOTO || $this->user->status == UserStatusService::EDIT_PHOTO) {
            $file_id = $this->update->getMessage()->getPhoto()[0]->getFileId();
            if ($file_id) {
                $this->user->photo = $file_id;
                $this->user->save();
                $this->user->status == UserStatusService::EDIT_PHOTO ? $this->triggerCommand(Profile::class) : $this->triggerCommand(SetHasCar::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['something_went_wrong']);
            }
        } else {
            $this->user->status = $this->user->status == UserStatusService::DONE ? UserStatusService::EDIT_PHOTO : UserStatusService::PHOTO;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['ask_user_photo'], new ReplyKeyboardMarkup([
                [$this->text['back']]
            ], false, true));
        }
    }

}