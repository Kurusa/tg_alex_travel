<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Models\RecordSearch;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class ArrivalDate extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::SEARCH_ARRIVAL_DATE) {
            $date = explode('.', $this->update->getMessage()->getText());
            if (checkdate($date[1], $date[0], date('Y'))) {
                RecordSearch::where('user_id', $this->user->id)->update([
                    'date' => $this->update->getMessage()->getText()
                ]);
                $this->triggerCommand(ArrivalTime::class);
            } else {
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['wrong_date']);
            }
        } else {
            $this->user->status = UserStatusService::SEARCH_ARRIVAL_DATE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['arrival_date'] . '<code>' . date('d.m') . '</code>', new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}