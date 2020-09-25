<?php

namespace App\Commands\RecordInfo;

use App\Commands\BaseCommand;
use App\Models\Booking;
use App\Models\Record;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class BookSpot extends BaseCommand
{

    function processCommand()
    {
        $callback = json_decode($this->update->getCallbackQuery()->getData(), true);
        if ($callback['a'] == 'book_spot' && $callback['id']) {
            $record = Record::find($callback['id']);
            $buttons = [];
            for ($i = 1; $i <= 3; $i++) {
                if ($record->free_spots >= $i) {
                    $buttons[] = [
                        'text' => $i,
                        'callback_data' => json_encode([
                            'a' => 'spot_select',
                            'id' => $record->id,
                            'c' => $i
                        ])
                    ];
                }
            }
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['select_spot_count'], new InlineKeyboardMarkup([$buttons]));
        } elseif ($callback['a'] == 'spot_select' && $callback['id']) {
            Booking::create([
                'user_id' => $this->user->id,
                'record_id' => $callback['id']
            ]);
            Record::find($callback['id'])->decrement('free_spots', $callback['c']);
            $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());
        }
    }

}