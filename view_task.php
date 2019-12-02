<?php 
	include_once 'core/connect_db.php';
	include_once 'core/login.php';
	include_once 'core/csrf_protection.php';
	include_once 'core/check_user_in_project.php';

	//Проверяем существует ли задание 
	if (!isset($_GET['id']))
		exit('Ошибка');

	$task = R::findOne('tasks', 'id = ?', array($_GET['id']));
	if (!isset($task['id'])){
		exit('Ошибка');
	}

	//Получаем проект
	$project = $task->projects;
	if (!isset($project['id']))
		exit('Ошибка');

	//Проверяем в проекте ли пользователь
	if (!check_user_in_project($project, $user) and !check_user_in_task($task, $user)){
		exit('Ошибка');
	}

	$error_set = '';
	$error_end = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_csrf()){
		if (isset($_POST['settings'])){
			if (strip_tags(trim($_POST['name'])) != '' and strip_tags($_POST['name']) != $task['name']){
				$task->name = strip_tags($_POST['name']);
				$error_set .= 'Название проекта изменено.';
			}

			if (strip_tags(trim($_PSOT['date'])) != '' and strip_tags($_POST['date']) != $task['date']){
				$task->date = strip_tags($_POST['date']);
				$error_set .= ' Дата выполнения изменена';
			}

			if (strip_tags(trim($_POST['description'])) != '' and strip_tags($_POST['description']) != $task['description']){
				$task->description = strip_tags($_POST['description']);
				$error_set .= ' Описание изменено.';
			}

			R::store($task);
		}

		if (isset($_POST['end_task'])){
			if (strip_tags(trim($_POST['complete'])) == 'on' and !$task['done']){
				switch ('') {
					case strip_tags(trim($_POST['description'])):
						$error_end .= 'Заполните описание';
						break;

					default:
						$task->done = True;
						$task->end_description = strip_tags($_POST['description']);
						R::store($task);
						break;
				}
			}

			if (strip_tags(trim($_POST['description'])) != '' and strip_tags($_POST['description']) != $task['end_description'] and $task['done']){
				$task->end_description = strip_tags($_POST['description']);
				R::store($task);
				$error_end .= 'Описание изменено';
			}
		}
	}	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Задание: <?=$task['name']?></title>
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
	<?php
		include 'includes/header.html';
	?>
	<content>
		<div class="width-1024px-non-background">
			<p class="title-p-black">Задание: <?=$task['name']?></p>
			<form action="" method="POST">
				<p class="text-align-center font-size-25px">Настройки задания</p>
				<p>Название проекта: <input type="text" name="name" required="true" value="<?=$task['name']?>"></p>
				<p>Дата выполнения: <input type="date" name="date" required="true" value="<?=$task['date']?>">
				<p>Описание проекта:</p>
				<textarea name="description" required="true"><?=$task['description']?></textarea>
				<?=csrf_html()?>
				<p class="error-p-red"><?=$error_set?></p>
				<input type="hidden" name="settings" value="1">
				<p class="text-align-center"><input type="submit" value="Изменить"></p>
			</form>
		</div>
		<div class="width-1024px-non-background">
			<p class="title-p-black">Заполнения сопутствующей информации о задании</p>
			<form action="" method="POST">
				<?php
					csrf_html(); 

					if (!$task['done']){
						echo '<p>Выполнено: <input type="checkbox" name="complete"></p>';
					} else {
						echo '<p class="alert-red">Задание выполнено пользвоателем: </p>';
					}
				?>
				<input type="hidden" name="end_task" value="1">
				<p>Описание выполниной работы</p>
				<textarea name="description"><?=$task['end_description']?></textarea>
				<p class="error-p-red"><?=$error_end?>
				<p class="text-align-center"><input type="submit" value="Готово!"></p>
			</form>
		</div>
	</content>
</body>
</html>