<?php

namespace App\Commands\Verify;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\UserCar;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class VerifyCarData extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::VERIFY_CAR_DATA) {
            if ($this->update->getMessage()->getText()) {
                $this->triggerCommand(MainMenu::class);
            } else {
                $file_id = $this->update->getMessage()->getPhoto()[0]->getFileId();
                UserCar::where('user_id', $this->user->id)->update([
                    'verify_image' => $file_id
                ]);
                $this->triggerCommand(VerifyCarPhotoIn::class);
            }
            
        } else {
            $this->user->status = UserStatusService::VERIFY_CAR_DATA;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['send_car_photo'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));

        }
    }

}