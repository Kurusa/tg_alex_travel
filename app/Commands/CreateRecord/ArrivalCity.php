<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\RecordLocation;
use App\Services\NovaPoshtaApi;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class ArrivalCity extends BaseCommand
{

    function processCommand()
    {
        $google = new NovaPoshtaApi();
        if ($this->user->status == UserStatusService::ARRIVAL && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            $city_list = $google->getCities($this->update->getMessage()->getText());
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

                $this->user->status = UserStatusService::ARRIVAL_SELECTING;
                $this->user->save();
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['this_city'], new ReplyKeyboardMarkup($buttons, false, true));
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
            }
        } elseif ($this->user->status === UserStatusService::ARRIVAL_SELECTING && $this->update->getMessage()->getText() !== $this->text['wrong_city']) {
            $city = $google->getCities($this->update->getMessage()->getText());
            if ($city['data'][0]['Addresses'][0]['Ref']) {
                $record = Record::create([
                    'user_id' => $this->user->id,
                ]);
                RecordLocation::create([
                    'record_id' => $record->id,
                    'arrival_city_id' => $city['data'][0]['Addresses'][0]['Ref'],
                    'arrival_city_name' => $city['data'][0]['Addresses'][0]['MainDescription'],
                ]);
                $this->triggerCommand(ArrivalStreet::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['cant_find']);
            }
        } else {
            $this->user->status = UserStatusService::ARRIVAL;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['arrival_city'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}