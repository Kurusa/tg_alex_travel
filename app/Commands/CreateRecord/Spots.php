<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Spots extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::RECORD_SPOTS) {
            Record::where('user_id', $this->user->id)->where('status', 'NEW')->update([
                'spots' => intval($this->update->getMessage()->getText()),
                'free_spots' => intval($this->update->getMessage()->getText()),
            ]);
            $this->triggerCommand(Description::class);
        } else {
            $this->user->status = UserStatusService::RECORD_SPOTS;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['record_spots'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}