<h1>Current Twitter Trend</h1>
<?php foreach($trends->trends as $key=>$trend):?>
	<dl id="trends">
		<dt>as of <?php echo $key;?></dt>
		<?php foreach($trend as $obj):?>
		<dd>
			<a href="<?php echo FrontController::urlFor('twitter', array('query'=>urlencode($obj->query)), false);?>" title="<?php echo $obj->name;?>">
				<img src="http://images.easyimg.com/925f1d0d/r-12/bw-1/s-13/p-3/pl-10/pr-10/b/c-000000/fc-ffffff/link.png?t=<?php echo urlencode($obj->name);?>" title="<?php echo $obj->name;?>" />
			</a>
		</dd>
		<?php endforeach;?>
	</dl>
<?php endforeach;?>
<style type="text/css">
	#trends dd a{
	}
</style>