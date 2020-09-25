<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Price extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::RECORD_PRICE) {
            Record::where('user_id', $this->user->id)->where('status', 'NEW')->update([
                'price' => intval($this->update->getMessage()->getText())
            ]);
            $this->triggerCommand(Spots::class);
        } else {
            $this->user->status = UserStatusService::RECORD_PRICE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['record_price'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}