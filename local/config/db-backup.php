<?php


return [

	'path' => storage_path() . '/database_backup/',

	'mysql' => [
		// Window
		// 'dump_command_path' => 'C:/xampp/mysql/bin/',
		// 'restore_command_path' => 'C:/xampp/mysql/bin/',
		
		// Mac
		'dump_command_path' => '/Applications/XAMPP/xamppfiles/bin/',
		'restore_command_path' => '/Applications/XAMPP/xamppfiles/bin/',	
	],

	's3' => [
		'path' => ''
	],

    'compress' => false,
];

