<?php
// This page is executed at the beginning of all pages


// session start
session_start();

// base config
define('BASE_PATH', __DIR__);    // Example path : C:\xampp\htdocs\my workspace\php_tutorial\news-project
define('CURRENT_DOMAIN', currentDomain() . "/news-project/");    // Example path : http://localhost:8080/ + /news-project/
define('DISPLAY_ERROR', true);

// database config
define('DB_HOST', "localhost");
define('DB_NAME', "news-project");
define('DB_USERNAME', "root");
define('DB_PASSWORD', "");

// including files
require_once 'Database/DataBase.php';
require_once 'Database/CreateDB.php';
require_once 'Activities/Admin/Admin.php';
require_once 'Activities/Admin/Category.php';

/* $db = new Database\DataBase();
$create_db = new Database\CreateDB();
$create_db->run(); */

// just for debuging
function dd($var)
{
    echo "<pre>";
    var_dump($var);
    exit;
}

// routing system
function URI($reservedURL, $class, $method, $requestMethod = 'GET')     // reserved address , selection class , method of selected class , method of page (default = GET)
{
    // current url array
    $currentURL = explode('?', currentURL())[0];
    $currentURL = str_replace(CURRENT_DOMAIN, '', $currentURL);
    $currentURL = trim($currentURL, '/');
    $currentURLarray = explode('/', $currentURL);
    $currentURLarray = array_filter($currentURLarray);

    // reserved url array
    $reservedURL = trim($reservedURL, '/');
    $reservedURLarray = explode('/', $reservedURL);
    $reservedURLarray = array_filter($reservedURLarray);

    // check for current address
    if (sizeof($currentURLarray) != sizeof($reservedURLarray) || methodField() != $requestMethod) {
        return false;
    }

    $parameters = [];
    for ($key = 0; $key < sizeof($currentURLarray); $key++) {
        if ($reservedURLarray[$key][0] == '{' && $reservedURLarray[$key][-1] == '}') {
            array_push($parameters, $currentURLarray[$key]);
        } elseif ($currentURLarray[$key] !== $reservedURLarray[$key]) {
            return false;
        }
    }

    if (methodField() == 'POST') {
        $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $parameters = array_merge([$request], $parameters);
    }


    $object = new $class;
    call_user_func_array(array($object, $method), $parameters);
    exit();
}

// helpers
function protocol()
{
    // SERVER_PROTOCOL respone http or https
    return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
}

function currentDomain()
{
    // HTTP_HOST respone the domain like localhost
    return protocol() . $_SERVER['HTTP_HOST'];
}

function ASSET($src)    // for addressing assets file (like css, js, ...)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $source = $domain . '/' . trim($src, '/');
    return $source;
}

function URL($url)      // for addressing pages
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $link = $domain . '/' . trim($url, '/');
    return $link;
}

function currentURL()
{
    // http://localhost:8080/my%20workspace/php_tutorial/news-project/
    return currentDomain() . $_SERVER['REQUEST_URI'];
}

function methodField()
{
    // REQUEST_METHOD response the method of the current page is GET or POST
    return $_SERVER['REQUEST_METHOD'];
}

function displayError($displayError)
{
    if ($displayError) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}
displayError(DISPLAY_ERROR);

global $flashMessage;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

function flash($name, $value = null)
{
    if ($value === null) {
        global $flashMessage;
        $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
        return $message;
    } else {
        $_SESSION['flash_message'][$name] = $value;
    }
}


// Category rout
URI('admin/category', 'Admin\Category', 'index');
URI('admin/category/create', 'Admin\Category', 'create');
URI('admin/category/store', 'Admin\Category', 'store', 'POST');
URI('admin/category/edit/{id}', 'Admin\Category', 'edit');
URI('admin/category/update/{id}', 'Admin\Category', 'update', 'POST');
URI('admin/category/delete/{id}', 'Admin\Category', 'delete');

// 404 page
require 'Template/404/index.html';