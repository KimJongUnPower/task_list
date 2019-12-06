<?php 
	include '../core/login.php';
	include '../core/check_user_in_project.php';

	if (!isset($_GET['id'])){
		exit('Ошибка');
	}	

	$task = R::findOne('tasks', 'id = ?', array($_GET['id']));
	if (!isset($task['id'])){
		exit('Ошибка');
	}

	if (!check_user_in_task($task, $user)){
		exit('Ошибка');
	}

	foreach ($task->sharedUsers as $user_in_task) {		
		R::exec('DELETE FROM tasks_users WHERE users_id = '.$user_in_task['id'].' AND tasks_id = '.$task['id'].';');
	}

	$project = $task->projects;
	R::trash(R::findOne('tasks', 'id = ?', array($_GET['id'])));
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/view_project.php?id='.$project['id']);
	exit();
?>