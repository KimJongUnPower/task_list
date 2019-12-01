<?php
	/* Функция для проверки в проекте ли пользователь */

	require_once 'connect_db.php';

	function check_user_in_project($project_for_user, $verified_user){
		$users_check = $project_for_user->sharedUsers;

		foreach ($users_check as $user_check) {
			if ($user_check['id'] == $verified_user['id']){
				return True;
			}
		}
		return False;
	}