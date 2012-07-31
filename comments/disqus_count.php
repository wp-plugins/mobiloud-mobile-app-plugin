<?php
include("../../../../wp-blog-header.php");

$postID = $_GET["post_id"];
$site_url = network_site_url("/");
$disqus_identifier_string = "$postID $site_url?p=$postID";
$post_permalink = get_permalink($postID);
$post_title = get_the_title($postID);

?>
<html>
	<style>
		html, body{background:transparent; width:100%;}
		
		.dsq-link a:link,
		.dsq-link a:visited,
		.dsq-link a:hover
		{
			font-family:helvetica;
			font-weight:bold;
			text-decoration:none;
			font-size:12;
			color:#444;
		}

	</style>
<body>
	<table width="100%" style="position:absolute;top:0px;left:-1px;">
		<tr valign="top">
			<td align="center">
				<div class="dsq-link">
					<a href="<?php echo $post_permalink;?>#disqus_thread">
				        <span class="dsq-postid" rel="<?php echo $disqus_identifier_string;?>">
							...
						</span>
				     </a>
				</div>
			</td>
		</tr>
	</table>
	<script type="text/javascript">

		var disqus_shortname = "<?php echo $_GET['shortname'];?>";
	    var disqus_domain = 'disqus.com';
       
		(function () {
            var s = document.createElement('script'); s.async = true;
            s.type = 'text/javascript';
            s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
            (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
        }());

     </script>

</body>
</html>
