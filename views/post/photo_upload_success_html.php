<script type="text/javascript">
<?php if(empty($photo['error_message'])):?>
	top.photoWasUploaded('{$photo_name}', '{$file_name}');
<?php else:?>
	alert('<?php echo $photo['error_message'];?>');
<?php endif;?>
</script>