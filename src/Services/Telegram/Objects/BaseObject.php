<?php


namespace App\Services\Telegram\Objects;


abstract class BaseObject
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var array|mixed
     */
    private $postResponse = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->postResponse = $this->getPhpInput();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        $this->postResponse = $this->getPhpInput();

        $action = substr($name, 0, 3);

        if ($action !== 'get')
            return false;

        $this->items[] = $this->getObjectName();

        $property = substr($name, 3);
        $property = $this->camelCaseToUnderscore($property);

        $relations = $this->relations();

        if (array_key_exists($property, $relations)) {
            $className = $relations[$property];
            $items = $this->items;
            unset($this->items);
            return $class = new $className($items);
        }

        $itemsData = $this->postResponse;

        foreach ($this->items as $key => $value) {
            $itemsData = $itemsData[$this->items[$key]];
        }

        unset($this->items);
        return $itemsData[$property];
    }

    /**
     * @return array
     */
    protected function getItems()
    {
        return $this->items;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function camelCaseToUnderscore(string $name)
    {
        return strtolower(preg_replace( '/(?<!^)[A-Z]/', '_$0', $name));
    }

    /**
     * @return mixed
     */
    private function getPhpInput()
    {
        return json_decode(file_get_contents('php://input'), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @return mixed
     */
    abstract function relations();

    /**
     * @return mixed
     */
    abstract public function getObjectName();

}
