<div id="articles">
	<dl id="posts">
		<dd class="post">
			<form action="<?php echo FrontController::urlFor('post');?>" method="post" class="body" id="post_form">
				<fieldset>
					<legend><?php echo ($post->title == null ? 'New post' : $post->title);?></legend>
					<p>
						<label for="title">Title</label>
						<input type="text" id="title" name="title" value="{$post->title}" />
					</p>
		
					<p>
						<label for="body">Post</label>
						<textarea name="body" id="body" cols="50" rows="20">{$post->body}</textarea>
					</p>
					<p>
						<label for="is_published">Published?</label>
						<input type="checkbox" id="is_published" name="is_published" value="true"<?php echo $post->is_published ? ' checked="true"' : '';?> />
					</p>
					<p>
						<input type="submit" name="save_button" value="Save" />
					</p>
					
					<input type="hidden" name="type" id="type" value="{$post->type}" />
					<input type="hidden" name="id" value="{$post->id}" />
				</fieldset>
			</form>
			<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" action="<?php echo FrontController::urlFor('post/photo');?>">
				<input type="hidden" value="put" name="_method" />
				<fieldset>
					<legend>Media</legend>
					<label for="photo" id="photo_label">Add a photo</label>
					<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
					<input type="file" name="photo" id="photo" />
					<iframe src="<?php echo FrontController::urlFor('empty');?>" id="upload_target" name="upload_target" style="width:0;height:0;border:none;"></iframe>
					<ul id="photo_list"></ul>
				</fieldset>
			</form>
			<div class="footer">			
			</div>
		</dd>
	</dl>
</div>
<script type="text/javascript">
	window.addEvent('domready', function(){
		$('photo').addEvent('change', function(e){
			if($('photo_names[' + this.value + ']')){
				alert("you've already added that photo.");
				return false;
			}else{
				$('media_form').submit();				
			}
		});		
	});
	
	function photoWasUploaded(photo_name, file_name){
		$('photo').set('value', null);
		$('photo_list').adopt(new Element('li', {html: photo_name}));
		$$('#post_form fieldset')[0].adopt(new Element('input', {id: 'photo_names[' + photo_name + ']', type: 'hidden', name: 'photo_names[]', value: photo_name + '=' + file_name}));
	}
</script>

