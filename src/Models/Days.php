<?php


namespace App\Models;

use App\Services\DBConnect;


class Days extends ActiveRecordEntity
{

    protected $name;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
    }

    /**
     * @param int $number
     */
    public function setName(int $number)
    {
        $this->name = $number;
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'days';
    }
}