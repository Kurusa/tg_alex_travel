<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class ArrivalTime extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::ARRIVAL_TIME) {
            Record::where('user_id', $this->user->id)->where('status', 'NEW')->update([
                'time' => $this->update->getMessage()->getText()
            ]);
            $this->triggerCommand(Price::class);
        } else {
            $this->user->status = UserStatusService::ARRIVAL_TIME;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['arrival_time'] . '<code>' . date('H:i') . '</code>', new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}