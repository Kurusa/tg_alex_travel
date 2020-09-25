<?php

namespace App\Commands\RecordInfo;

use App\Commands\BaseCommand;
use App\Models\User;
use App\Utils\Twig;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class UserDetails extends BaseCommand
{

    function processCommand()
    {
        $callback = json_decode($this->update->getCallbackQuery()->getData(), true);
        if ($callback['a'] == 'user_details' && $callback['id']) {
            $user = User::find($callback['id']);
            $twig = Twig::getInstance();
            $template = $twig->load('user_' . strtolower($this->user->lang) . '.twig');

            $this->getBot()->sendPhoto($this->user->chat_id, $this->user->photo, $template->render(['user' => $user]), false, null, false, 'html');
        }
    }

}