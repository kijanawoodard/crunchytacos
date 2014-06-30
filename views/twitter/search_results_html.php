<style type="text/css">
	#tweets dt img{
		float: left;
		vertical-align: top;
	}
	#tweets dt span{
		color: rgb(100,100,100);
	}
	#tweets dl{
		margin-bottom: 3em;
		padding-left: 75px;
	}
	#tweets dl p a{
		color: rgb(0,0,0);
		background: rgb(235,174,82);
	}
	#tweets dl small{
		font-size: .5em;
	}
	#tweets dl small a{
		color: rgb(69,189,232);
	}
</style>
<a href="<?php echo FrontController::urlFor('twitter');?>">back</a>
<dl id="tweets">
<?php foreach($search_response->results as $tweet):?>
	<dt>
		<img src="<?php echo $tweet->profile_image_url;?>" alt="Avatar" />
		<span><?php echo $tweet->from_user;?></span>
	</dt>
	<dl>
		<p><?php echo $this->linkify_tweet($tweet->text);?></p>
		<small>
			from <?php echo html_entity_decode($tweet->source);?> 
			at <?php echo $tweet->created_at;?>
		</small>
	</dl>
<?php endforeach;?>
</dl>
