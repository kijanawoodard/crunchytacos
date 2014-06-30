<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
        <title>Fishing Maps for Texas, California, and Louisiana, Mississippi, Oklahoma: {$title}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="verify-v1" content="ZwoL06lbMk5FGm5CHmWzc7sfw5GIAIn6zhkqMpuYN0c=" >
		<meta name="author" content="Hook-N-Line fishing maps" />
		<meta name="keywords" content="fishing map,fishing lake map,fishing hot spot map,louisiana fishing map,fishing map texas,fishing hotspots map,gulf of mexico fishing map,galveston fishing map, california, san francisco" />
		<meta name="description" content="Texas fishing maps, California fishing maps, Louisiana fishing maps, Mississippi fishing maps, and Oklahoma fishing maps." />
		<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/reset.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/default.css" media="all" />
		{$resource_css}
		
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		
		<script type="text/javascript">
			jQuery(document).ready(function(){
				if(jQuery('#same_as_shipping').length > 0){
					jQuery('#same_as_shipping').change(function(){
						if(this.checked){
							jQuery('#billing_address p').fadeOut('fast');
						}else{
							jQuery('#billing_address p').fadeIn('fast');
						}
					});
				}
			});
		</script>
    </head>
    <body>
		<?php require('menu.php'); ?>
		<div class="header">
			<h1><a href="<?php echo FrontController::urlFor(null);?>"><span>Hook-N-Line fishing and boating maps</span></a></h1>
			<hcard>
				If you have trouble placing a map order, call 
				<phone class="office">Phone: 281-286-6554</phone> for assistance.
			</hcard>
		</div>	
		<div id="container">
			<?php echo AppResource::getUserMessage();?>
			<div class="section only">
				<?php echo($output);?>
			</div>
			<div style="clear: both;height: 20px;"></div>
	    </div>
		<div class="footer">
            <p><span>&copy; <?php echo date("Y");?>. Hook-N-Line Inc. All rights reserved.</span></p>
			<div id="count"></div>
 			<?php require('footer.php');?>
       </div>
	</body>
</html>