<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\RecordSearch;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Spots extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::SEARCH_RECORD_SPOTS) {
            $record = RecordSearch::where('user_id', $this->user->id)->first();
            $record->spots = intval($this->update->getMessage()->getText());
            $record->save();

            $this->triggerCommand(SearchRecord::class);
        } else {
            $this->user->status = UserStatusService::SEARCH_RECORD_SPOTS;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['record_spots'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}