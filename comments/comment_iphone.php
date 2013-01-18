<?php


function ml_comment_content_to_html($content)
{
	return str_replace("\n","<br>",$content);
}


function ml_render_iphone_comment($comment)
{
	?>
	<div class="ml_comment">

		<div style="float:left;">
			<div class="ml_comment_avatar">
				<?php $uid_or_email = $comment->user_id != 0 ? $comment->user_id : $comment->comment_author_email;
					  $link = ml_facebook_get_picture_url($uid_or_email);
					  if($link) echo "<img src='$link' class='avatar avatar-50 photo' height=50 width=50>";
					  else echo get_avatar($uid_or_email,50);
				?>
			</div>
		</div>
		<div style="float:left;margin-left:20px;">
			<div class="ml_comment_author">
				<?php echo $comment->comment_author;?>
			</div>			
			<div class="ml_comment_text">
				<?php echo ml_comment_content_to_html($comment->comment_content);?>
			</div>

			<div class="ml_comment_bottom">
				<div class="ml_comment_date">
					<?php echo mysql2date('l, F j, Y. G:i',$comment->comment_date);?>				
				</div>
			</div>
		</div>
		
		
		<div style="clear:both;"></div>
		
	</div>
	<?php
}

?>