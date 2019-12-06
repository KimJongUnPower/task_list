<?php 
	include '../core/login.php';
	include '../core/check_user_in_project.php';

	if (!isset($_GET['id'])){
		exit('Ошибка');
	}	

	$project = R::findOne('projects', 'id = ?', array($_GET['id']));
	if (!isset($project['id'])){
		exit('Ошибка');
	}

	if (!check_user_in_project($project, $user)){
		exit('Ошибка');
	}

	//Находим все задания в проекте и удаляем их и пользователей в заднии
	foreach ($project->ownTasks as $task) {
		foreach ($task->sharedUsers as $user_in_task) {
			R::exec('DELETE FROM tasks_users WHERE users_id = '.$user_in_task['id'].' AND tasks_id = '.$task['id'].';');		
		}	
		R::trash($task);
	}

	//Удаляем пользователей из проекта
	foreach ($project->sharedUsers as $user_in_project) {		
		R::exec('DELETE FROM projects_users WHERE users_id = '.$user_in_project['id'].' AND projects_id = '.$project['id'].';');
	}

	R::trash(R::findOne('projects', 'id = ?', array($_GET['id'])));
	header('Location: http://'.$_SERVER['HTTP_HOST']);
	exit();
?>