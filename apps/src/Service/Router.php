<?php


namespace App;

class Router {
  private array $routes = [];

  public function get(String $path, callable|array $callback): void {
    $this->routes['GET'][$path] = $callback;
  }
  
  public function post(String $path, callable|array $callback): void {
    $this->routes['POST'][$path] = $callback;
  }

  public function query(String $path, callable|array $callback): void {
    $this->routes['QUERY'][$path] = $callback;
  }

  public function put(String $path, callable|array $callback): void {
    $this->routes['PUT'][$path] = $callback;
  }

  public function resolve(): mixed {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    
    $callback = $this->routes[$method][$path] ?? null;

    if ($callback === null) {
      http_response_code(404);
      return "404 Not Found";
    }

    if (is_array($callback)) {
      [$class, $method] = $callback;
      $controller = new $class();
      return $controller->$method();
    }
    
    return $callback;
  }
 

}
