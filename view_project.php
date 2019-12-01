<?php 
	include_once 'core/connect_db.php';
	include_once 'core/login.php';
	include_once 'core/csrf_protection.php';
	include_once 'core/check_user_in_project.php';

	if (!isset($_GET['id']))
		exit('Ошибка');

	$project = R::findOne('projects', 'id = ?', array($_GET['id']));
	if (!isset($project['id']))
		exit('Ошибка');

	//Проверка в проекте ли пользователь

	if (!check_user_in_project($project, $user)){
		exit('Ошибка');
	}

	$error_del = '';
	$error_add = '';
	$error_set = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_csrf()){
		//Добовление пользователя к проекту
		if (isset($_POST['add_user'])){
			$user_on_project = R::findOne('users', 'id = ?', array($_POST['user']));
			if (isset($user_on_project['id'])){
				//Проверяем что пользователь не в проекте
				if (!check_user_in_project($project, $user_on_project)){
					$user_on_project->sharedProjects[] = $project;
					R::store($user_on_project); 
				} else
					$error_add .= 'Пользователь уже в проекте';
			} else 
				$error_add .= 'Укажи корректного пользователя';
		}

		//Удаление пользователя из проекта
		if (isset($_POST['delete_user'])){
			$user_del = R::findOne('users', 'id = ?', array($_POST['user']));
			if (isset($user_del['id'])){
				if (check_user_in_project($project, $user_del)){
					//Не нашёл более действиного метода чем sql запрос 
					R::exec('DELETE FROM projects_users WHERE users_id = '.$user_del['id'].' AND projects_id = '.$project['id'].';');
				} else {
					$error_del .= 'Пользователь не в проекте';
				}
			} else 
				$error_del .= 'Укажи корректного пользователя';
		}

		if (isset($_POST['settings_project'])){
			if (isset($_POST['name']) and strip_tags(trim($_POST['name'])) != '' and strip_tags($_POST['name']) != $project['name']){
				$project->name = strip_tags($_POST['name']);
				$error_set .= 'Название изменено';
			}

			if (isset($_POST['description']) and strip_tags(trim($_POST['description'])) != '' and strip_tags($_POST['description']) != $project['description']){
				$project->description = strip_tags($_POST['description']);
				$error_set .= ' описание изменено';
			}

			R::store($project);
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
					<select name="user" required="true" size="5">
						<?php 
							//Выводим пользователей которые только в прокте
							$users_in_project = $project->sharedUsers;
							foreach ($users_in_project as $user_in_project) {
								echo '<option value="'.$user_in_project['id'].'">'.$user_in_project['login'].'</option>';
							}
						?>
					</select>
					<p class="error-p-red"><?=$error_del?></p>
					<?= csrf_html()?>
					<input type="hidden" name="delete_user" value="1">
					<p class="text-align-center"><input type="submit" value="Удалить"></p>
				</form>
			</div>
			<div class="">
				<form action="" method="POST">
					<p>Название <input type="text" name="name" value="<?=$project['name']?>"></p>
					<p>Описание</p>
					<textarea name="description"><?=$project['description']?></textarea>
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
	//Показ заданий (Кто выполнил или кем должно быть выполнено )
</body>
</html>