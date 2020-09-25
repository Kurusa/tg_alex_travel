<?php

namespace App\Commands\Verify;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class   VerifyProfile extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::VERIFY_PROFILE) {
            if ($this->update->getMessage()->getText()) {
                $this->triggerCommand(MainMenu::class);
            } else {
                $file_id = $this->update->getMessage()->getPhoto()[0]->getFileId();
                $chat_id_list = explode(',', env('ADMIN_CHAT_ID'));
                $caption = '<a href="tg://user?id=' . $this->user->chat_id . '">' . $this->user->first_name . '</a>';
                $inline_buttons =  new InlineKeyboardMarkup([
                    [[
                        'text' => $this->text['verify_profile'],
                        'callback_data' => json_encode([
                            'a' => 'done_verify_profile',
                            'id' => $this->user->chat_id
                        ])
                    ]],
                    [[
                        'text' => $this->text['decline_verify_profile'],
                        'callback_data' => json_encode([
                            'a' => 'decline_verify',
                            'id' => $this->user->chat_id
                        ])
                    ]]
                ]);

                foreach ($chat_id_list as $admin_chat_id) {
                    $this->getBot()->sendPhoto($admin_chat_id, $file_id, $caption, null, $inline_buttons, false, 'html');
                }
                $this->triggerCommand(MainMenu::class);
            }
            
        } else {
            $this->user->status = UserStatusService::VERIFY_PROFILE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['send_profile_photo'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));

        }
    }

}