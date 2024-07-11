<?php
declare(strict_types=1);
namespace gimle;

use \gimle\xml\SimpleXmlElement;

$resultlimit = Config::get('search.limit');

$exec = 'find $1 -type f -print0 | xargs -0 stat --format \'%Y :%y %n\' | sort -nr | cut -d: -f2-';
$exec = 'cd ' . escapeshellarg(STORAGE_DIR . 'xml/wiki/') . '; ' . $exec . '| head -n' . ($resultlimit + 1);
$result = exec($exec);

?>
<div id="searchresults" class="content">
<?php
foreach ($result['stout'] as $fifo) {
	$date = substr($fifo, 0, 19);
	$file = substr($fifo, 38);

	$sxml = SimpleXmlElement::open(STORAGE_DIR . 'xml/wiki/' . $file);
	$title = trim(strip_tags((string) current($sxml->xpath('//header[1]'))));
	if ($title === '') {
		$title = substr($file, 0, -4);
	}
?>
	<a href="<?=htmlspecialchars(BASE_PATH . 'wiki/' . Wiki::getSlug($file))?>">
		<h3><?=htmlspecialchars($title)?></h3>
		<p><?=htmlspecialchars($date)?> - <?=htmlspecialchars(substr($file, 0, -4))?></p>
	</a>
<?php
}
?>
</div>
<?php

return true;
