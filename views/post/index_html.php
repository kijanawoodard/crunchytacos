<section id="posts">
<?php if($posts == null):?>
	<p>There are no posts right now.</p>
	<a href="<?php echo FrontController::urlFor('post/new');?>">Create a new one</a>
<?php else:?>
	<ol class="posts">
<?php foreach($posts as $post):?>
		<li class="post">
			<div class="body">
				<h1><?php echo $post->title;?></h1>	
				<?php echo $post->body;?>
			</div>
			<div class="footer">
<?php if(LoginResource::isAuthorized()):?>
				<a href="<?php echo FrontController::urlFor('post', array('id'=>$post->id));?>">edit</a>
<?php endif;?>
				<p class="date"><?php echo $post->date;?></p>
			</div>
		</li>
<?php endforeach;?>
	</ol>
<?php endif;?>
</section>