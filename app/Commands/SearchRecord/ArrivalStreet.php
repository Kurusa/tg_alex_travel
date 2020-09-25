<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Models\RecordSearch;
use App\Services\NovaPoshtaApi;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class ArrivalStreet extends BaseCommand
{

    function processCommand()
    {
        $poshta = new NovaPoshtaApi();
        $record = RecordSearch::where('user_id', $this->user->id)->first();
        if ($this->user->status == UserStatusService::SEARCH_ARRIVAL_STREET && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            if ($this->update->getMessage()->getText() == $this->text['skip']) {
                $this->triggerCommand(DepartureCity::class);
            } else {
                $street_list = $poshta->getStreetByRef($record->arrival_city_id, $this->update->getMessage()->getText());
                if ($street_list['success'] && $street_list['data'][0]['TotalCount'] >= 1) {
                    $buttons = [];
                    foreach ($street_list['data'][0]['Addresses'] as $street) {
                        $buttons[] = [$street['Present']];
                    }
                    $buttons[] = [$this->text['wrong_city']];

                    $this->user->status = UserStatusService::SEARCH_ARRIVAL_STREET_SELECTING;
                    $this->user->save();
                    $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['this_street'], new ReplyKeyboardMarkup($buttons, false, true));
                } else {
                    $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
                }
            }
        } elseif ($this->user->status === UserStatusService::SEARCH_ARRIVAL_STREET_SELECTING && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            $street = $poshta->getStreetByRef($record->arrival_city_id, $this->update->getMessage()->getText());
            if ($street['data'][0]['Addresses'][0]['SettlementStreetRef']) {
                RecordSearch::where('user_id', $this->user->id)->update([
                    'arrival_street_id' => $street['data'][0]['Addresses'][0]['SettlementStreetRef'],
                ]);
                $this->triggerCommand(ArrivalStreetNumber::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
            }
        } else {
            $this->user->status = UserStatusService::SEARCH_ARRIVAL_STREET;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['arrival_street'], new ReplyKeyboardMarkup([
                [$this->text['skip']], [$this->text['cancel']]
            ], false, true));
        }
    }

}