<?php

namespace MVC;

class Router {

    public $rutasGET = [];
    public $rutasPOST = [];

    public function get($url, $fn) {
        $this->rutasGET[$url] = $fn;
    }

    public function post($url, $fn) {
        $this->rutasPOST[$url] = $fn;
    }

    public function comprobarRutas() {
        session_start();
        $auth = $_SESSION['login'] ?? null; 

        // Arreglo de rutas protegidas
        $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        // Obtener la URL actual
        $urlActual = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $metodo = $_SERVER['REQUEST_METHOD'];

        // Proteger las rutas
        if(in_array($urlActual, $rutas_protegidas) && !$auth) {
            header('Location: /');
        }

        // Obtener la función que se ejecuta con base a la ruta
        if ($metodo === 'GET') {
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else {
            $fn = $this->rutasPOST[$urlActual] ?? null;
        }

        // Ejecutar la función asociada a la ruta
        if ($fn) {
            call_user_func($fn, $this);
        } else {
            echo 'Página no encontrada';
        }
    }

    // Muestra una vista
    public function render($view, $datos = []) {
        // Incluir los datos de la vista
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        include __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // limpiar el buffer
        include __DIR__ . "/views/layout.php";
    }
}