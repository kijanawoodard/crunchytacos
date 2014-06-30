<?php
date_default_timezone_set('US/Central');
class_exists('FrontController') || require('lib/FrontController.php');
$front_controller = new FrontController();
set_error_handler(array($front_controller, 'errorDidHappen'));
set_exception_handler(array($front_controller, 'exceptionDidHappen'));
echo $front_controller->execute();
?>