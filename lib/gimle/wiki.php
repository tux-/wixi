<?php
declare(strict_types=1);
namespace gimle;

use \gimle\xml\SimpleXmlElement;
use \gimle\xml\Xsl;

class Wiki
{
	public static function getSlug (string $file): string
	{
		if (str_ends_with($file, '.xml')) {
			$file = substr($file, 0, -4);
		}

		$url = explode('/', $file);
		array_walk($url, function (&$value) {
			$value = rawurlencode($value);
		});

		return implode('/', $url);
	}

	public static function xmlFilename (string $page): string
	{
		$file = rawurldecode($page);
		if ($file !== '') {
			$file = substr($file, strlen('wiki/'));
		}
		else {
			$file = 'wiki';
		}
		foreach (explode('/', $file) as $part) {
			if ((str_starts_with($part, ' ')) || (str_ends_with($part, ' '))) {
				throw new Exception('Invalid path.');
			}
		}
		$file = STORAGE_DIR . 'xml/wiki/' . $file;
		if (str_contains($file, ['.', '//', '\'', '#', "\n", "\t"])) {
			throw new Exception('Invalid path.');
		}
		if (str_ends_with($file, '/')) {
			$file = substr($file, 0, -1);
		}
		$file .= '.xml';
		if (filter_var($file, FILTER_VALIDATE_DIRNAME) === false) {
			throw new Exception('Invalid path.');
		}
		return $file;
	}

	public static function slugToFilename (string $slug): string
	{
		if ($slug === '') {
			return STORAGE_DIR . 'xml/wiki/wiki.xml';
		}

		foreach (explode('/', $slug) as $part) {
			if ((str_starts_with($part, ' ')) || (str_ends_with($part, ' '))) {
				throw new Exception('Invalid path.');
			}
		}
		$file = STORAGE_DIR . 'xml/wiki/' . $slug;
		if (str_contains($file, ['.', '//', '\'', '#', "\n", "\t"])) {
			throw new Exception('Invalid path.');
		}
		if (str_ends_with($file, '/')) {
			$file = substr($file, 0, -1);
		}
		$file .= '.xml';
		if (filter_var($file, FILTER_VALIDATE_DIRNAME) === false) {
			throw new Exception('Invalid path.');
		}
		return $file;
	}

	public static function xmlToJson (SimpleXmlElement $section, array &$json, int $level = 1): array
	{
		foreach ($section->xpath('./*') as $node) {
			$name = $node->getName();
			if ($name === 'header') {
				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'header',
					'data' => [
						'text' => self::inlineToHtml($node),
						'level' => $level,
					],
				];
				continue;
			}
			if ($name === 'p') {
				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'paragraph',
					'data' => [
						'text' => self::inlineToHtml($node),
					],
				];
				continue;
			}
			if ($name === 'code-block') {
				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'code',
					'data' => [
						'code' => (string) $node,
						'languageCode' => (string) $node['codelang'],
					],
				];
				continue;
			}
			if ($name === 'list') {
				$items = [];
				if ((string) $node['checkboxes'] === 'true') {
					foreach ($node->xpath('./li') as $item) {
						$items[] = [
							'text' => self::inlineToHtml($item->p),
							'checked' => ((string) $item['checked'] === 'true' ? true : false),
						];
					}
					$json['blocks'][] = [
						'id' => self::getId($node),
						'type' => 'checklist',
						'data' => [
							'items' => $items,
						],
					];
					continue;
				}

				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'list',
					'data' => [
						'style' => ((string) $node['type'] === 'decimal' ? 'ordered' : 'unordered'),
						'items' => self::xmlToJsonNestedUl($node),
					],
				];
				continue;
			}
			if ($name === 'alert') {
				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'alert',
					'data' => [
						'type' => (string) $node['type'],
						'align' => (string) $node->p['align'],
						'message' => self::inlineToHtml($node->p),
					],
				];
				continue;
			}
			if ($name === 'hr') {
				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'delimiter',
					'data' => [
					],
				];
				continue;
			}
			if ($name === 'table') {
				$content = [];
				foreach ($node->xpath('./*/tr') as $tr) {
					$row = [];
					foreach ($tr->xpath('./*/p') as $data) {
						$row[] = self::inlineToHtml($data);
					}
					$content[] = $row;
				}
				$json['blocks'][] = [
					'id' => self::getId($node),
					'type' => 'table',
					'data' => [
						'withHeadings' => (current($node->xpath('.//thead/tr')) ? true : false),
						'content' => $content,
					],
				];
				continue;
			}
			if ($name === 'section') {
				self::xmlToJson($node, $json, $level + 1);
			}
		}
		return $json;
	}

	public static function jsonToXml (array $json): string
	{
		libxml_use_internal_errors(true);

		$sxml = new SimpleXmlElement('<editorjs/>');

		$sxml['time'] = $json['time'];
		$sxml['version'] = $json['version'];

		function parseRich ($html, $name)
		{
			$dom = new \DomDocument();
			$dom->loadHtml("<?xml version='1.0' encoding='utf-8'?>\n" . '<' . $name . '>' . $html . '</' . $name . '>');
			$resolved = simplexml_import_dom($dom, '\\gimle\\xml\\SimpleXmlElement');

			$xsl = new Xsl();
			$xsl->stylesheet(SITE_DIR . 'xsl/html.xsl');

			$sxml = new SimpleXmlElement($xsl->render($resolved));

			return $sxml;
		}

		Wiki::jsonBlocksToXml($json, $sxml);

		$xsl = new Xsl();
		$xsl->stylesheet(SITE_DIR . 'xsl/result.xsl');

		$result = str_replace('&#13;', "\n", tab_indent($xsl->render($sxml), 2));

		return $result;
	}

	private static function getId (SimpleXmlElement $node): string
	{
		$id = (string) $node['id'];
		if ($id === '') {
			$id = random(null, 10);
		}
		return $id;
	}

	private static function xmlToJsonNestedUl (SimpleXmlElement $node): array
	{
		$items = [];
		if ((bool) $node === true) {
			foreach ($node->xpath('./li') as $item) {
				$items[] = [
					'content' => self::inlineToHtml($item->p),
					'items' => self::xmlToJsonNestedUl($item->list),
				];
			}
		}
		return $items;
	}

	private static function jsonBlocksToXmlNestedUl (array $json, SimpleXmlElement $node)
	{
		foreach ($json['items'] as $item) {
			if ((string) $item['content'] !== '') {
				$child = $node->addChild('li');
				$child->insertLast(parseRich($item['content'], 'p'));
				if (!empty($item['items'])) {
					$list = $child->addChild('list');
					self::jsonBlocksToXmlNestedUl($item, $list);
					if ($list->count() === 0) {
						$list->remove();
					}
				}
			}
		}
	}

	private static function jsonBlocksToXml (array $json, SimpleXmlElement &$sxml): void
	{
		$section = $sxml;
		foreach ($json['blocks'] as $block) {
			if ($block['type'] === 'header') {
				$xp = explode('/', $section->getXpath());
				$level = 1;
				foreach ($xp as $tag) {
					if ($tag === 'section') {
						$level++;
					}
				}
				if ($level < $block['data']['level']) {
					while ($level < $block['data']['level']) {
						$section = $section->addChild('section');
						$level++;
					}
				}
				elseif ($level > $block['data']['level']) {
					while ($level > $block['data']['level']) {
						$section = $section->getParent();
						$level--;
					}
				}
				$section->insertLast(parseRich($block['data']['text'], 'header'));
				$node = current($section->xpath('./header[last()]'));
				$node['id'] = $block['id'];
				continue;
			}

			if ($block['type'] === 'paragraph') {
				$section->insertLast(parseRich($block['data']['text'], 'p'));
				$node = current($section->xpath('./p[last()]'));
				$node['id'] = $block['id'];
				continue;
			}

			if ($block['type'] === 'code') {
				$node = $section->addChild('code-block');
				$node['id'] = $block['id'];
				$node['codelang'] = $block['data']['languageCode'];
				$node[0] = str_replace(["\r", "\n"], ['', "\r"], $block['data']['code']);
				continue;
			}

			if ($block['type'] === 'list') {
				$node = $section->addChild('list');
				$node['id'] = $block['id'];
				$node['type'] = ($block['data']['style'] === 'ordered' ? 'decimal' : 'bullet');
				self::jsonBlocksToXmlNestedUl($block['data'], $node);
				continue;
			}

			if ($block['type'] === 'checklist') {
				$node = $section->addChild('list');
				$node['id'] = $block['id'];
				$node['checkboxes'] = 'true';
				foreach ($block['data']['items'] as $item) {
					$child = $node->addChild('li');
					$child = current($node->xpath('./li[last()]'));
					$child['checked'] = ($item['checked'] === true ? 'true' : 'false');
					$child->insertLast(parseRich($item['text'], 'p'));
				}
				continue;
			}

			if ($block['type'] === 'alert') {
				$node = $section->addChild('alert');
				$node['id'] = $block['id'];
				$node['type'] = $block['data']['type'];
				$node->insertLast(parseRich($block['data']['message'], 'p'));
				$p = current($node->xpath('./p[last()]'));
				$p['align'] = $block['data']['align'];
				continue;
			}

			if ($block['type'] === 'delimiter') {
				$node = $section->addChild('hr');
				$node['id'] = $block['id'];
				continue;
			}

			if ($block['type'] === 'table') {
				$node = $section->addChild('table');
				$node['id'] = $block['id'];
				$tbody = null;
				foreach ($block['data']['content'] as $index => $row) {
					if ($index === 0) {
						if ($block['data']['withHeadings'] === true) {
							$thead = $node->addChild('thead');
							$tr = $thead->addChild('tr');
							foreach ($row as $col) {
								$th = $tr->addChild('th');
								$th->insertLast(parseRich($col, 'p'));
							}
							continue;
						}
					}
					if ($tbody === null) {
						$tbody = $node->addChild('tbody');
					}
					$tr = $tbody->addChild('tr');
					foreach ($row as $col) {
						$td = $tr->addChild('td');
						$td->insertLast(parseRich($col, 'p'));
					}
				}
				continue;
			}
		}
	}


	private static function inlineToHtml (SimpleXmlElement $node): string
	{
		foreach ($node->xpath('.//marker') as &$child) {
			$child['class'] = 'cdx-marker';
			$child->rename('mark');
		}
		foreach ($node->xpath('.//code-inline') as &$child) {
			$child['class'] = 'inline-code';
			$child->rename('code');
		}
		return implode('', $node->innerXml());
	}
}
