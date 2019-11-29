<?php 
	include_once 'core/csrf_protect.php';
	gen_csrf();

	$errors = '';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Авторизация</title>
		<link rel="stylesheet" type="text/css" href="style/style.css">
	</head>
	<body>
		<?php 
			include 'includes/header.html';
		?>
		<content>
			<div class="width-1024px-non-background">
				<p class="title-p-black">Авторизация</p>
				<form action="" method="POST">
					<?= csrf_html(); ?>
					<p>
						Логин: <input type="text" name="login" required="true">
					</p>
					<p>Пароль: <input type="password" name="password" required="true"></p>
					<p class="error-p-red"><?=$errors?></p>
					<p><input type="submit" name="Войти"></p>
				</form>
			</div>
		</content>
	</body>
</html>