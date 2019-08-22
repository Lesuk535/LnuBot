<?php


namespace App\Models;

use App\Services\DBConnect;


class WeekType extends ActiveRecordEntity
{
    protected $type;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'week_type';
    }
}