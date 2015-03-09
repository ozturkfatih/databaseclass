<?php
return [
    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    */

    'default' => 'mysql',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    | Here are each of the database connections setup for your application.
    */


    'connections' => [
        'mysql' => array(
            'read' => array(
                'host' => '127.0.0.1',
            ),
            'write' => array(
                'host' => '127.0.0.1'
            ),
            'port'      => '3306',
            'driver'    => 'mysql',
            'database'  => 'learnpdo',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'persistent'=> false,
            'prefix'    => ''
        ),
    ],

    'exception'=>[
      'environment'=>array(
          'development' =>false,
          'release'     =>true
      ),
      'message'=>array(
          'notConnection'=>'Database Error: Unable to connect to the database:Could not connect to MySQL',
          'connection'=>'Database connection is success.'
      )
    ],

];
