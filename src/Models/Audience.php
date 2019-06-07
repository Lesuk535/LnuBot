<?php


namespace App\Models;

use App\Services\DBConnect;


class Audience extends ActiveRecordEntity
{

    protected $name;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'audience';
    }
}