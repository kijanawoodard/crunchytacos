<ol>
<?php foreach($publishers as $publisher):?>
	<li>
		<a href="<?php echo FrontController::urlFor('publisher', array('id'=>$publisher->id));?>">
			<?php echo $publisher->email;?>, <?php echo $publisher->url;?>
		</a>
	</li>
<?php endforeach;?>
</ol>
<a href="<?php echo FrontController::urlFor('publisher');?>" title="add a publisher">+</a>
