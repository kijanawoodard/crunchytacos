<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo($title);?></title>
        <link rel="stylesheet" href="<?php echo($themePath);?>/css/index.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo($themePath);?>/css/error.css" type="text/css" />
    </head>
    <body id="error-page">
		<?php echo(Controller::getErrorMessage());?>
		<?php echo($output);?>
    </body>
</html>