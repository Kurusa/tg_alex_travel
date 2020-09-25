<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Models\RecordSearch;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class ArrivalStreetNumber extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::SEARCH_ARRIVAL_STREET_NUMBER) {
            if ($this->update->getMessage()->getText() !== $this->text['skip']) {
                RecordSearch::where('user_id', $this->user->id)->update([
                    'arrival_street_number' => $this->update->getMessage()->getText(),
                ]);
            }
            $this->triggerCommand(DepartureCity::class);
        } else {
            $this->user->status = UserStatusService::SEARCH_ARRIVAL_STREET_NUMBER;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['arrival_street_number'], new ReplyKeyboardMarkup([
                [$this->text['skip'], $this->text['back']], [$this->text['cancel']]
            ], false, true));
        }
    }

}