<?php


namespace App\Models;

use App\Services\DBConnect;


class Lessons extends ActiveRecordEntity
{

    protected $number;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'lessons';
    }
}