<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\RecordSearch;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class ArrivalTime extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::SEARCH_ARRIVAL_TIME) {
            RecordSearch::where('user_id', $this->user->id)->update([
                'time' => $this->update->getMessage()->getText()
            ]);
            $this->triggerCommand(Spots::class);
        } else {
            $this->user->status = UserStatusService::SEARCH_ARRIVAL_TIME;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['arrival_time'] . '<code>' . date('H:i') . '</code>', new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}