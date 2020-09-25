<?php

namespace App\Commands\RecordInfo;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Utils\Twig;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecordInfo extends BaseCommand
{

    function processCommand()
    {
        $callback = json_decode($this->update->getCallbackQuery()->getData(), true);
        if ($callback['a'] == 'record' && $callback['id']) {
            $record = Record::find($callback['id']);
            $twig = Twig::getInstance();
            $template = $twig->load('record_' . strtolower($this->user->lang) . '.twig');

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $template->render([
                    'record' => $record
                ]), new InlineKeyboardMarkup([
                [[
                    'text' => $this->text['book_record'],
                    'callback_data' => json_encode([
                        'a' => 'book_spot',
                        'id' => $record->id
                    ])
                ]],
                [[
                    'text' => $this->text['map'],
                    'url' => 'https://www.google.com.ua/maps/dir/' . $record->location->arrival_city_name . '/' . $record->location->departure_city_name
                ]],
                [[
                    'text' => $this->text['details'],
                    'callback_data' => json_encode([
                        'a' => 'user_details',
                        'id' => $record->user_id
                    ])
                ]]
            ]));
        }
    }

}