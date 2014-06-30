<div class="left ui_list_view" id="object_list">
	<div id="navigation_controller_header"><h1>Databases</h1></div>
	<div style="clear: both;"></div>
	<ul class="horizontal">
		<li class="item" id="databases_column">
			<ul id="databases">
			<?php foreach($databases as $db):?>
				<li><a href="javascript:void(0);" class="database"><?php echo $db->Database;?></a></li>
			<?php endforeach;?>
			</ul>
		</li>

		<li class="item" id="tables_column">
			<ul id="tables" style="display: none;"></ul>
		</li>
	</ul>
</div>

<div class="right">
	<div id="query_results"></div>
	<div id="query" contentEditable="true"></div>
	<a href="javascript:void(0);" id="execute_link">execute!</a>
</div>

<style type="text/css">
	#query{
		width: 700px;
		height: 210px;
		background: rgb(50,50,50);
		border: solid 1px white;
	}
	#query_results{
		width: 700px;
		height: 210px;
		border: solid 1px white;
		overflow: auto;
	}
	.ui_list_view{
		overflow-y: auto;
		overflow-x: hidden;
		width: 320px;
		height: 420px;
		border: solid 1px white;
		position: relative;
	}
	ul.horizontal{
		margin-top: 30px;
		height: 100%;
		width: 640px;
		overflow: hidden;
	}
	
	.ui_list_view ul li.item{
		height: 100%;
		width: 320px;
		float: left;
		display: block;
	}
	
	#navigation_controller_header{
		height: 30px;
		position: fixed;
	}
	#navigation_controller_header ul li{
		float: left;
	}

	textarea{
		width: 100%;
		height: 300px;
	}
	.left{
		float: left;
		width: 300px;
		position: relative;
	}
	.right{
		margin-left: 305px;
	}
	input[type=submit]{
		float: right;
	}
	li.column{
		width: 100%;
		float: left;
	}
</style>

<script type="text/javascript">
	var links = [];
	var db_name = '';
	function dbDidClick(e){
		links.each(function(a){
			a.setStyle('background', 'transparent');
			a.setStyle('color', 'white');
		});
		this.setStyle('background', 'white');
		this.setStyle('color', 'black');
		db_name = this.text;
		new Request.HTML({update:'tables', url:'<?php echo FrontController::urlFor('db/tables');?>', onSuccess:tablesViewWillLoad}).get({db_name:this.text});
	}
	function tablesViewWillLoad(responseTree, responseElements, responseHTML, responseJavaScript){
		$('tables').setStyle('display', 'block');
		var backLink = new Element('a', {href:'<?php echo FrontController::urlFor('db');?>', html:'Back'
			, events: {click: backWasClicked}});
		var li = new Element('li');
		var title = new Element('li', {html:db_name});
		var ul = new Element('ul');
		li.grab(backLink);
		ul.grab(li);
		ul.grab(title);
		$('navigation_controller_header').set('html', '');
		$('navigation_controller_header').grab(ul);
		new Fx.Tween('databases_column', {onComplete: tablesViewDidLoad}).start('margin-left', 0, -320);
	}
	function tablesViewDidLoad(elem){
	}
	function databasesViewDidLoad(){
		$('navigation_controller_header').set('html', 'Databases');
	}
	function backWasClicked(e){
		new Fx.Tween('databases_column', {onComplete: databasesViewDidLoad}).start('margin-left', -320, 0);
		return false;
	}
	function executeLinkWasClicked(e){
		var query = $('query').get('html');
		var request = new Request.HTML({url:'<?php echo FrontController::urlFor('db/query');?>', update: 'query_results', data:'db_name=' + db_name + '&query=' + query});
		request.send();
		return false;
	}
	window.addEvent('domready', function(){
		var original = {};
		var extended = {};
		var test = $extend(original, extended);
		links = $$('a.database');
		links.each(function(a){
			a.addEvent('click', dbDidClick);
		});
		
		$('execute_link').addEvent('click', executeLinkWasClicked);
	});
</script>