<?php


namespace App\Models;

use App\Services\DBConnect;


class Subgroups extends ActiveRecordEntity
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

    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'subgroups';
    }
}