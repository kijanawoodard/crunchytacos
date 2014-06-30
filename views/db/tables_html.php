<ul>
<?php foreach($tables as $table):?>
	<li>
		<a href="javascript:void(0);" class="tables" title="<?php echo $table->$field_name;?>"><?php echo $this->forDisplay($table->$field_name);?></a>
	</li>
<?php endforeach;?>
</ul>
