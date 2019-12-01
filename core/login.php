<?php 
	require_once 'csrf_protection.php';
	require_once 'connect_db.php';
	session_start();

	if (!isset($_SESSION['login'])){
		if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_csrf()){
			$user = R::findOne('users', 'login = ?', array($_POST['login']));

			if (password_verify($_POST['password'], $user['password'])){
				$_SESSION['login'] = $user['id'];
			} else {
				exit(include 'includes/login.php');
			}
		} else{
			exit(include 'includes/login.php');
		}
	} else {
		$user = R::findOne('users', 'id = ?', array($_SESSION['login']));
		if (!isset($user['id'])){
			$_SESSION = array();
			exit('Ошибка');
		}
	}