<section id="footer">
	<strong>&copy; me incorporated.</strong>
<?php if(LoginResource::isAuthorized()):?>
	<a href="<?php echo FrontController::urlFor('login/logout');?>">logout</a>
	<a href="<?php echo FrontController::urlFor('post');?>" title="add a post">+</a>
<?php else:?>
	<a href="<?php echo FrontController::urlFor('login');?>">login</a>
<?php endif;?>
</section>