<table class="wp-list-table widefat fixed">
	<thead>
		<tr>
			<th style="width:5em;">ID</th>
			<th>Title</th>
			<th>Shortcode</th>
			<th>Action</th>
		</tr>
	</thead>

	<tbody>
		<?php 
		if(!count($galleries)){
			echo '<tr><td colspan="4"><em>No Galleries Found</em></td></tr>';
		}
		foreach($galleries as $gallery){
			if(!trim($gallery->title)){
				$gallery->title='<em>Empty</em>';
			}
			?>
		<tr>
			<td><?php echo $gallery->id;?></td>
			<td><a href="admin.php?page=SrzFb-Galleries&srzf=edit&id=<?php echo $gallery->id?>"><?php echo $gallery->title;?></a></td>
			<td><input type="text" value="[srizonfbgallery id=<?php echo $gallery->id?>]" disabled="disabled" /></td>
			<td width="100"><a href="admin.php?page=SrzFb-Galleries&srzf=delete&id=<?php echo $gallery->id?>"  onclick="return confirm('Are you sure you want to delete?');">Delete</a> - <a href="admin.php?page=SrzFb-Galleries&srzf=sync&id=<?php echo $gallery->id?>">ReSync</a></td>
		</tr>
		<?php }?>
	</tbody>
	
	<thead>
		<tr>
			<th style="width:5em;">ID</th>
			<th>Title</th>
			<th>Shortcode</th>
			<th>Action</th>
		</tr>
	</thead>
</table>
