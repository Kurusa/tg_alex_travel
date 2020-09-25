<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\Record;
use App\Services\Status\UserStatusService;
use Carbon\Carbon;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Description extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::RECORD_DESCRIPTION) {
            $record = Record::where('user_id', $this->user->id)->where('status', 'NEW')->first();

            if ($this->update->getMessage()->getText() !== $this->text['skip']) {
                $record->description = $this->update->getMessage()->getText();
            }
            $record->status = 'DONE';
            $record->save();
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['done']);
            $this->triggerCommand(MainMenu::class);
        } else {
            $this->user->status = UserStatusService::RECORD_DESCRIPTION;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['record_description'], new ReplyKeyboardMarkup([
                [$this->text['skip']]
            ], false, true));
        }
    }

}