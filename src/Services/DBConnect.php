<?php


namespace App\Services;


class DBConnect
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var self|null
     */
    private static $instance;


    private function __construct()
    {
        $dbOptions = require __DIR__ . '/../../config/db.php';
        $dsn = sprintf('%s:host=%s;dbname=%s', 'mysql', $dbOptions['host'], $dbOptions['dbname']);
        $this->pdo = new \PDO($dsn, $dbOptions['user'], $dbOptions['password']);
        $this->pdo->exec('SET NAMES UTF8');
    }

    /**
     * @return DBConnect
     */
    public static function connect(): self
    {
        if (self::$instance === null){
            return self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $className
     * @return array|bool|null
     */
    public function dbQuery(string $sql, array $params, $className = 'stdClass')
    {
        $query = $this->pdo->prepare($sql);
        $result = $query->execute($params);

        if ($result === false)
            return null;

        return $query->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    /**
     * @return string
     */
    public function getLastInsertId(): ?string
    {
        return $this->pdo->lastInsertId();
    }

}
