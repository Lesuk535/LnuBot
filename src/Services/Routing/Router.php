<?php


namespace App\Services\Routing;


abstract class Router
{

    private $routes;

    public function __construct(string $routes)
    {
        $this->routes = include ("$routes");
    }

    /**
     * @return bool
     */
    protected function run()
    {
        $result = false;

        $uri = $this->getUri();

        foreach ($this->routes as $pattern => $path) {
            if (preg_match("`^$pattern`", $uri)) {

                $internalRoute = preg_replace("`^$pattern$`", $path, $uri);

                $segment = explode('/', $internalRoute);

                $controller = array_shift($segment);
                $controller = ucfirst($controller);
                $controller = sprintf('App\Controller\%sController', $controller);

                $action = array_shift($segment).'Action';
                $action = ucfirst($action);

                $parameters = $segment;

                $controllerObject = new $controller();

                $result = call_user_func_array([$controllerObject, $action], $parameters);
            }
        }

        return $result === true ? true : false;

    }

    /**
     * @return mixed
     */
    abstract protected function getUri();

}