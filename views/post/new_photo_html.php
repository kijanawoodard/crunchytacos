<dl id="posts">
	<dd class="post">
		<form action="<?php echo FrontController::urlFor('post');?>" method="post" id="main_form" target="upload_target" enctype="multipart/form-data" class="body">
			<fieldset>
				<legend>New photo</legend>
				<p>
					<label for="title">Caption</label>
					<input type="text" id="title" name="title" value="" />
				</p>
				<p>
					<label for="photo" id="photo_label">Choose a photo to upload</label>
					<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
					<input type="file" name="photo" id="photo" />
					<iframe id="upload_target" name="upload_target" style="width:75px;height:75px;border:1px solid #fff;"></iframe>
					<ul id="photo_list"></ul>
				</p>
				<p>
					<label for="body">Description</label>
					<textarea name="description" id="description" cols="50" rows="20"></textarea>
				</p>
		
				<p>
					<input type="submit" value="Save" />
				</p>
		
				<input type="hidden" name="type" value="photo" />
			</fieldset>
		</form>
		<div class="footer"></div>
	</dd>
</dl>
<script type="text/javascript">
	window.addEvent('domready', function(){
		$('photo').addEvent('change', function(e){
			$('main_form').submit();
		});
	});
	function photoWasUploaded(value){
		$('photo_list').adopt(new Element('li', {html: value}));
	}
</script>
