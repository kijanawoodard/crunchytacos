<div id="articles">
	<dl id="posts">
		<dd class="post">
			<form action="<?php echo FrontController::urlFor('subscriber');?>" method="post" class="body" id="subscriber_form">
				<fieldset>
					<legend><?php echo ($subscriber->email == null ? 'New subscriber' : $subscriber->email);?></legend>
					<p>
						<label for="title">Email</label>
						<input type="text" id="email" name="email" value="{$subscriber->email}" />
					</p>
		
					<p>
						<label for="body">Url</label>
						<input type="text" name="url" id="url" value="{$subscriber->url}" />
					</p>
					<p>
						<label for="is_approved">Approved?</label>
						<input type="checkbox" id="is_approved" name="is_approved" value="true"<?php echo $subscriber->is_approved ? ' checked="true"' : null;?> />
					</p>
					<p>
						<input type="submit" name="save_button" value="Save" />
					</p>
					<input type="hidden" name="id" id="id" value="{$subscriber->id}" />
				</fieldset>
			</form>
			<form action="<?php echo FrontController::urlFor('subscriber');?>" method="post">
				<input type="hidden" name="id" id="id" value="{$subscriber->id}" />
				<input type="hidden" name="_method" value="delete" />
				<input type="submit" name="delete_button" value="Delete" />
			</form>
			
			<div class="footer">			
			</div>
		</dd>
	</dl>
</div>

