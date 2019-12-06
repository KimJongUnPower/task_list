<?php
	/* Функция для проверки в проекте ли пользователь */

	require_once 'connect_db.php';

	function check_user_in_project($project_for_user, $verified_user){

		foreach ($project_for_user->sharedUsers as $user_check) {
			if ($user_check['id'] == $verified_user['id']){
				return True;
			}
		}
		return False;
	}

	function check_user_in_task($task_for_user, $verified_user){

		foreach ($task_for_user->sharedUsers as $user_check) {
			if ($user_check['id'] == $verified_user['id']){
				return True;
			}
		}
		return False;
	}