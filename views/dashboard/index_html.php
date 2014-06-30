<div id="body_left">
	<h4>Profile Pic</h4>
    <img src="<?php echo FrontController::urlFor(null);?>images/erikb.jpg" id="current_pic" />
</div>
<div id="body_main">
    <div id="form_panels">
        <div id="panel1" class="panels">
        	<h2>Edit Your Profile</h2>
            <form id="profile_panel">
            	<label for="press_title">Title:</label><br>
				<input type="text" name="press_title" id="press_title" />
                <br>
				<br>
				<label for="profile_desc">Description</label><br>
				<input type="text" name="profile_desc" id="profile_desc" />
                <br>
				<br>
				<label for="profile_pic">Profile Picture</label><br>
                <input type="text" name="profile_pic" id="profile_pic" /> <input type="submit" name="submit" value="browse" class="button" />
                <br>
				<br>
				<input type="submit" name="submit" value="submit" class="button" />
            </form>
        </div>
        <div id="panel2" class="panels">
			<?php require('views/post/new_post_html.php');?>
		</div>
        <div id="panel3" class="panels">Panel 3</div>
        <div id="panel4" class="panels">Panel 4</div>
        <div id="panel5" class="panels">Panel 5</div>
        <div id="panel6" class="panels">Panel 6</div>
    </div>              
</div>
<div id="body_right">
	Profile stuff, friends, other shit
</div>  
