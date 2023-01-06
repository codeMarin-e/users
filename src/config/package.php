<?php
//    $dbDir = [ dirname(__DIR__), 'Database', 'migrations' ];
//    $dbDir = implode( DIRECTORY_SEPARATOR, $dbDir );
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\Users\Database\Seeders\MarinarUsersInstallSeeder"',
		],
		'remove' => [
            'php artisan db:seed --class="\Marinar\Users\Database\Seeders\MarinarUsersRemoveSeeder"',
        ]
	];
