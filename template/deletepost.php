<?php
declare(strict_types=1);
namespace gimle;

try {
	$from = Wiki::slugToFilename($_POST['from']);
	unlink($from);
	$dir = dirname($from);
	if ($dir !== STORAGE_DIR . 'xml/wiki/') {
		$iterator = new \FilesystemIterator($dir);
		if (!$iterator->valid()) {
			rmdir($dir);
		}
	}
	echo json_encode(true);
}
catch (\Throwable $t) {
	sp($t);
	echo json_encode([
		'error' => $t->getMessage(),
	]);
}

return true;
