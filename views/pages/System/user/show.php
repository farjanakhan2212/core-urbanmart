<div class="p-4">
<a class="btn btn-success" href="users">Manage User</a>
<?php echo table_wrap_open();?>
<table class='table'>
	<tr><th colspan='2'>User Details</th></tr>
<?php
	$html="";
		$html.="<tr><th>Id</th><td>$user->id</td></tr>";
		$html.="<tr><th>Name</th><td>$user->name</td></tr>";
		$html.="<tr><th>Email</th><td>$user->email</td></tr>";
		$html.="<tr><th>Branch</th><td>$user->branch</td></tr>";
		$html.="<tr><th>Created At</th><td>$user->created_at</td></tr>";
		$html.="<tr><th>Updated At</th><td>$user->updated_at</td></tr>";

	echo $html;
?>
</table>
<?php echo table_wrap_close();?>
</div>