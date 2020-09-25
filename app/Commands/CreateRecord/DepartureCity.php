<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\RecordLocation;
use App\Services\NovaPoshtaApi;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class DepartureCity extends BaseCommand
{

    function processCommand()
    {
        $poshta = new NovaPoshtaApi();
        $record = Record::where('user_id', $this->user->id)->where('status', 'NEW')->first();
        if ($this->user->status == UserStatusService::DEPARTURE && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            $city_list = $poshta->getCities($this->update->getMessage()->getText());
            if ($city_list['success'] && $city_list['data'][0]['TotalCount']) {
                $buttons = [];
                $pre_buttons = [];
                foreach ($city_list['data'][0]['Addresses'] as $city) {
                    if (!in_array($city['MainDescription'], $pre_buttons)) {
                        $pre_buttons[] = $city['MainDescription'];
                    }
                }

                foreach ($pre_buttons as $pre_button) {
                    $buttons[] = [$pre_button];
                }
                $buttons[] = [$this->text['wrong_city']];

                $this->user->status = UserStatusService::DEPARTURE_SELECTING;
                $this->user->save();
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['this_city'], new ReplyKeyboardMarkup($buttons, false, true));
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
            }
        } elseif ($this->user->status === UserStatusService::DEPARTURE_SELECTING && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            $city = $poshta->getCities($this->update->getMessage()->getText());
            if ($city['data'][0]['Addresses'][0]['Ref']) {
                RecordLocation::where('record_id', $record->id)->update([
                    'departure_city_id' => $city['data'][0]['Addresses'][0]['Ref'],
                    'departure_city_name' => $city['data'][0]['Addresses'][0]['MainDescription'],
                ]);
                $this->triggerCommand(DepartureStreet::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
            }
        } else {
            $this->user->status = UserStatusService::DEPARTURE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['departure_city'], new ReplyKeyboardMarkup([
                [$this->text['back']], [$this->text['cancel']]
            ], false, true));
        }
    }

}