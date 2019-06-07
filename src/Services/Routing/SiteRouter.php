<?php


namespace App\Services\Routing;


class SiteRouter extends Router
{
    public function __construct(string $routes)
    {
        parent::__construct($routes);
        parent::run();

    }

    /**
     * @return mixed|string
     */
    protected function getUri(): ?string
    {
        $uri = $_GET['route'];
        $uri = trim($uri);
        $uri = htmlspecialchars($uri);
        return $uri;
    }

}