<?php


return [

	'path' => storage_path() . '/database_backup/',

	'mysql' => [
		// Window
		'dump_command_path' => env('WINDOWS_MYSQLDUMP_PATH'),
		'restore_command_path' => env('WINDOWS_MYSQLDUMP_PATH'),
		
		// Mac
		// 'dump_command_path' => env('MAC_MYSQLDUMP_PATH'),
		// 'restore_command_path' => env('MAC_MYSQLDUMP_PATH'),	
	],

	's3' => [
		'path' => ''
	],

    'compress' => false,
];

