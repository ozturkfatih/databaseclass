# Databaseclass - PDO wrapper with prepared statements.

> This project provides a full extension for PHP's PDO (PHP Data Objects) class designed 
> for ease-of-use and saving development time/effort. This is achived by providing 
> methods - delete, insert, select, select a row, update and 
> other PDO property - for quickly building common SQL statements, 
> handling exceptions when SQL errors are produced, 
> and automatically returning results/number 
> of affected rows for the appropriate SQL statement types.
- Connection Settings
- Installation
- Methods
    - Select
        - Fetch Modes 
    - Single Row
        - Fetch Modes 
    - Fetch Modes
    - Insert
    - Udate
    - Delete
    - Query
    - Stored Procedure

### To use the class

#### 1. Connection Settings
First of all, Edit the database settings in the Config/Database.php

```php
<?php
return [
    //Database Connection parameters
    'connections' => [
        'mysql' => array(
            'read' => array(
                'host' => '127.0.0.1',  //localhost
            ),
            'write' => array(
                'host' => '127.0.0.1'   //localhost
            ),
            'port'      => '3306',
            'driver'    => 'mysql',
            'database'  => 'test',              //database name
            'username'  => 'root',              //database username
            'password'  => '',                  //database user password
            'charset'   => 'utf8',              //default charset
            'collation' => 'utf8_general_ci',   //charset collection
            'persistent'=> false,
            'prefix'    => ''
        ),
    ],
];
```
