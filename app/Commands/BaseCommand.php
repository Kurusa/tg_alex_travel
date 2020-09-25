<?php

namespace App\Commands;

use App\Models\User;
use App\Utils\Api;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

/**
 * Class BaseCommand
 * @package App\Commands
 */
abstract class BaseCommand
{

    /**
     * @var User
     */
    protected $user;

    /**
     * @var \TelegramBot\Api\Types\User $user
     */
    protected $bot_user;

    protected $text;

    protected $update;

    /**
     * @var BotApi $bot
     */
    private $bot;

    public function __construct(Update $update)
    {
        $this->update = $update;
        if ($update->getCallbackQuery()) {
            $this->bot_user = $update->getCallbackQuery()->getFrom();
        } elseif ($update->getMessage()) {
            $this->bot_user = $update->getMessage()->getFrom();
        } else {
            throw new \Exception('cant get telegram user data');
        }
    }

    function handle()
    {
        $this->user = User::where('chat_id', $this->bot_user->getId())->first();
        if (!$this->user) {
            User::create([
                'chat_id' => $this->bot_user->getId(),
                'user_name' => $this->bot_user->getUsername(),
                'first_name' => $this->bot_user->getFirstName(),
            ]);
            $this->user = User::where('chat_id', $this->bot_user->getId())->first();
        }

        $this->text = require(__DIR__ . '/../config/lang/' . strtolower($this->user['lang']) . '/bot.php');
        $this->processCommand();
    }

    /**
     * @return Api
     */
    public function getBot(): Api
    {
        if (!$this->bot) {
            $this->bot = new Api(env('TELEGRAM_BOT_TOKEN'));
        }

        return $this->bot;
    }

    /**
     * @param $class
     */
    function triggerCommand($class)
    {
        (new $class($this->update))->handle();
    }

    abstract function processCommand();

}