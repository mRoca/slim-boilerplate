<?php
	 SlimBoilerplate\Layout\LayoutView::setTitle('Error');
	 SlimBoilerplate\Layout\LayoutView::setDescription('Error page');
?>

<div>
	<h1>Error</h1>
	
	<?php if (isset($flash['error'])) { ?>
		<div class="error"><?= $flash['error'] ?></div>
	<?php } ?>
</div>
