<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',

        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'admin',
            'pass' => 'tFn3wPZYFn72yPt',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'vtexvsi_transcribe',
            'user' => 'admin',
            'pass' => 'EJT2Dz59XFU6DFJX',
            'port' => '3306',
            'charset' => 'utf8',
        ],
		'test' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'vtexvsi_transcribe',
            'user' => 'root',
            'pass' => 'root',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
