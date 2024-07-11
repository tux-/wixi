<?php
declare(strict_types=1);
namespace gimle;

$slug = page('page');
if ($slug === null) {
	$slug = '';
}

?>
<div class="content">
	<div id="editorjs">
	</div>
</div>
<dialog id="postmenu" popover="auto">
	<div class="close">
		<button>x</button>
	</div>
	<h1>Postmenu</h1>
	<div class="forms">
		<form id="movepost" action="<?=BASE_PATH?>movepost" method="post">
			<input type="hidden" name="from" value="<?=htmlspecialchars($slug)?>"/>
			<input type="text" name="slug" value="<?=htmlspecialchars($slug)?>"/>
			<button>Move</button>
		</form>
		<form id="deletepost" action="<?=BASE_PATH?>deletepost" method="post">
			<input type="hidden" name="from" value="<?=htmlspecialchars($slug)?>"/>
			<button>Delete</button>
		</form>
	</div>
</dialog>
<?php

return true;
