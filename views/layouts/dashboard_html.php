<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!--
	          d8b                                            
	          Y8P                                            

	 .d88b.  8888 88888b.  888d888 .d88b.  .d8888b  .d8888b  
	d8P  Y8b "888 888 "88b 888P"  d8P  Y8b 88K      88K      
	88888888  888 888  888 888    88888888 "Y8888b. "Y8888b. 
	Y8b.      888 888 d88P 888    Y8b.          X88      X88 
	 "Y8888   888 88888P"  888     "Y8888   88888P'  88888P' 
	          888 888                                        
	         d88P 888                                        
	       888P"  888

-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>        
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width = 900"/>
    <!-- <link rel="icon" href="" type="image/gif"/> -->
    <link rel="apple-touch-icon" href="<?php echo FrontController::urlFor(null);?>images/favicon.png" />
    <script type="text/javascript" language="javascript" src="<?php echo FrontController::urlFor(null);?>js/mootools.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo FrontController::urlFor(null);?>js/mooslides.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo FrontController::urlFor(null);?>js/mootools-more.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/global.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/dashboard.css"/>
	<title>ejPress: {$title}</title>
</head>
<body>
	<div id="header">
    	<div class="content">
        	<h1>Joey Guerra DOT COM!</h1>
            <ul id="dashboard_nav">
                    <li><a href="javascript: myslides.slideTo(0)" id="link_profile">Profile</a></li>
                    <li><a href="javascript: myslides.slideTo(1)" id="link_text">Text</a></li>
                    <li><a href="javascript: myslides.slideTo(2)" id="link_photo">Photo</a></li>
                    <li><a href="javascript: myslides.slideTo(3)" id="link_quote">Link</a></li>
                    <li><a href="javascript: myslides.slideTo(4)" id="link_link">Chat</a></li>
                    <li><a href="javascript: myslides.slideTo(5)" id="link_chat">Media</a></li>
                    <li class="menu"><a href="#" id="link_apps">Apps</a>
                    	<ul class="links">
                        	<li><a href="#">App 1</a></li>
                            <li><a href="#">Widget 2</a></li>
                            <li><a href="#">Thing 3</a></li>
                        </ul>
                    </li>
            </ul>
        </div>
    </div>
    <div id="body">
    	<div class="content">
			
			{$output}
        </div>
    </div>
    <div id="footer">
    	<div class="content">
        	&copy; 2009 JOEY G
        </div>
    </div>
    <script type="text/javascript">
		window.addEvent('domready', function() {
			// Creates a new mooslides object will all possible options
			myslides = new mooslides('form_panels', {
				customToolbar: true,
			});
			$('dashboard_nav').getElements('li.menu').each( function( elem ){
		var list = elem.getElement('ul.links');
		var myFx = new Fx.Slide(list).hide();
		elem.addEvents({
			'mouseenter' : function(){
				myFx.cancel();
				myFx.slideIn();
			},
			'mouseleave' : function(){
				myFx.cancel();
				myFx.slideOut();
			}
		});
	})

		});
	</script>

</body>
</html>