<?php
declare(strict_types=1);
namespace gimle;

use \gimle\xml\SimpleXmlElement;

$resultlimit = Config::get('search.limit');

$q = $query = explode(' ', $_GET['q']);
array_walk($query, function (&$value) {
	$value = mb_strtolower($value);
});
array_walk($q, function (&$value) {
	$value = preg_quote($value);
});
$q = implode('.*', $q);

$exec = 'cd ' . escapeshellarg(STORAGE_DIR . 'xml/wiki/') . '; grep -irlE ' . escapeshellarg($q) . '| head -n' . ($resultlimit + 1);
$result = exec($exec);

?>
<div id="searchresults" class="content">
<?php

$i = 0;
$results = [];
foreach ($result['stout'] as $file) {
	if ($i === $resultlimit) {
?>
	<p>Result gave more than <?=$resultlimit?> hits. Result list limited.</p>
<?php
		break;
	}
	$xml = file_get_contents(STORAGE_DIR . 'xml/wiki/' . $file);
	$text = normalize_space(strip_tags($xml));
	$lower = mb_strtolower($text);
	$sxml = new SimpleXmlElement($xml);
	$score = -(mb_strlen($lower) / 1000);
	foreach ($query as $word) {
		$score += substr_count($lower, $word);
	}

	$title = $file;
	$header = current($sxml->xpath('//header[1]'));
	if ($header !== false) {
		$res = strip_tags((string) current($header->innerXml()));
		if ($res !== '') {
			$title = $res;
		}
	}

	$url = explode('/', substr($file, 0, -4));
	array_walk($url, function (&$value) {
		$value = rawurlencode($value);
	});

	$results[] = [
		'title' => $title,
		'score' => $score,
		'url' => implode('/', $url),
	];

	$i++;
}

usort($results, function ($a, $b) {
	return $b['score'] <=> $a['score'];
});

foreach ($results as $result) {
?>
	<a href="<?=BASE_PATH?>wiki/<?=$result['url']?>">
		<h3><?=htmlspecialchars($result['title'])?></h3>
		<p>score: <?=$result['score']?></p>
	</a>
<?php
}
?>
</div>
<?php

return true;
