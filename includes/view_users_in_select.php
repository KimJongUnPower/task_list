<select name="user" size="5" required="true">
	<?php 
		$users = R::findAll('users');
		foreach ($users as $performing_user) {
			echo '<option value="'.$performing_user['id'].'">'.$performing_user['login'].'</option>';
		}
	?>
</select>