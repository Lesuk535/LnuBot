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
     *
     * __set($name, $value) буде виконана при запуску недоступної властивості. У властивість з назвою $name
     * буде записано $value. $camelCaseName приймає перетворений рядок виду setName значення якого буде
     * використано в якості імені $this->$camelCaseName. Після чого ми сетерим туди потрібне значення.
     *
     * Нахуй потрібно? Діло в тому, що у классі DBconnect є метод dbQuery() в ньому є така строчка:
     *
     * return $query->fetchAll(\PDO::FETCH_CLASS, $className);
     *
     * у fetchAll() ми передаємо спеціальну константу \PDO::FETCH_CLASS, завдяки ній нам повернеться результат у
     * вигляді обєкту якогось класу. $className (другий аргумент) назва того самого обєкту якогось класу.
     *
     * В нашому випадку ми всюди передаємо static::class (обэкт классу де викликаэться метод).
     *
     * типу нам потрібно з таблиці gay отримати дані де колонка name === Бодя. Робиш відповідний запит
     * і тобі повертається обєкт де властивості є назвами колонок (id, name, love_dicks, is_alone).Так як в нашій моделі
     * new Бодя немає властивості private love_dicks, але є private loveDicks, то викликається магічний метод __set().
     * А що він робить, я написа на початку.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    /**
     *
     * повертає всі значення з таблиці, як ти можеш помітити в запит передається
     * static::getTableName(). Як я вже писав вище static - це обєкт класу де викликається метод. getTableName() - собсно
     * метод, що викликаємо.
     *
     * @return array|null
     */
    public function getAll()
    {
        $sql = sprintf('SELECT * FROM `%s`;', static::getTableName());
        return $this->db->dbQuery($sql, [], static::class);
    }

    /**
     *
     * береш дані по айдішці, він верне або один обєкт, або нулл (немає в таблиці такого)
     *
     * @param int $id
     * @return static|null
     */
    public static function getById(int $id): self
    {
        $db = DBConnect::connect();
        $sql = sprintf('SELECT * FROM `%s` WHERE `id` = :id;', static::getTableName());
        $entities = $db->dbQuery($sql, ['id' => $id], static::class);
        return $entities ? $entities[0] : null;
    }

    /**
     *
     * метод говорить сам за себе
     *
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * якщо id не пуста, то робить апдейт даних в таблиці. Якщо ні то інсертить їх
     *
     */
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
     *
     * тут ми юзаємо рефлексію. Рефлексія - це коли програма, під час виконання, може в реальному часі
     * дізнаватись про свій стан і змінювати свою поведінку. Тут ми отримуємо іменаВластивості та приводимо до
     * імена_властивості. Після чого в массив  $mappedProperties добавляємо елементи з ключами імя_властивість і зі
     * значенням цих властивостей.
     *
     * також видаляю тут ключ дб, ібо він нам нахуй не потрібний
     *
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
        /**
         * array_keys($mappedProperties) бере ключі з масиву та робить інший масив, де ключі попереднього тепер є
         * значення
         *
         * sprintf('%s', implode(',', array_keys($mappedProperties))) - обєднює елементи масиву в строки розбиваючи їх комою
         *
         * explode(',', sprintf('%s', implode(',', array_keys($mappedProperties)))) по комі розбиває на масив. Так ми
         * отримуємо массив з назвами колонок
         *
         */
        $columns = explode(',', sprintf('%s', implode(',', array_keys($mappedProperties))));

        $tableName = static::getTableName();
        $masks = '';

        /**
         *  екраную значення колонок, типу зі всіма колонками має буди id = :id, циклом я так роблю зі всіма
         * коллонками
         */
        foreach ($columns as $column) {
            $masks = sprintf('%s, `%s` = :%s', $masks, $column, $column);
        }

        /**
         * перше символ виходить пустий рядок, видаляю його
         */
        $masks = mb_substr($masks, 1);

        /**
         * склеюю запит до бази
         */
        $sql = sprintf('UPDATE `%s` SET %s WHERE `id` = %s', $tableName, $masks, $this->id);

        $this->db->dbQuery($sql, $mappedProperties);
    }

    /**
     * @param array $mappedProperties
     */
    private function insert(array $mappedProperties)
    {
        /**
         * видаляю нулл з масиву
         */
        $mappedProperties = array_filter($mappedProperties);

        // В один рядок колонки дістаю
        $columns = sprintf('%s ', implode(', ', array_keys($mappedProperties)));
        // те ж саме тіки з масками
        $masks = sprintf(':%s ', implode(', :', array_keys($mappedProperties)));

        $tableName = static::getTableName();

        // запит
        $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $tableName, $columns, $masks);

        $this->db->dbQuery($sql, $mappedProperties, static::class);
        $this->id = $this->db->getLastInsertId(); // повериає ост айді добавленої колонки
        $this->refresh();
    }

    /**
     * бере по айді дані
     *
     * по рефлексі беремо властивості
     *
     * Далі біжимо циклом по цих властивостей
     *
     * робимо властивості публічними
     *
     * читаємо їх імя
     *
     * властивість з таким же іменем задає значення із властивості взятого у обєкту із бази
     *
     *
     */
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