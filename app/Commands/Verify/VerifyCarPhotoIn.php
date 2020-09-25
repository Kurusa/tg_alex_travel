<?php

namespace App\Commands\Verify;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class VerifyCarPhotoIn extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::VERIFY_CAR_PHOTO_IN) {
            if ($this->update->getMessage()->getText()) {
                $this->triggerCommand(MainMenu::class);
            } else {
                $file_id = $this->update->getMessage()->getPhoto()[0]->getFileId();
                $chat_id_list = explode(',', env('ADMIN_CHAT_ID'));
                $caption = '<a href="tg://user?id=' . $this->user->chat_id . '">' . $this->user->first_name . '</a>';
                $inline_buttons = new InlineKeyboardMarkup([
                    [[
                        'text' => $this->text['verify_car'],
                        'callback_data' => json_encode([
                            'a' => 'done_verify_car',
                            'id' => $this->user->chat_id
                        ])
                    ]],
                    [[
                        'text' => $this->text['decline_verify_profile'],
                        'callback_data' => json_encode([
                            'a' => 'decline_verify_car',
                            'id' => $this->user->chat_id
                        ])
                    ]]
                ]);
                $media = new \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia();
                $media->addItem(new InputMediaPhoto($this->user->car->verify_image, $caption, 'html'));
                $media->addItem(new InputMediaPhoto($file_id, $caption, 'html'));
                foreach ($chat_id_list as $admin_chat_id) {
                    $this->getBot()->sendMediaGroup($admin_chat_id, $media);
                    $this->getBot()->sendMessageWithKeyboard($admin_chat_id, $caption, $inline_buttons);
                }
                $this->triggerCommand(MainMenu::class);
            }
        } else {
            $this->user->status = UserStatusService::VERIFY_CAR_PHOTO_IN;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['send_car_photo_in'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));

        }
    }

}