<?php
declare(strict_types=1);
namespace gimle;

use \gimle\xml\Xsl;

try {
	$file = Wiki::xmlFilename($_GET['page']);
	if (file_exists($file)) {
		$sxml = \gimle\xml\SimpleXmlElement::open($file);
	}
	else {
		if (($_GET['page'] === '') || ($_GET['page'] === 'wiki') || ($_GET['page'] === 'wiki/')) {
			$sxml = \gimle\xml\SimpleXmlElement::open(STATIC_DIR . 'welcome.xml');
		}
		elseif (is_readable(STORAGE_DIR . 'default.xml')) {
			$sxml = \gimle\xml\SimpleXmlElement::open(STORAGE_DIR . 'default.xml');
		}
		else {
			$sxml = \gimle\xml\SimpleXmlElement::open(STATIC_DIR . 'default.xml');
		}
	}

	$json = [
		'time' => (int) $sxml['time'],
		'version' => (string) $sxml['version'],
		'blocks' => [],
	];

	Wiki::xmlToJson($sxml, $json);

	echo json_encode($json);
}
catch (\Throwable $t) {
	sp($t);
	echo json_encode(false);
}

return true;
