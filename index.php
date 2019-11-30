<?php 
	include 'core/connect_db.php';
	include 'core/login.php';
	include_once 'core/csrf_protect.php';

	$errors = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_csrf()){
		switch ('') {
			case strip_tags(trim($_POST['project'])):
				$errors .= 'Укажите название проекта';
				break;
			
			case strip_tags(trim($_POST['description'])):
				$errors .= 'Укажите описание';
				break;

			default:
				$new_project = R::dispense('projects');
				$new_project->name = strip_tags($_POST['project']);
				$new_project->description = strip_tags($_POST['description']);
				R::store($new_project);

				//Добовляем проект пользователю
				$user->sharedUsers[] = $new_project;
				R::store($user);

				//Переадрисация
				
				header('Location: http://'.$_SERVER['HTTP_HOST'].'/view_project.php?id=?'.$project['id']);
				exit();
		}
	}

	gen_csrf();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Выбор проекта</title>
		<link rel="stylesheet" type="text/css" href="style/style.css">
	</head>
	<body>
		<?php 
			include 'includes/header.html';
		?>
		<content>
			<div class="width-1024px-non-background">
				<p class="title-p-black">Создание проекта</p>
				<form action="" method="POST">
					<?= csrf_html(); ?>
					<p>
						Название проекта: <input type="text" name="project" required="true">
					</p>
					<p>Описание проекта:</p>
					<textarea name="description" required="true"></textarea>
					<p class="error-p-red"><?=$errors?></p>
					<p><input type="submit" name="Создать"></p>
				</form>
			</div>
			<div class="full-black">
				<p class="title-p-white">Проекты</p>
				<div class="width-1024px-non-background">
					<?php
						//Добовляем связь с проектами доступными пользователю

						$projects = $user->sharedProjects;
						foreach ($projects as $project){
							echo '<a class="non-style-white" href="http://'.$_SERVER['HTTP_HOST'].'/view_project.php?id='.$project['id'].'">
							<div class="min-128px-item-white">
								<p class="item-title-white">'.$project['name'].'</p>
								<div class="project-info">
									<p class="item-additionally-info-white">'.$project['description'].'</p>';

							$tasks = $project->sharedTasks;
							//Вывести сколько заданий всего
							echo '<p class="additionally-info-white">Всего заданий:'.$count($tasks).'</p></div>
							</div></a>';
						}
					?>
				</div>
			</div>
		</content>
	</body>
</html>