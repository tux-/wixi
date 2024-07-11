<?php
declare(strict_types=1);
namespace gimle;

use \gimle\xml\SimpleXmlElement;

$dirs = [];
$files = [];

$page = page('page');
if ($page === null) {
	$page = '';
}
else {
	$page .= '/';
	$dirs[] = [
		'name' => '..',
		'url' => dirname($page),
	];
}

if (!is_readable(STORAGE_DIR . 'xml/wiki/' . $page)) {
?>
<div id="searchresults" class="content">
	<div class="error">
		<h1>Error</h1>
		<p>The location was not found.</p>
	</div>
</div>
<?php
	return true;
}

?>
<div id="searchresults" class="content">
<?php
foreach (new \DirectoryIterator(STORAGE_DIR . 'xml/wiki/' . $page) as $fifo) {
	$fname = $fifo->getFilename();
	if (str_starts_with($fname, '.')) {
		continue;
	}
	if ($fifo->isDir()) {
		$dirs[] = [
			'name' => $fname,
			'url' => Wiki::getSlug($page . $fname),
		];
	}
	else {
		$name = substr($fname, 0, -4);
		if (($page === '') && ($name === 'wiki')) {
			continue;
		}
		$sxml = SimpleXmlElement::open(STORAGE_DIR . 'xml/wiki/' . $page . $fname);
		$title = trim(strip_tags((string) current($sxml->xpath('//header[1]'))));
		if ($title === '') {
			$title = $name;
		}
		$files[] = [
			'title' => $title,
			'name' => $name,
			'url' => Wiki::getSlug($page . $name),
		];
	}
}
usort($dirs, function ($a, $b) {
	return strnatcasecmp($a['name'], $b['name']);
});
usort($files, function ($a, $b) {
	return strnatcasecmp($a['name'], $b['name']);
});
foreach ($dirs as $dir) {
?>
	<a href="<?=BASE_PATH?>browse/<?=$dir['url']?>">
		<h3><?=htmlspecialchars($dir['name'])?></h3>
	</a>
<?php
}
?>
<hr/>
<?php
foreach ($files as $file) {
?>
	<a href="<?=htmlspecialchars(BASE_PATH . 'wiki/' . $file['url'])?>">
		<h3><?=htmlspecialchars($file['title'])?></h3>
		<p><?=htmlspecialchars($file['name'])?></p>
	</a>
<?php
}
?>
</div>
<?php

return true;
