<ul class="navigation">
	<li><a href="<?php echo FrontController::urlFor('headsup');?>" id="headsup_link">Headsup</a>
<?php if(LoginResource::isAuthorized()):?>
	<li><a href="<?php echo FrontController::urlFor('login/logout');?>">Logout</a></li>
<?php else:?>
	<li><a href="<?php echo FrontController::urlFor('login');?>" title="login">Login</a></li>
<?php endif;?>
	<li>
		<form action="<?php echo FrontController::urlFor('site');?>" method="get">
			<input name="search_term" id="search_term" class="label" type="search" value="<?php echo strlen($search_term) > 0 ? $search_term : 'Quick Search'; ?>" />
		</form>
	</li>
</ul>