<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\RecordLocation;
use App\Services\NovaPoshtaApi;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class DepartureStreet extends BaseCommand
{

    function processCommand()
    {
        $poshta = new NovaPoshtaApi();
        $record = Record::where('user_id', $this->user->id)->where('status', 'NEW')->first();
        if ($this->user->status == UserStatusService::DEPARTURE_STREET && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            if ($this->update->getMessage()->getText() == $this->text['skip']) {
                $this->triggerCommand(ArrivalDate::class);
            } else {
                $street_list = $poshta->getStreetByRef($record['location']->departure_city_id, $this->update->getMessage()->getText());
                if ($street_list['success'] && $street_list['data'][0]['TotalCount'] >= 1) {
                    $buttons = [];
                    foreach ($street_list['data'][0]['Addresses'] as $street) {
                        $buttons[] = [$street['Present']];
                    }
                    $buttons[] = [$this->text['wrong_city']];

                    $this->user->status = UserStatusService::DEPARTURE_STREET_SELECTING;
                    $this->user->save();
                    $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['this_street'], new ReplyKeyboardMarkup($buttons, false, true));
                } else {
                    $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
                }
            }
        } elseif ($this->user->status === UserStatusService::DEPARTURE_STREET_SELECTING && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            $street = $poshta->getStreetByRef($record['location']->departure_city_id, $this->update->getMessage()->getText());
            if ($street['data'][0]['Addresses'][0]['SettlementStreetRef']) {
                RecordLocation::where('record_id', $record->id)->update([
                    'departure_street_id' => $street['data'][0]['Addresses'][0]['SettlementStreetRef'],
                    'departure_coo' => json_encode($street['data'][0]['Addresses'][0]['Location'], true)
                ]);
                $this->triggerCommand(DepartureStreetNumber::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
            }
        } else {
            $this->user->status = UserStatusService::DEPARTURE_STREET;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['departure_street'], new ReplyKeyboardMarkup([
                [$this->text['skip'], $this->text['back']], [$this->text['cancel']]
            ], false, true));
        }
    }

}