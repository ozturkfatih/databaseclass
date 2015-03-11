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
    - Insert
    - Udate
    - Delete
    - Query
    - Stored Procedure

### To use the class

#### 1. Connection Settings
- First of all, Edit the database settings in the Config/Database.php
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
#### 2. Installation
- Require the class in your project. 
```php
    require_once('Config/Config.php');
    require_once('vendor/autoload.php');
```

- Create the instance Database Class

 You can look php namespace usage. :link: [PHP Manual-Using namespaces](http://php.net/manual/en/language.namespaces.basics.php)
```php
use Bin\System\Database as DB;
$DB=new DB();
```
#### 3. Methods
- Once you have configured your database connection, you may run methods using the DB facade.

- #### Select Method
```php
    $table = 'users';
    $results=DB::select($table);
```
> The select method will always return an array of results.

- If you want get some field in table.
```php
    $table = 'users';
    $fields = array('Name','Lastname');
    $result=DB::select($table,$fields);
```
- Default fetch mode is PDO::FETCH_ASSOC. You can change fetch mode. 
- :link: [PDOStatement::fetch](http://php.net/manual/en/pdostatement.fetch.php)
```php
    DB::setFetchMode(PDO::FETCH_OBJ);
    //"PDO::FETCH_OBJ: returns an anonymous object with property names that correspond to the column names returned in your result set"
    $fields = array('Name','Lastname');
    $result=DB::select($table,$fields);
```

- :warning: setFetchMode method must be used before select, row and query methods.

- #### Row Method
```php
    //row method parameters are $table, $values, $params, $options
    //$values and $options can be null, 
    $table = 'users';
    $params = array('Id' => 3);
    $result = DB::row($table, null, $params);
```
- If you want get some field in table.
```php
    $table = 'users';
    $values = array('Id','Name','Lastname');
    $params = array('Id' => 3);
    $result = DB::row($table, $values, $params);
```
- Default fetch mode is PDO::FETCH_ASSOC. You can change fetch mode. 
- :link: [PDOStatement::fetch](http://php.net/manual/en/pdostatement.fetch.php)
```php
    DB::setFetchMode(PDO::FETCH_OBJ);
    $table = 'users';
    $params = array('Id' => 3);
    $result = DB::row($table, null, $params);
```
- :warning: setFetchMode method must be used before select, row and query methods.

- #### Insert Method
```php
    $params = ['Name'      => 'Fatih',
               'Lastname'  => 'ÖZTÜRK',
               'Age'       =>  29,
               'Birthdate' => '1985-03-15'];
    $result = DB::insert('users', $params);
    if($result){
        echo "Success message";
    }
```
- :warning: The array keys must be same table coloumn name.

- #### Update Method
```php
    $params = ['Name'      => 'Fatih',
               'Lastname'  => 'ÖZTÜRK',
               'Age'       =>  29,
               'Birthdate' => '1985-03-15',
               'Where'     => ['Id' => 1]];
    $result = DB::update('users', $params);
    if ($result) {
        echo "Success message";
    }
```

- :warning: You must set where clause like 'Where'=>['Id' => 1]

- #### Delete Method
```php
    $params = ['Id'=>1];
    $result = DB::delete('users', $params);
```

- #### Query Method

- If you can use a subquery or join query, this method is simply fetch your query.
```php
    $sql="SELECT * FROM users";
    $result=DB::query($sql);
```
- :link: [PDO::query](http://php.net/manual/en/pdo.query.php)

- #### Stored Procedure 

> Comming soon


