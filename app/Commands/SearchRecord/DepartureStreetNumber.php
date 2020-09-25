<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Models\RecordSearch;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class DepartureStreetNumber extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::SEARCH_DEPARTURE_STREET_NUMBER) {
            if ($this->update->getMessage()->getText() !== $this->text['skip']) {
                RecordSearch::where('user_id', $this->user->id)->update([
                    'departure_street_number' => $this->update->getMessage()->getText(),
                ]);
            }
            $this->triggerCommand(ArrivalDate::class);
        } else {
            $this->user->status = UserStatusService::SEARCH_DEPARTURE_STREET_NUMBER;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['departure_street_number'], new ReplyKeyboardMarkup([
                [$this->text['skip'], $this->text['back']], [$this->text['cancel']]
            ], false, true));
        }
    }

}