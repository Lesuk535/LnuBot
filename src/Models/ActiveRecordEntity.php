<?php


namespace App\Models;

use App\Services\DBConnect;


abstract class ActiveRecordEntity
{

    /**
     * @var DBConnect
     */
    protected $db;

    /**
     * @var int
     */
    protected $id;

    public function __construct(DBConnect $db)
    {
        $this->db = $db;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array|bool|null
     */
    public function getAll()
    {
        $sql = sprintf('SELECT * FROM `%s`;', static::getTableName());
        return $this->db->dbQuery($sql, [], static::class);
    }

    /**
     * @param int $id
     * @return ActiveRecordEntity
     */
    public static function getById(int $id): self
    {
        $db = DBConnect::connect();
        $sql = sprintf('SELECT * FROM `%s` WHERE `id` = :id;', static::getTableName());
        $entities = $db->dbQuery($sql, ['id' => $id], static::class);
        return $entities ? $entities[0] : null;
    }

    /**
     * @param int $id
     * @param string $value
     * @return static
     */
    public static function getValueById(int $id, string $value): self
    {
        $db = DBConnect::connect();
        $sql = sprintf('SELECT `%s` FROM `%s` WHERE `id` = :id;',$value, static::getTableName());
        $entities = $db->dbQuery($sql, ['id' => $id], static::class);
        return $entities ? $entities[0] : null;
    }

    /**
     * @param string $columnName
     * @param string $value
     * @return static
     */
    public function getIdByColumn(string $columnName, string $value)
    {
        $db = DBConnect::connect();
        $sql = sprintf('SELECT `id` FROM `%s` WHERE `%s` = :value;', static::getTableName(), $columnName);
        $entities = $db->dbQuery($sql, ['value' => $value], static::class);
        return $entities ? $entities[0] : null;
    }

    /**
     * @param string $columnName
     * @param $value
     * @return static
     */
    public function getValueByColumn(string $columnName, $value)
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE `%s` = :value;', static::getTableName(), $columnName);
        $results = $this->db->dbQuery($sql, ['value' => $value], static::class);
        return $results ? $results[0] : null;
    }

    /**
     * @param string $columnName
     * @param $value
     * @return array
     */
    public function getAllValueByColumn(string $columnName, $value) {
        $sql = sprintf('SELECT * FROM `%s` WHERE `%s` = :value;', static::getTableName(), $columnName);
        $results = $this->db->dbQuery($sql, ['value' => $value], static::class);
        return $results ? $results : null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();

        if ($this->id !== null) {
            $this->update($mappedProperties);

        }
        else {
            $this->insert($mappedProperties);
        }
    }

    /**
     * @return array
     */
    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();


        $mappedProperties = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
        }

        unset($mappedProperties['db']);


        return $mappedProperties;
    }

    /**
     * @param array $mappedProperties
     */
    private function update(array $mappedProperties)
    {
        $columns = explode(',', sprintf('%s', implode(',', array_keys($mappedProperties))));

        $tableName = static::getTableName();
        $masks = '';

        foreach ($columns as $column) {
            $masks = sprintf('%s, `%s` = :%s', $masks, $column, $column);
        }

        $masks = mb_substr($masks, 1);

        $sql = sprintf('UPDATE `%s` SET %s WHERE `id` = %s', $tableName, $masks, $this->id);

        $this->db->dbQuery($sql, $mappedProperties, static::class);
    }

    /**
     * @param array $mappedProperties
     */
    private function insert(array $mappedProperties)
    {
        $mappedProperties = array_filter($mappedProperties);

        $columns = sprintf('%s ', implode(', ', array_keys($mappedProperties)));

        $masks = sprintf(':%s ', implode(', :', array_keys($mappedProperties)));

        $tableName = static::getTableName();

        $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $tableName, $columns, $masks);

        $this->db->dbQuery($sql, $mappedProperties, static::class);
        $this->id = $this->db->getLastInsertId(); // повериає ост айді добавленої колонки
        $this->refresh();
    }

    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);
        $reflector = new \ReflectionObject($objectFromDb);
        $properties = $reflector->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $this->$propertyName = $property->getValue($objectFromDb);
        }

    }

    /**
     * @param string $source
     * @return string
     */
    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    /**
     * @param string $source
     * @return string
     */
    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }

    /**
     * @return string
     */
    abstract protected static function getTableName();
}