<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\RecordLocation;
use App\Services\NovaPoshtaApi;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class DepartureStreetNumber extends BaseCommand
{

    function processCommand()
    {
        $record = Record::where('user_id', $this->user->id)->where('status', 'NEW')->first();
        if ($this->user->status == UserStatusService::DEPARTURE_STREET_NUMBER) {
            if ($this->update->getMessage()->getText() !== $this->text['skip']) {
                RecordLocation::where('record_id', $record->id)->update([
                    'departure_street_number' => $this->update->getMessage()->getText(),
                ]);
            }
            $this->triggerCommand(ArrivalDate::class);
        } else {
            $this->user->status = UserStatusService::DEPARTURE_STREET_NUMBER;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['departure_street_number'], new ReplyKeyboardMarkup([
                [$this->text['skip'], $this->text['back']], [$this->text['cancel']]
            ], false, true));
        }
    }

}