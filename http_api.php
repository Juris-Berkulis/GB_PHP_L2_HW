<?php

use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;

require_once __DIR__ . '/vendor/autoload.php';

// Объект запроса из суперглобальных переменных
$request = new Request($_GET, $_SERVER);

//// Получаем данные из объекта запроса
//$parameter = $request->query('name');
//$header = $request->header('Some-Header');
//$path = $request->path();

// Создаём объект ответа
$response = new SuccessfulResponse([
    'message' => 'Hello from PHP!!!',
]);

// Отправляем ответ
$response->send();
