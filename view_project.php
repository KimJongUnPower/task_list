<?php 
	include_once 'core/connect_db.php';
	include_once 'core/login.php';
	include_once 'core/csrf_protection.php';

	$project = R::findOne('projects', 'id = ?', array($_GET['id']));
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

	$error_del = '';
	$error_add = '';
	$error_set = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_csrf()){
		//Добовление пользователя к проекту
		if (isset($_POST['add_user'])){
			$user_for_project = R::findOne('users', 'id = ?', array($_POST['user']));
			if (isset($user['id'])){
				//Проверяем что пользователь не в проекте
				$users_check = $project->sharedUsers;
				foreach ($users_check as $user_check) {
					if ($user_check['id'] == $user_for_project['id']){
						$check = 2;
						break;
					}
				}

				if ($check != 2){
					$user_for_project->sharedUsers[] = $project;
					R::store($user_for_project);
				} else
					$error_add .= 'Пользователь уже в проекте';
			} else 
				$error_add .= 'Укажи корректного пользователя';
		}

		//Удаление пользователя из проекта
		if (isset($_POST['delete_user'])){
			$user_for_project = R::findOne('users', 'id = ?', array($_POST['user']));
			if (isset($user_for_project['id'])){
				
			} else 
				$error_del .= 'Укажи корректного пользователя';
		}

		if (isset($_POST['settings_project'])){

		}
	}	

	gen_csrf();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Проект: <?= $project['name']?></title>
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
	<?php 
			include 'includes/header.html';
	?>
	<content>
		<div class="width-1024px-non-background">
			<p class="title-p-black"><?=$project['name']?></p>
			<div class="min-height-200px">
				<form action="" method="POST" class="two-form-in-line">
					<p>Добовление пользователя в проект</p>
					<?php 
						include 'includes/view_users_in_select.php';
					?>
					<p class="error-p-red"><?=$error_add?></p>
					<?= csrf_html()?>
					<input type="hidden" name="add_user" value="1">
					<p class="text-align-center"><input type="submit" value="Добавить"></p>
				</form>
				<form action="" method="POST" class="two-form-in-line">
					<p>Удаление пользователя из проека</p>
					<?php 
						include 'includes/view_users_in_select.php';
					?>
					<p class="error-p-red"><?=$error_del?></p>
					<?= csrf_html()?>
					<input type="hidden" name="delete_user" value="1">
					<p class="text-align-center"><input type="submit" value="Удалить"></p>
				</form>
			</div>
			<div class="">
				<form class="width-100">
					<div class="two-columns">
						
					</div>
					<div class="two-columns">
						
					</div>
					<p class="error-p-red"><?=$error_set?></p>
					<?= csrf_html(); ?>
					<input type="hidden" name="settings_project">
					<p class="text-align-center"><input type="submit" value="Изменить"></p>
				</form>
			</div>
			<a class="non-style-white a-red" href="http://<?=$_SERVER['HTTP_HOST']?>/create_task.php?project=<?=$project['id']?>"><p class="text-align-centerr">Создать задание</p></a>
		</div>
		<div class="full-black">
			<div class="width-1024px-non-background">
				<p class="title-p-white">Задания</p>
				<div class="width-1024px-non-background">
					<?php
						$tasks = $project->sharedTasks;
						
						foreach ($tasks as $task) {
							echo '<a class="non-style-white" href="http://'.$_SERVER['HTTP_HOST'].'/view_task.php?id='.$task['id'].'">
							<div class="min-128px-item-white">
								<p class="item-title-white">'.$task['name'].'</p>
								<div class="project-info">
									<p class="item-additionally-info-white">'.$task['description'].'</p>
									<p class="additionally-info-white">Выполнить до: '.$task['date'].'</p>
									<p class="additionally-info-white">Выполнено: ';

							if ($task['done']){
								echo 'Да';
							} else {
								echo 'Нет';
							}

							echo '</p>
							<p class="additionally-info-white">Пользователи: ';

							$performing_users = $task->ownUsers;
							foreach ($performing_users as $performing_user) {
								echo $performing_user['login'].' ';
							}

							echo '</p>
								</div>
							</div></a>';
						}
					?>
				</div>
			</div>
		</div>
	</content>
	//Добовление других пользователей в проект
	//Показ заданий (Кто выполнил или кем должно быть выполнено )
</body>
</html>