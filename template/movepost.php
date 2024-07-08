<?php
declare(strict_types=1);
namespace gimle;

$from = Wiki::slugToFilename($_POST['from']);

try {
	$newName = Wiki::slugToFilename($_POST['slug']);
}
catch (\Throwable $t) {
	sp($t);
	echo json_encode([
		'error' => $t->getMessage(),
	]);
	return true;
}

if ($from === $newName) {
	echo json_encode([
		'error' => 'Same page',
	]);
	return true;
}

if (file_exists($newName)) {
	echo json_encode([
		'error' => 'Page exists',
	]);
	return true;
}

if (!file_exists($from)) {
	echo json_encode([
		'error' => 'Save page first',
	]);
	return true;
}

$dir = dirname($newName);
if (!file_exists($dir)) {
	mkdir($dir, 0777, true);
}

rename($from, $newName);

$dir = dirname($from);
if ($dir !== STORAGE_DIR . 'xml/wiki/') {
	$iterator = new \FilesystemIterator($dir);
	if (!$iterator->valid()) {
		rmdir($dir);
	}
}

echo json_encode([
	'target' => $_POST['slug'],
]);

return true;
