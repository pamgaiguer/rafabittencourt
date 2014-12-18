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
		if(!count($albums)){
			echo '<tr><td colspan="4"><em>No Albums Found</em></td></tr>';
		}
		foreach($albums as $album){
			if(!trim($album->title)){
				$album->title='<em>Empty</em>';
			}
			?>
		<tr>
			<td><?php echo $album->id;?></td>
			<td><a href="admin.php?page=SrzFb-Albums&srzf=edit&id=<?php echo $album->id?>"><?php echo $album->title;?></a></td>
			<td><input type="text" value="[srizonfbalbum id=<?php echo $album->id?>]" disabled="disabled" /></td>
			<td width="100"><a href="admin.php?page=SrzFb-Albums&srzf=delete&id=<?php echo $album->id?>"  onclick="return confirm('Are you sure you want to delete?');">Delete</a> - <a href="admin.php?page=SrzFb-Albums&srzf=sync&id=<?php echo $album->id?>">ReSync</a></td>
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
