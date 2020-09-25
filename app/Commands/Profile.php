<?php

namespace App\Commands;

use App\Services\Status\UserStatusService;
use App\Utils\Twig;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Profile extends BaseCommand
{

    function processCommand()
    {
        if (strpos($this->user->status, 'EDIT_') !== false) {
            $this->user->status = UserStatusService::DONE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['editing_done'], new ReplyKeyboardMarkup([
                [$this->text['create_record'], $this->text['search_record']],
                [$this->text['profile']]
            ], false, true));
        }

        $twig = Twig::getInstance();
        $template = $twig->load('user_' . strtolower($this->user->lang) . '.twig');

        $buttons = [];
        foreach ($this->text as $key => $item) {
            if (strpos($key, 'edit_') !== false) {
                $buttons[] = [[
                    'text' => $item,
                    'callback_data' => json_encode([
                        'a' => $key
                    ])
                ]];
            }
        }
        if ($this->user->photo) {
            $this->getBot()->sendPhoto($this->user->chat_id, $this->user->photo, $template->render(['user' => $this->user]), null, new InlineKeyboardMarkup($buttons), false, 'html');
        } else {
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $template->render(['user' => $this->user]), new InlineKeyboardMarkup($buttons));
        }
    }
}