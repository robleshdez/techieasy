<?php
// Este archivo es una versión mejorada y generalizada de Router.php
// core/Dispatcher.php

class Dispatcher {
    
    private $renderController;

    public function __construct($renderController) {
        $this->renderController = $renderController;
    }

    public function resolve() {
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $path = trim(substr($uri, strlen($base)), '/');
        $segments = explode('/', $path);

        // Soporte para idioma opcional (/es/..., /en/..., etc.)
        $locale = in_array($segments[0], ['es', 'en']) ? array_shift($segments) : null;
        $mainFolder   = $segments[0] ?? 'home';
        $templateName = $mainFolder;
        $moduleName   = '';
        $actionName   = 'index';
        $businessID   = '';
        $itemID       = '';
        $controller   = ucfirst($mainFolder) . 'Controller';

        // Dispatcher por niveles
        if (count($segments) === 1) {
            $moduleName = $mainFolder;

        } elseif (count($segments) === 2) {
            $templateName = $mainFolder;
            $controller = ucfirst($segments[1]) . 'Controller';
            $moduleName = $segments[1];

        } elseif (count($segments) === 3) {
            $templateName = $mainFolder;
            $controller = ucfirst($segments[1]) . 'Controller';
            $moduleName = $segments[1];
            $businessID = $segments[2];

        } elseif (count($segments) === 4) {
            $templateName = $segments[1];
            $controller = ucfirst($segments[3]) . 'Controller';
            $moduleName = $segments[3];
            $businessID = $segments[2];

        } elseif (count($segments) === 5) {
            $templateName = $segments[1];
            $controller = ucfirst($segments[3]) . 'Controller';
            $moduleName = $segments[3];
            $actionName = 'details';
            $businessID = $segments[2];
            $itemID = $segments[4];

        } elseif (count($segments) === 6) {
            $templateName = $segments[1];
            $controller = ucfirst($segments[3]) . 'Controller';
            $moduleName = $segments[3];
            $actionName = $segments[4];
            $businessID = $segments[2];
            $itemID = $segments[5];
        }

        if (!class_exists($controller)) {
            return [ 'templateName' => 'error', 'actionName' => '404' ];
        }

        $ctrlInstance = new $controller();
        if (!method_exists($ctrlInstance, $actionName)) {
            return [ 'templateName' => 'error', 'actionName' => '404' ];
        }

        $data = $ctrlInstance->$actionName($businessID, $itemID);

        return array_merge($data, [
            'locale'       => $locale,
            'mainFolder'   => $mainFolder,
            'templateName' => $templateName,
            'moduleName'   => $moduleName,
            'actionName'   => $actionName,
            'businessID'   => $businessID,
            'itemID'       => $itemID
        ]);
    }

    // Nota: si en el futuro deseas añadir soporte real de rutas tipo API,
    // puedes reactivar una función como resolveAPI() y adaptarla al nuevo estándar.
}
