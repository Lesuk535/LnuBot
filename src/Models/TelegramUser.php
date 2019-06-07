<?php


namespace App\Models;

use App\Services\DBConnect;


class TelegramUser extends ActiveRecordEntity
{
    protected $chatId;

    protected $status;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
    }

    /**
     * @param int $chatId
     * @param string $columnName
     */
    public function register(int $chatId, string $columnName = 'chat_id')
    {
        $id = $this->getValueByColumn($columnName, $chatId);

        if ($id === null) {
            $this->setChatId($chatId);
            $this->save();
        }
    }

    /**
     * @param int $chatId
     */
    public function setChatId(int $chatId)
    {
        $this->chatId = $chatId;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'telegram_user';
    }
}