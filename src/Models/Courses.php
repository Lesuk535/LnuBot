<?php


namespace App\Models;

use App\Services\DBConnect;


class Courses extends ActiveRecordEntity
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
     * @return mixed
     */
    public function getNumber()
    {
        return $this->getNumber();
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'courses';
    }
}