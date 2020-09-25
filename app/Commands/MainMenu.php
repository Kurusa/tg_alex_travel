<?php

namespace App\Commands;

use App\Models\Record;
use App\Models\RecordLocation;
use App\Models\RecordSearch;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class MainMenu extends BaseCommand
{

    function processCommand($text = false)
    {
        $possible_undone_record = Record::where('user_id', $this->user->id)->where('status', 'NEW')->first();
        if ($possible_undone_record) {
            RecordLocation::where('record_id', $possible_undone_record->id)->delete();
            $possible_undone_record->delete();
        }

        $possible_undone_search = RecordSearch::where('user_id', $this->user->id)->first();
        if ($possible_undone_search) {
            $possible_undone_search->delete();
        }

        $this->user->status = UserStatusService::DONE;
        $this->user->save();
        $buttons = [
            [$this->text['create_record'], $this->text['search_record']],
            [$this->text['profile']]
        ];
        if ($this->user->verified == 0) {
            $buttons[] = [
                $this->text['verify_profile']
            ];
        }
        if ($this->user->car && $this->user->car->verified == 0) {
            $buttons[] = [
                $this->text['verify_car']
            ];
        }


        $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['main_menu'], new ReplyKeyboardMarkup($buttons, false, true));
    }

}