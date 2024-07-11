<?php
declare(strict_types=1);
namespace gimle;

use \gimle\canvas\Canvas;

header('Content-type: text/html; charset=' . mb_internal_encoding());

Canvas::title(Config::get('sitename'));
?>
<!doctype html>
<html lang="%lang%">
	<head>
		<meta charset="<?=mb_internal_encoding()?>">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>%title%</title>
		<meta name="description" content="%description%">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="shortcut icon" href="<?=getPublicResource('favicon.png')?>">

		<script src="<?=BASE_PATH?>editorjs/editorjs.js"></script>

		<script src="<?=BASE_PATH?>editorjs/header.js"></script>
		<script src="<?=BASE_PATH?>editorjs/delimiter.js"></script>
		<script src="<?=BASE_PATH?>editorjs/nested-list.js"></script>
		<script src="<?=BASE_PATH?>editorjs/checklist.js"></script>
		<script src="<?=BASE_PATH?>editorjs/code.js"></script>
		<script src="<?=BASE_PATH?>editorjs/table.js"></script>
		<script src="<?=BASE_PATH?>editorjs/inline-code.js"></script>
		<script src="<?=BASE_PATH?>editorjs/underline.js"></script>
		<script src="<?=BASE_PATH?>editorjs/alert.js"></script>
		<script src="<?=BASE_PATH?>editorjs/marker.js"></script>
		<script src="<?=BASE_PATH?>editorjs/strikethrough.js"></script>

		<link rel="stylesheet" href="<?=getPublicResource('css/index.css')?>"/>

		<script src="<?=getPublicResource('js/gimle.js')?>"></script>
		<script>
			gimle.BASE_PATH = '<?=BASE_PATH?>';
		</script>
		<script src="<?=getPublicResource('js/default.js')?>"></script>
	</head>
	<body>
		<header>
			<div>
				<div>
					<h1><a href="<?=BASE_PATH?>">Wixi</a></h1>
				</div>
				<div id="toptools">
					<div>
						<form action="<?=BASE_PATH?>">
							<input name="q" type="search" value="<?=(isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '')?>" placeholder="search"/>
						</form>
						<button data-editorjs="save">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
							</svg>
							<span>Save</span>
						</button>
						<button data-action="settings">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
							</svg>
						</button>
						<button data-action="menu">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</header>
		<div id="menu">
			<div>
				<div id="innermenu">
					<button id="toggletheme"></button>
					<ul>
						<li><a href="<?=BASE_PATH?>">Home</a></li>
						<li><a href="<?=BASE_PATH?>modified">Last modified</a></li>
						<li><a href="<?=BASE_PATH?>browse">Browse</a></li>
					</ul>
				</div>
			</div>
		</div>
		%content%
	</body>
</html>
<?php
return true;
