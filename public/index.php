<?php

/**
 * TODO: Добавить точку входа для админки с проверкой маршрута (изолирует инсталлер от сайта)
 * TODO: Создать скрипт для админки с проверкой состояния системы:
 *      - проверка .env на наличие и корректность
 *      - проверка lock-файла, чтобы удедиться что система была ранее установлена
 *      - при не успешной проверке запуск инсталлятора или восстановления системы
 *      - при восстановлении дополнительная проверка состояния БД
 * TODO: Назвать инсталяттор "setup" и заложит 2 режима работы:
 *      - установка системы (install)
 *      - восстановление системы (repair)
 * TODO: Создать парсер для работы с файлом .env (пинсинг в массив, редактирование переменных в файле). 
 *      Размещение парсера в ядре системы (отдельная папка). Парсер должен уметь:
 *      - понимать и обрабатывать кавычки в значении ключа
 *      - интерполяцию ${VARIABLE}
 *      - многострочные значения
 *      - коммертарии до переменной (группировка нескольких строк комментариев как единный комментарий)
 *      - коммертарии с строке с переменной
 *      - закомментированная переменная как выключенное состояние
 *      - отдельный вид комментария в начале группы (ничинается и заканчивается как /^# ?\-+$/ (искать
 *          по этой регулярке)) как ID на схему с данными и настройками,
 *          или как заголовак с [опционально] описанием, если формат не похож на ID или такого ID нет.
 *      - дополнительный файл со схемой для системных групп и переменных, содержащий параметры для интерфейса установки
 * TODO: Реализовать создание первого админа в БД с указанием минимального количества данных,
 *      позже будет расширяться отдельным плагином
 * TODO: Реализовать с админке функционал проверки целостности системы и предлагать контролируемое восстановление
 * TODO: Создать создать скрипт для CRON-задачи на проверку целостности системы и логирования ЧП
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());


// Код нейронки (v1 Без изоляции в админку)
/*
// 1. Подключение Composer autoload
require __DIR__.'/../vendor/autoload.php';

// 2. Чтение .env (если есть)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->safeLoad();

// 3. Проверка .env — всегда, независимо от lock-файла
$envFile = __DIR__.'/../.env';
$appKey = $_ENV['APP_KEY'] ?? null;
$envValid = file_exists($envFile) && !empty($appKey);

if (!$envValid) {
    require __DIR__.'/../install/index.php';
    exit;
}

// 4. Проверка lock-файла (быстрый hint)
$installedLock = __DIR__.'/../storage/installed.lock';
if (!file_exists($installedLock)) {
    require __DIR__.'/../install/index.php';
    exit;
}

// 5. Штатная загрузка Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
*/

// Код нейронки (v2 С изоляцией в админку)
/*
// public/index.php
$adminPrefix = env('ADMIN_PREFIX', 'admin'); // Берем из .env
$requestPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Если запрос начинается с префикса админки
if (str_starts_with($requestPath, $adminPrefix)) {
    require __DIR__.'/../admin/index.php';
    exit;
}

// Иначе стандартный запуск Laravel для сайта
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
*/