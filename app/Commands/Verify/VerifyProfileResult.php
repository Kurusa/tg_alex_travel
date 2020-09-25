<?php

namespace App\Commands\Verify;

use App\Commands\BaseCommand;
use App\Models\User;

class VerifyProfileResult extends BaseCommand
{

    function processCommand()
    {
        if ($this->update->getCallbackQuery()) {
            $callback_data = json_decode($this->update->getCallbackQuery()->getData(), true);
            if ($callback_data['a'] == 'done_verify_profile') {
                User::where('chat_id', $callback_data['id'])->update([
                    'verified' => 1
                ]);
                $this->getBot()->sendMessage($callback_data['id'], $this->text['your_profile_is_verified']);
            } elseif ($callback_data['a'] == 'decline_verify') {
                $this->getBot()->sendMessage($callback_data['id'], $this->text['your_profile_is_declined']);
            }
        }
    }

}