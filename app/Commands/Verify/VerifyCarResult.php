<?php

namespace App\Commands\Verify;

use App\Commands\BaseCommand;
use App\Models\User;
use App\Models\UserCar;

class VerifyCarResult extends BaseCommand
{

    function processCommand()
    {
        if ($this->update->getCallbackQuery()) {
            $callback_data = json_decode($this->update->getCallbackQuery()->getData(), true);
            if ($callback_data['a'] == 'done_verify_car') {
                UserCar::where('user_id', $this->user->id)->update([
                    'verified' => 1
                ]);
                $this->getBot()->sendMessage($callback_data['id'], $this->text['your_car_is_verified']);
            } elseif ($callback_data['a'] == 'decline_verify_car') {
                $this->getBot()->sendMessage($callback_data['id'], $this->text['your_profile_is_declined']);
            }
        }
    }

}