<?php
	require 'libs/rb-sqlite.php';

	R::setup('sqlite:/mnt/info/php/task_list/core/sqlite.db');

	if (!R::testConnection()){
        R::close();
        exit('Ошибка');
    }