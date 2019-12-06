<?php 
	include '../core/login.php';
	include '../core/check_user_in_project.php';

	$project = R::findOne('projects', 'id = ?', array($_GET['project']));
	if (!isset($project['id']))
		exit('Ошибка');

	//Проверка в проекте ли пользователь
	$users_check = $project->sharedUsers;
	foreach ($users_check as $user_check) {
		if ($user_check['id'] == $user['id']){
			$check = 1;
			break;
		}
	}
	if (!isset($check)){
		exit('Ошибка');
	}

	$error = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_csrf()){
		switch ('') {
			case strip_tags(trim($_POST['name'])):
				$error .= 'Укажите название';
				break;

			case strip_tags(trim($_POST['description'])):
				$error .= 'Заполните описание';
				break;

			case strip_tags(trim($_POST['date'])):
				$error .= 'Укажите дату выполнения';
				break;

			case strip_tags(trim($_POST['user'])):
				$error .= 'Укажите пользователя для выполнения';
				break;
			
			default:
				$performing_user = R::findOne('users', 'id = ?', array($_POST['user']));
				if (isset($performing_user['id'])){
					$task = R::dispense('tasks');
					$task->name = strip_tags($_POST['name']);
					$task->description = strip_tags($_POST['description']);
					$task->date = strip_tags($_POST['date']);
					$task->done = False;
					$task->sharedUsers[] = $performing_user;
					R::store($task);

					$project->ownTasks[] = $task;
					R::store($project);

					header('Location: http://'.$_SERVER['HTTP_HOST'].'/view_project.php?id='.$project['id']);
					exit();
				} else {
					$error .= 'Укажите корректного пользователя';
				}
				break;
		}
	}	

	gen_csrf();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Создание задания</title>
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
	<?php
		include '../includes/header.html';
	?>
	<content>
		<div class="width-1024px-non-background">
			<p class="title-p-black">Создание задания для проекта: <?=$project['name']?>
			<form action="" method="POST">
				<p>Название задания <input type="text" name="name" required="true"></p>
				<p>Описание задания</p>
				<textarea name="description" required="true"></textarea>
				<p>Дата выполнения: <input type="date" name="date" required="true"></p>
				<p>Пользователь для выполнения:</p>
				<?php 
					include_once '../includes/view_users_in_project_on_select.php';
				?>
				<p class="error-p-red"><?=$error?></p>
				<?=csrf_html()?>
				<p class="text-align-center"><input type="submit" value="Создать"></p>
			</form>
		</div>
	</content>
</body>
</html>