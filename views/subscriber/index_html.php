<ol>
<?php foreach($subscribers as $subscriber):?>
	<li>
		<a href="<?php echo FrontController::urlFor('subscriber', array('id'=>$subscriber->id));?>">
			<?php echo $subscriber->email;?>, <?php echo $subscriber->url;?>
		</a>
	</li>
<?php endforeach;?>
</ol>
<a href="<?php echo FrontController::urlFor('subscriber');?>" title="add a subscriber">+</a>
