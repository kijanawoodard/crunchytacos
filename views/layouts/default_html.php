<?php
	class_exists('LoginResource') || require('resources/LoginResource.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Joey's log: {$title}</title>
		<link rel="icon" href="images/j.png"/>
		<meta name="description" content="This is my personal log. I'm Joey, a web developer in Coppell, Tx."/>
		<meta name="keywords" content="joey guerra, personal log"/>
		<meta name="viewport" content="width=980"/>
		<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/reset.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/default.css" media="all" />
		{$resource_css}
		<script type="text/javascript" language="javascript" src="<?php echo FrontController::urlFor(null);?>js/mootools-core.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo FrontController::urlFor(null);?>js/mootools-more.js"></script>
	</head>
	<body>
		<header>
			<h1><span>Joey's Log</span></h1>
		</header>
		<section id="me" rel="author">
			<a href="<?php echo FrontController::urlFor(null);?>" title="Joey Guerra" class="avatar">
				<img src="<?php echo UserResource::avatar(220);?>" />
			</a>
			<p>"Joey is a thoughtful, smart web developer. I love being him."</p>
			<p>- Joey</p>
		</section>
		<section id="content">
			<section id="parent">
				<div class="user_message">
					<?php echo AppResource::getUserMessage();?>
				</div>
				{$output}
			</section>
		</section>
		<section style="clear: both;height: 60px;"></section>
		<?php require('footer.php');?>       
		<noscript></noscript>
	</body>
</html>
