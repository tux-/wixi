<?php
declare(strict_types=1);
namespace gimle;

use \gimle\router\Router;
use \gimle\canvas\Canvas;

/**
 * The local absolute location of the site.
 *
 * @var string
 */
define('gimle\\SITE_DIR', substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR) + 1));

require SITE_DIR . 'module/gimle/init.php';

Canvas::title(null, 'template');
Canvas::title('Site name', 'sitename');
$router = Router::getInstance();

$router->setCanvas('pc');

$router->bind('*', '', function () use ($router) {
	$router->setTemplate('wiki');
});
$router->bind('*', 'wiki(/:page)', function () use ($router) {
	$router->setTemplate('wiki');
}, ['page' => '.*']);
$router->bind('*', 'contents', function () use ($router) {
	$router->setCanvas('json');
	$router->setTemplate('contents');
});
$router->bind('*', 'save', function () use ($router) {
	$router->setCanvas('json');
	$router->setTemplate('save');
}, Router::R_POST);

$router->dispatch();

return true;
