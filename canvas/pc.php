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
		<script src="<?=BASE_PATH?>editorjs/list.js"></script>
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
							Save
						</button>
					</div>
				</div>
			</div>
		</header>
		%content%
	</body>
</html>
<?php
return true;
