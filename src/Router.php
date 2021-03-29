<?php

namespace Mimj;

class Router
{
    protected $routes = [];
    private $params;

    /**
     * Add a route to the routing table
     *
     * @param string $route The route URL
     * @param array $params Parameters (controllers, action, etc)
     */
    public function add($route, $params = [])
    {
        // Convert the route to the regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/<([a-z]+):(.*)>/', '(?P<\1>\2)', $route);

        // Convert variables e.g. {controllers}
        // this will result to for example: \/admin\/(?P<action>[a-z-]+)\/(?P<controller>[a-z-]+)
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Add start and end delimiters. and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Get all the routes from the routing table
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the $params
     * property if a route is found.
     *
     * @param string $url
     *
     * @return bool true if a match found, false otherwise
     */
    public function match($url)
    {

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);

            if (class_exists($controller)) {
                $controller_obj = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_obj, $action])) {
                    $controller_obj->$action();
                } else {
                    echo "Method $action (in controller $controller) not found";
                }
            } else {
                echo "Controller class $controller not found";
            }
        } else {
            echo "route not found";
        }
    }

    private function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    private function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables($url)
    {
        $url = preg_replace('(\?.*)','',$url);
        return $url;
    }
}
