<div id="articles">
	<dl id="posts">
		<dd class="post">
			<form action="<?php echo FrontController::urlFor('publisher');?>" method="post" class="body" id="publisher_form">
				<fieldset>
					<legend><?php echo ($publisher->email == null ? 'New publisher' : $publisher->email);?></legend>
					<p>
						<label for="title">Email</label>
						<input type="text" id="email" name="email" value="{$publisher->email}" />
					</p>
		
					<p>
						<label for="body">Url</label>
						<input type="text" name="url" id="url" value="{$publisher->url}" />
					</p>
					<p>
						<label for="is_approved">Approved?</label>
						<input type="checkbox" id="is_approved" name="is_approved" value="true"<?php echo $publisher->is_approved ? ' checked="true"' : null;?> />
					</p>
					<p>
						<input type="submit" name="save_button" value="Save" />
					</p>
					<input type="hidden" name="id" id="id" value="{$publisher->id}" />
				</fieldset>
			</form>
			<form action="<?php echo FrontController::urlFor('publisher');?>" method="post">
				<input type="hidden" name="id" id="id" value="{$publisher->id}" />
				<input type="hidden" name="_method" value="delete" />
				<input type="submit" name="delete_button" value="Delete" />
			</form>
			
			<div class="footer">			
			</div>
		</dd>
	</dl>
</div>

