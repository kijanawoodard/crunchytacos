<style type="text/css">
	dl dd{
		float: left;
		position: relative;
	}
	#zoom{
		width: 100%;
		height: 100%;
	}
	#zoom p{
		margin-bottom: 0;
	}
	dl dd label{
		display: block;
		position: absolute;
	}
	dl dd a.share{
		position: absolute;
		bottom: 0;
		right: 0;
		color: rgb(255,255,255);
	}
	
	#email_view{
		position: absolute;
		top: -202px;
		left: 50%;
		margin-left: -150px;
		width: 300px;
		height: 200px;
		z-index: 3;
		background: rgb(255,255,255);
		border: solid 1px rgb(100,100,100);
	}
</style>
<script type="text/javascript">
	var images = new Array();
	var temp_image = null;
</script>
<div id="email_view" style="display: block;">
	<form id="email_form" action="<?php echo FrontController::urlFor('sharing', null, false);?>" method="post">
		<fieldset>
			<legend>Share a photo</legend>
			<img src="" id="photo_to_share" width="50" height="50" />
			<input id="url_to_share" type="hidden" name="url" value="" />
			<p>
				<label for="email">Email</label>
				<input type="text" name="email" id="email" />
			</p>
			
			<p>
				<label for="message">Message</label>
				<input type="text" name="message" id="message" />
			</p>
			
			<p>
				<input type="reset" value="Nevermind" id="cancel_button" />
				<input type="submit" value="Share It" id="share_button" />
			</p>
		</fieldset>
	</form>
</div>
<dl>
<?php for($i=0; $i < count($images)-1; $i++):?>
	<?php $image = $images[$i];?>
	<script type="text/javascript">
		temp_image = new Image();
		temp_image.src = '<?php echo $this->getBigSrc($image->src);?>';
		temp_image.lowsrc = '<?php echo $this->getLittleSrc($image->src);?>';
		temp_image.onload = function(e){
			//jQuery('#thumb_<?php echo $i;?>').text(this.width + 'x' + this.height);
		};
		images.push(temp_image);
	</script>
		<dd>
			<label id="thumb_<?php echo $i;?>"></label>
			<a class="thumbnail" href="<?php echo $this->getBigSrc($image->src);?>" title="">
				<img src="<?php echo $this->getLittleSrc($image->src);?>" id="photo_<?php echo $i;?>" />
			</a>
			<a class="share" href="#" rel="<?php echo $this->getBigSrc($image->src);?>">share</a>
		</dd>
<?php endfor;?>
</dl>

<script type="text/javascript" charset="utf-8">
	var Controller = {
		selected_image_url: null
		, shareWasClicked: function(e){
			if(e && e.target.rel){
				Controller.selected_image_url = e.target.rel;
				$('#url_to_share').attr('value', e.target.rel);
				$('#photo_to_share').attr('src', e.target.rel);
			}
			var email_view = $('#email_view');
			if(email_view.css('top') == '0px'){
				$('#email_view').animate({
					top: -201
				}, 250);
			}else{
				$('#email_view').animate({
					top: 0
				}, 250);
			}
		}
		, emailShouldSend: function(e){
			$.post(this.action, $(this).serialize(), Controller.emailWasSent);
			return false;
		}
		, emailWasSent: function(data, status){
			Controller.shareWasClicked(null);
			var elem = $('img[src=' + Controller.selected_image_url + ']:last');
			Controller.fire(document.getElementById(elem.attr('id')), 'click');
		}
		, fire: function(element, event_name) {
		    if (document.createEventObject){
		        // dispatch for IE
		        var evt = document.createEventObject();
		        return element.fireEvent('on'+event_name,evt)
		    }else{
		        // dispatch for firefox + others
		        var evt = document.createEvent("HTMLEvents");
		        evt.initEvent(event_name, true, true ); // event type,bubbling,cancelable
		        return !element.dispatchEvent(evt);
		    }
		}
	};
	
	$(document).ready(function() {
		$('a.thumbnail').fancyZoom({zoom_image: true, width: 600, height: 800, scaleImg: true, closeOnClick: true, directory: '<?php echo FrontController::urlFor(null);?>fz/images'});
		
		$('a.share').click(Controller.shareWasClicked);
		$('#cancel_button').click(Controller.shareWasClicked);
		$('#email_form').submit(Controller.emailShouldSend);
	});
</script>
