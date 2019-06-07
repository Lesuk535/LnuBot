<?php


namespace App\Services\Telegram\Objects;

/**
 * Class User.
 *
 *
 * @method int      getId()             Unique identifier for this user or bot.
 * @method string   getFirstName()      User's or bot's first name.
 * @method string   getLastName()       (Optional). User's or bot's last name.
 * @method string   getUsername()       (Optional). User's or bot's username.
 * @method string   getLanguageCode()   (Optional). User's or bot's username.
 */
class User extends BaseObject
{

    public function relations()
    {
        return [];
    }

    public function getObjectName()
    {
        return 'user';
    }
}