<?php


namespace App\Services\Telegram\Objects;

/**
 * Class Contact
 *
 * @method string   getPhoneNumber()    Contact's phone number.
 * @method string   getFirstName()      Contact's first name.
 * @method string   getLastName()       (Optional). Contact's last name.
 * @method int      getUserId()         (Optional). Contact's user identifier in Telegram.
 */
class Contact extends BaseObject
{

    function relations()
    {
        return [];
    }

    function getObjectName()
    {
        return 'contact';
    }
}