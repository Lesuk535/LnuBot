<?php


namespace App\Services\Routing;

use App\Services\Telegram\Objects\Message;
use App\Services\Telegram\Objects\CallbackQuery;

class ApiRouter extends Router
{
    public function __construct(string $routes)
    {
        parent::__construct($routes);
        parent::run();
    }

    /**
     * @return mixed|string
     */
    protected function getUri(): ?string
    {
        $message = new Message();
        $messageText = $message->getText();

        if ($messageText !== null)
            return $messageText;

        $callbackQuery = new CallbackQuery();

        return $callbackQuery->getData();
    }
}