<div id="articles">
	<dl id="posts">
		<dd class="post">
			<form action="<?php echo FrontController::urlFor('friend');?>" method="post" class="body" id="friend_form">
				<fieldset>
					<legend><?php echo ($friend->name == null ? 'New friend' : $friend->name);?></legend>
					<p>
						<label for="title">Name</label>
						<input type="text" id="name" name="name" value="{$friend->name}" />
					</p>
		
					<p>
						<label for="body">Site</label>
						<input type="text" id="site" name="site" value="{$friend->site}" />
					</p>
					
					<p>
						<input type="submit" name="save_button" value="Save" />
					</p>
					
					<input type="hidden" name="id" value="{$friend->id}" />
				</fieldset>
			</form>
		</dd>
	</dl>
</div>