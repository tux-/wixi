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
	if (isset($_GET['q'])) {
		return $router->setTemplate('search');
	}
	$router->setTemplate('wiki');
});
$router->bind('*', 'wiki(/:page)', function () use ($router) {
	$router->setTemplate('wiki');
}, ['page' => '.*']);
$router->bind('*', 'contents', function () use ($router) {
	$router->setCanvas('json');
	$router->setTemplate('contents');
});
$router->bind('*', 'browse(/:page)', function () use ($router) {
	$router->setTemplate('browse');
}, ['page' => '.*']);
$router->bind('*', ':template', function () use ($router) {
	$router->setTemplate(page('template'));
}, ['page' => 'modified']);
$router->bind('*', ':template', function () use ($router) {
	$router->setCanvas('json');
	$router->setTemplate(page('template'));
}, ['template' => 'save|movepost'], Router::R_POST);

$router->dispatch();

return true;
