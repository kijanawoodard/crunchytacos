<section id="posts">
<?php if($friends == null):?>
	<p>You have no friends right now:(</p>
	<a href="<?php echo FrontController::urlFor('friend');?>">Make a new one</a>
<?php else:?>
	<ol class="posts">
<?php foreach($friends as $friend):?>
		<li class="post">
			<div class="body">
				<h1><?php echo $friend->name;?></h1>	
				<?php echo $friend->site;?>
			</div>
			<div class="footer">
<?php if(LoginResource::isAuthorized()):?>
				<a href="<?php echo FrontController::urlFor('friend', array('id'=>$friend->id));?>">edit</a>
<?php endif;?>
			</div>
		</li>
<?php endforeach;?>
	</ol>
<?php endif;?>
</section>
