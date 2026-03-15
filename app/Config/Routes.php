<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'RegistController::index');
$routes->post('card/generate', 'RegistController::generateCard');

$routes->group('admin', static function ($routes) {
    $routes->match(['get', 'post'], 'login', 'AdminController::login');
    $routes->get('logout', 'AdminController::logout');

    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->match(['get', 'post'], 'scan', 'AdminController::scan');

    $routes->match(['get', 'post'], 'import', 'AdminController::import');

    $routes->get('report', 'AdminController::report');
    $routes->get('report/pdf', 'AdminController::reportPdf');
    $routes->get('report/csv', 'AdminController::reportCsv');
    $routes->get('logs/login', 'AdminController::loginLogs');
    $routes->get('logs/scan', 'AdminController::scanLogs');
});
