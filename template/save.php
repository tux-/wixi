<?php
declare(strict_types=1);
namespace gimle;

$json = null;
$result = null;
try {
	$json = json_decode(file_get_contents('php://input'), true);
	$result = Wiki::jsonToXml($json);

	$file = Wiki::xmlFilename($_GET['page']);
	$directory = dirname($file);

	if (!file_exists($directory)) {
		mkdir($directory);
	}

	libxml_use_internal_errors(true);
	$dom = new \DomDocument();
	$dom->loadXml($result);
	if ($dom->schemaValidate(SITE_DIR . 'xsd/wixi.xsd') === false) {
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			sp($error);
		}
		throw new Exception('Schemavalidate failed.');
	}

	file_put_contents($file, $result . "\n");

	echo json_encode(true);
}
catch (\Throwable $t) {
	sp($t);
	sp($json);
	sp($result);
	echo json_encode(false);
}

return true;
