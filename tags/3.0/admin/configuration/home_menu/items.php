<div id='ml_create_home_menu_item'>
	<table>
		<tr>
			<td><h2>Select new item</h2></td>
			<td>
				<select name="menu[type]">
					<option value='0'>Select item type...</option>
					<option value='page'>Page</option>
					<option value='category'>Category</option>
					<option value='link'>Link</option>
				</select>
			</td>
		</tr>
	</table>
	<div class='ml-home-menu-fields'>
		<div class='category'>
		</div>
		<div class='link'></div>

		<h2 class='page'>Page</h2>

		<div class='image'>
			<center>
				<input type='hidden' name="menu[image]" placeholder="Image URL"/>
				<img src='http://placehold.it/150&text=Upload+Image'>
				<br>
				<div class='button action upload-btn'>Upload</div>
			</center>
		</div>


		<p>&nbsp;</p>
		<div class='page'>
			<div class='page-id-select'>
				<select name="menu[page_id]">
				<option>Select a page...</option>
				<?php
					foreach(get_pages() as $page) {
						echo "<option value='$page->ID'>$page->post_title</option>";
					}
				?>
				</select>
			</div>
		</div>

		<input type="text" name="menu[title]" placeholder="Title...">
		<input type="submit" class='button action' value="Add">
	</div>


</div>
<ul class="ml-home-menu">		
<?php
	foreach(ml_home_menu_items() as $item) {
		ml_home_menu_print_item($item);
	}
?>
</ul>

<div class='ml-iphone5'>
</div>
