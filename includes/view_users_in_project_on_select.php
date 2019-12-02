<select name="user" required="true" size="5">
	<?php
		$users_in_project = $project->sharedUsers;
		foreach ($users_in_project as $user_in_project) {
			echo '<option value="'.$user_in_project['id'].'">'.$user_in_project['login'].'</option>';
		}
	?>
</select>