<?php
	require 'libs/rb-sqlite.php';

	R::setup('sqlite:/mnt/info/php/sqlite_db/task_list.db');

	if (!R::testConnection()){
        R::close();
        exit('Ошибка');
    }