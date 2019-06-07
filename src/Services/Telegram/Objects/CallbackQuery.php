<?php


namespace App\Services\Telegram\Objects;


/**
 * @method string   getData()              Optional. Data associated with the callback button. Be aware that a bad client can send arbitrary data in this field.
 * @method string   getInlineMessageId()   Optional. Identifier of the message sent via the bot in inline mode, that originated the query.
 * @method string   getId()                Unique identifier for this query
 */
class CallbackQuery extends BaseObject
{

    function relations()
    {
        return [];
    }

    public function getObjectName()
    {
        return 'callback_query';
    }
}