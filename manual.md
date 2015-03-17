# Table of contents

<!-- MarkdownTOC -->

- [Things to know before using ORM](#things-to-know-before-using-orm)
    - [What is MVC ? What is model view controller pattern?](#what-is-mvc--what-is-model-view-controller-pattern)
    - [How can I develop my project model with classes?](#how-can-i-develop-my-project-model-with-classes)
    - [What is PHPDoc?](#what-is-phpdoc)
    - [What is ORM ? What is Object Relational Mapper?](#what-is-orm--what-is-object-relational-mapper)
    - [Why do i have low salary as a php programmer?](#why-do-i-have-low-salary-as-a-php-programmer)
- [Installation](#installation)
    - [Include](#include)
    - [apc_cache for php <= 5.4.x](#apc_cache-for-php--54x)
    - [apc_cache for php >= 5.5.x](#apc_cache-for-php--55x)
    - [PHP Notices](#php-notices)
- [Connect](#connect)
    - [Basic architecture](#basic-architecture)
    - [Connect simply](#connect-simply)
    - [Connect using custom link](#connect-using-custom-link)
    - [Connect using many links](#connect-using-many-links)
- [Prepare](#prepare)
    - [Map class to table](#map-class-to-table)
    - [Map namespace classes to tables](#map-namespace-classes-to-tables)
    - [Map classes by pattern to tables](#map-classes-by-pattern-to-tables)
    - [Get registered table handler of class](#get-registered-table-handler-of-class)
    - [Create databases for mapped classes](#create-databases-for-mapped-classes)
    - [Create tables for mapped classes](#create-tables-for-mapped-classes)
    - [Update table structures or Update/create new fields](#update-table-structures-or-updatecreate-new-fields)
    - [Generate sql dump for database changes and save to file](#generate-sql-dump-for-database-changes-and-save-to-file)
- [Load](#load)
    - [Load all from table and iterate throught the result](#load-all-from-table-and-iterate-throught-the-result)
    - [Load by id from table](#load-by-id-from-table)
    - [Load by field equals value or field like value](#load-by-field-equals-value-or-field-like-value)
    - [Load using pager from table](#load-using-pager-from-table)
- [Query](#query)
    - [Load using custom query](#load-using-custom-query)
    - [Query helper functions](#query-helper-functions)
    - [Query where](#query-where)
    - [Query order](#query-order)
    - [Query limit](#query-limit)
    - [Load using custom query with pager](#load-using-custom-query-with-pager)
    - [Load using query and return single object instead of object array](#load-using-query-and-return-single-object-instead-of-object-array)
    - [Load affecting default load behavior or changing table default query](#load-affecting-default-load-behavior-or-changing-table-default-query)
- [Save](#save)
    - [save single object to table](#save-single-object-to-table)
    - [save with boolean result](#save-with-boolean-result)
    - [save with saved object result](#save-with-saved-object-result)
    - [save without knowing object table](#save-without-knowing-object-table)
    - [save objects to table](#save-objects-to-table)
- [Delete](#delete)
    - [delete single object](#delete-single-object)
    - [delete by id](#delete-by-id)
    - [delete table objects](#delete-table-objects)
    - [delete various type of object same time](#delete-various-type-of-object-same-time)
    - [delete by query from table](#delete-by-query-from-table)
- [Debug](#debug)
- [Cache](#cache)
    - [cache user - cache table records on user level (default session)](#cache-user---cache-table-records-on-user-level-default-session)
    - [cache long - table records on server level (default apc_cache)](#cache-long---table-records-on-server-level-default-apc_cache)
    - [cache temp - table records for script runtime (default memory)](#cache-temp---table-records-for-script-runtime-default-memory)
    - [develop and plug custom cache engine for desired cache level](#develop-and-plug-custom-cache-engine-for-desired-cache-level)
- [Table](#table)
- [table modifiers](#table-modifiers)
    - [use custom name table for class](#use-custom-name-table-for-class)
    - [use custom name field of table for class property](#use-custom-name-field-of-table-for-class-property)
- [Field](#field)
    - [field locaization](#field-locaization)
- [Tools](#tools)
    - [universal time handling](#universal-time-handling)

<!-- /MarkdownTOC -->


# Things to know before using ORM
## What is MVC ? What is model view controller pattern?

**MVC** stands for **Model View Controller**. If you heard about it before but still dont know what it is you might be afraid of it.

But MVC is nothing than bunch of terms describing best practices how code in big projects should be organized for not getting lost in them.

If you have developed at least some kind of web application you already have used **MVC** concepts withought knowing it. I could tell you that **Controller** is a portion of code which displays **Views** to user and also receives **input** from user, **processes** it using **Model** and than decides in what **View** to parse **output** data but nobody understands sentences like that.

Therefore I ll show you an example. (*Note that while using db.php our goal is only knowing how to architect Model*)

View is very simple thing. Dont get anything what you read here literally I m trieng to come out with the simpliest examples.

Imagine a task. We must build a product page where user will purchase product.

We have two html files: product.html and success.html

#### product.html
```html
<h1>{product_name}</h1>
<a href="{project_link}?page=purchase&product={product_id}">
    Buy product
</a>
```

#### purchase.html
```html
You have successfully purchased {product_name}
```

This are two stupid static html files and we can totally consider them as **views**. In {product_name} there goes actual product name. When user clicks "Buy product" we will have page and product variables incoming in our script. Congratulations you know what views are !

But how to use that views in actual task ? Here we need a controller. Let us assume we are so stupid we build our entire site php script in only index.php?

#### index.php
```php
if ($_REQUEST['page']=='product')
{
    $product = $database->product->load ($_REQUEST['product']);

    $output = array ();
    $output['project_link'] = $_SERVER['REMOTE_ADDR'];
    $output['product_name'] = $product->name;
    $output['product_id'] = $product->id;

    $view = file_get_contents ('./product.html');
    foreach ($output as $field = > $value)
    {
        $view = str_replace ('{'.$field.'}', $value);
    }
    echo $view;
    exit;
}
else if ($_REQUEST['page']=='purchase')
{
    $product = $database->product->load ($_REQUEST['product']);

    ### MAGIC HAPPENS HERE !
    $product->buy();

    $output = array ();
    $output['project_link'] = $_SERVER['REMOTE_ADDR'];
    $output['product_name'] = $product->name;
    $output['product_id'] = $product->id;

    $view = file_get_contents ('./purchase.html');
    foreach ($output as $field = > $value)
    {
        $view = str_replace ('{'.$field.'}', $value);
    }
    echo $view;
    exit;

}
```

So this is **Controller**. If you look closer it contains two sections. They are almost identical. One parses page "product" and another parses page "purchase". That sections are almost identical except in section "purchase" user buys a product while in section "product" user views product page. View variables like {product_name} and {product_id} are replaced using simple str_replace function. Views files are loaded simply by file_get_contents. And line $product->buy() actually does what it says. But where is that method code called **buy** ?

#### shop.php
```
namespace shop
{
    class proudct
    {
        public $id;
        public $name;
        public function buy ()
        {
            /*
            Here you should imagine the code where actual happens buy
            */
        }
    }
}
```

This file is **Model**. If that does not make sense for you I strongly recommend you to sell burgers.

## How can I develop my project model with classes?

I assume that you will handle your controllers and views by yourself but what you need for using db.php is to have model in classes. First you must understand what is core of your project than you must describe it in classes.

Somewhere in your model you can meet lines like this:

```php
namespace user;
class user
{
    public $id;
    public $login;
    public $password;
    public $email;
    public $first;
    public $last;
    public $birth;
    /**
    * @var \user\group
    */
    public $group;
    public function __construct ($login=null, $email=null, $first=null, $last=null)
    {
        $this->login = $loginl
        $this->email = $email;
        $this->first = $first;
        $this->last = $last;
    }
    public function name ()
    {
        if ($this->first && $this->last)
        {
            return $this->first.' '.$this->last.
        }
        else if ($this->first)
        {
            return $this->first;
        }
        else if ($this->last)
        {
            return $this->last;
        }
        else if ($this->email)
        {
            return $this->email
        }
        else
        {
            return $this->login;
        }
    }
    public function age ()
    {
        return intval((time()-strtotime($this->birth))/(3600*24*365));
    }
    public function password ($value=null)
    {
        $this->password = md5 ($value);
    }
}

class group
{
    public $id;
    public $name;
    public function __construct ($name=null)
    {
        $this->name = $name;
    }
}
```

So we did a user class in namespace user. Address of class is \user\user. This code is portion of model. User class has interesting method $user->name(). If you look closer it returns first name and last name if they are given. Or returns only first name if only first name given or returns email adress if it is given or at least returns user login. Model is nice place for such functionality.

Also we have another class 'group' in 'user' namespace. Adress of a class is \user\group. Notice that user->group has comment @var \user\group above pointing PHP that it represents instance of class \user\group.

Let me show you **usage of that classes** in your actual work probably at some point in your controller portion of code:

```php
$user = new \user\user ('administrator', 'John', 'Smith', 'john@company.com');
$user->birth = '1985-07-17';

echo $user->name()." is ".$user->age(); //Assume you are reading this in 2015
```

Outputs:

```php
John Smith is 30
```

What I try here is to give you an idea what and why can be placed in model. In everday code I use for example $user->name() and $user->age(). But functionality of it resides back in my model code and used for example in my other projects also.

I assume you now know what is **model** and how to make it with **classes** and **namespaces**.

## What is PHPDoc?
It is official documentation form supported by native PHP API. With PHPDoc you can document your classes, properties and methods and other things not related to classes. For example:

```php
/**
* @var \user\group
*/
public $group;
```

@var tells PHP that following property is type of class located in namespace user and called group (\user\group). Unlike regular comments in PHP, PHPDoc comments first line must begin with

```php
/**
```
Must be followed with next lines containing special keywords which describe your subject to be documented. Line must begin with *.

And PHPDoc comments section must be closed like regular comments section:

```php
*/
```

Spaces tabs and other things does not matter. Only matters PHPDoc comment opening.

db.php uses PHPDoc comments to fetch additional informations about properties and classes.

## What is ORM ? What is Object Relational Mapper?
In previous chapter we described classes in your model. Here we will copy only property declaration part of it:

```php
namespace user;
class user
{
    public $id;
    public $login;
    public $password;
    public $email;
    public $first;
    public $last;
    public $birth;
    /**
    * @var \user\group
    */
    public $group;
    ...
    ...
    ...
}

class group
{
    public $id;
    public $name;
    public function __construct ($name=null)
    {
        $this->name = $name;
    }
    ...
    ...
    ...
}
```

Now if you want to use objects of this classes in your everyday code some of this objects need to be saved in database for later usage. Some of them need to be loaded some of them need to be deleted.

And these tasks are handled by ORM. ORM maps your objects directly to table records and field values almost literally speaking.

To be clear ORM's do things like this (Assuming we have some ORM in variable $database):

```php
$group = new \user\group('Administrators');

$database->save ($group);

$user = new \user\user ('admin', 'John', 'Smith');
$user->group = $group;

$database->save ($user);

$john = $database->user->user->load ($user->id);
echo $john->name().' is in group '.$john->group->name;
# user->user because of class path \user\user

$database->user->group->delete ($group);
```

Instance of \user\user was just saved,loaded and deleted for example in mysql database table but you dont see any queries here. This is ORM.

## Why do i have low salary as a php programmer?
Because you dont use classes in your model or you dont have model in your projects at all.

Nothing great can be done without it.

# Installation

## Include
The only file you need is **db.php** iself. Just include db.php in your project and you are ready to go. Other files in this repo are just samples and documents.

```php
include './db.php';
```
## apc_cache for php <= 5.4.x

**db.php** requires **apc_cache** module's user variable caching functions by default. You can also override default caching engine but it is subject for further reading. A link for apc_cache installation instructions is here http://php.net/manual/en/apc.installation.php. On windows you just need to download proper php_apc.dll and enable it in php.ini by uncommenting line extension=php_apc.dll

In linux it is a bit difficult but usually you will and up with this commands:
```
wget http://pecl.php.net/get/APC-3.1.9.tgz
tar -xvf APC-3.1.9.tgz
cd APC-3.1.9
./configure -enable-apc -enable-apc-mmap -with-apxs2=/usr/sbin/apxs
make
make test
make install
```

## apc_cache for php >= 5.5.x

apc_cache has two kind of functionality one for storing precompiled script byte code in shared memory and second for caching user variables. As from PHP version 5.5.x php comes with built in module for script byte code caching named OPcache, there is no point for developing apc_cache for new versions of php. Instead they initialized new module named APCu and the only thing it does is stores user variables in cache with same old functions.

So if you are using PHP 5.5.x >= than you will need to install http://pecl.php.net/package/APCu for db.php to work.

**For Windows:** download suitable dll file. There are 4 major builds for php:
```
nts 32 bit (not thread safe)
nts 64 bit (not thread safe)
ts 32 bit (thread safe)
nts 64 bit (thread safe)
```

Therefore you should know which kind of php you have and than you must choose one from four from list like this:
```
5.5 Non Thread Safe (NTS) x86
5.5 Thread Safe (TS) x86
5.5 Non Thread Safe (NTS) x64
5.5 Thread Safe (TS) x64
```
Where x86 means 32 bit and x64 means 64 bit

Copy to dll file contained in archive file to php extensions directory, by default it is php/ext and add line extension=php_apc.dll to php.ini

**For Linux:** show the world you are true linux power user, compile APCu by yourself.


## PHP Notices
For some real reasons **db.php** generates notice level errors. If you have not restricted notices in error reporting section in your php.ini file you can set it manually on runtime like:
```php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
```
In the begining of your php script before db.php include.

For additional information read http://php.net/manual/en/function.error-reporting.php

# Connect

## Basic architecture
To work with orm you will need an instance of class \db\database. It stores connection links (represented by default with \db\link class) and table handlers for classes. Any class you are willing to map table in actual database must have its own handler. Class handler is represented by an instance of \db\table. By itself class properties have its own handlers represented by \db\field and instances of it are stored in table handler. db.php basic usage contains following important steps:

1. Initialize \db\database, establish connections to data source(s)/sql server(s).
2. Initialize desired class(es) handlers.
[3. Optionally autmatically/manually update/create database(s)/table(s)/field(s) on servers you are connected.
4. Using proper table handler save/load/delete/query objects of your classes in actual database tables.

So next thing we are going to learn is how to initialize \db\database and establish connection(s).

## Connect simply
Simple way to start is to specify server, database, username and password to database constructor.

```php
$database = new \db\database(string $hostname, string $database, string $username, string $password);
```

```php
$database = new \db\database('mysql:host=127.0.0.1', 'my_db', 'root', '1234');
```

**hostname**
Is first parameter confusing ? It is actually a data source name. db.php uses **PDO** as default link provider so first parameter is actually PDO data source name string.

**database**
Second parameter is **database name** and used by **db.php** to locate your tables on this connection.

**username**
This is what you think.

**password**
Same applies here.

This was the simpliest connection ever db.php can handle but remember you can have multiple connections to multiple servers and multiple databases same time.

## Connect using custom link
```
$database = new \db\database (string $database, \db\link $link);
```

But befure discussing it let me introduce \db\link to you.

By default db.php establishes connections using \db\link class which by itself is wrapper of php built in PDO class.
```php
$link = new \db\link (string $name, string $hostname, string $username, string $password, array $config);
```

**name**
It is unique name of the link and you will use it later. For example it might be named like 'my_mysql_link'.

**hostname**
It is actually a data source name. db.php uses **PDO** as default link provider so first parameter is PDO data source name string.

**username**
This is what you think.

**password**
Same applies here.

**config**
By default \db\link is PDO wrapper and here you can pass PDO configuration options. Default value of config is
```php
array (\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", \PDO::ATTR_PERSISTENT => true)
```
You can also first init $link = new \db\link(...) and than set custom configuration option like:
```php
$link->config[\PDO::MYSQL_ATTR_READ_DEFAULT_FILE] = '/etc/my.cnf'
```

Link goes as a second parameter of \db\database constructor. If you are passing link than first parameter is default database name for your tables.

```
$database = new \db\database (string $database, \db\link $link);
```

**database**
Default database name for your tables

Usage:
```php
$database = new \db\database ('my_db', new \db\link ('my_mysql_link', 'mysql:host=127.0.0.1', 'my_user', 'my_pass'));
```

After creating you can access your link by name:
```php
$database->link($name);
```

First link is considered as default link. Default link can be accessed without parameter:

```php
$database->link();
```

#### Overriding default link class
You can override link and develop your own. Just look at code of class \db\link and make same methods. It is done without interface. db.php even does not check where do your link objects come it just requires that that they had following methods:

##### query
Executes query and returns result array or resource iteratable as array if any. Returns null if resultless query or false if query has no results.
```php
[array] public function query (string $query)
```

##### select
Returns result array or resource iteratable as array or false if no records. Array must contain values with numerical keys begining with 0 in natural select field order. Like if we select field1,field2 from table, result record array must contain $row[0] = 'field 1 value', $row[1] = 'field 2 value'
```php
array public function select (string $query)
```

##### fetch
Fetches first record of result. Result must contain values with numerical keys begining with 0 in natural select field order.
```php
array public function fetch (string $query)
```

##### value
Fetches first value of first record of result.
```php
string public function value (string $query)
```

##### error
Must return true or false if passed $code patarmeter and $code parameter equals actual error code that had place. Or must return error information if no $code parameter passed. Comparing passed $code and returning true or false is important part of link class while autmaticaly generating database structure.
```php
boolean/mixed public function error (integer $code=null)
```

##### id
Returns last inserted id for that connection
```php
integer public function id ()
```

For further reading see sample codes using only \db\link https://github.com/hazardland/db.php/blob/master/samples/002.link.php

## Connect using many links
First init database
```php
$database = new \db\database ();
```
Or first init database with default database name:
```php
$database = new \db\database ('my_default_db_name');
```
It is assumed that this database is located on first connection link you ever establish (Or even located on any link for tables without database name specification).


Than add links as many as you wish:
```php
$database->link (new \db\link ('my_mysql_link', 'mysql:host=127.0.0.1', 'my_user', 'my_pass'));
$database->link (new \db\link ('my_odbc_link', 'odbc:my_odbc_alias', 'my_user', 'my_pass'));
```

**Note that you can specify custom links to tables also custom databases to tables. By default table without link specification is located on default link. Table without database specification is located in default database.

Additional link usage samples https://github.com/hazardland/db.php/blob/master/samples/002.link.php

# Prepare

## Map class to table
Here begins most interesting part of db.php as we have setted up desired connection(s) now we need to make database know about our class(es).

To add single "class to table" handler or so called "class handler" you have to:

```php
$database->add (string $class);
```

```php
$database->add('\path\to\class');
```
Or
```php
$database->add('\\path\\to\\class');
```
Also you can specify class path by dots:
```php
$database->add('path.to.class');
```
First dot in string makes no sense but you also can:
```php
$database->add('.path.to.class');
```

For example if we have:
```php
namespace shop;

class product
{
    ....
}
```

We can:

```php
$database->add ('shop.product');
```

We added class handler to orm now we need to access class handler.

After adding class to database we can access class table handler by
```php
$database->path->to->class
```

In \shop\product example class case we will access it like this:
```php
$database->shop->product
```

*Note*: The only reserved namespace name is 'context' it means you must not have classes in namespace named 'context' or class without namespace named 'context'. $database->context is used by database object for storing various data such is conenction links, table handlers, defined locales, varius cache handlers and so on.


## Map namespace classes to tables

If we have many classes in namespace we can add them using just one line of code:

```php
namespace shop;

class product
{
    ....
}
class cart
{
    ....
}
class cost
{
    ....
}
```

```php
$database->scan ('\namespace');
```
Or
```php
$database->scan ('\\namespace');
```
Or
```php
$database->scan ('.namespace');
```

In this case if we scan:

```php
$database->scan ('.shop');
```
We will have following class table handlers:

```php
$database->shop->product
$database->shop->cart
$database->shop->cost
```

Class table handler is an instance of \db\table it contains information for mapping class to actual database table and api for accessing class table data. For further exploration of class table handlers see sample at https://github.com/hazardland/db.php/blob/master/samples/003.class.php

## Map classes by pattern to tables
```php
$database->scan (string $string);
```

$database->scan also adds any class whose full name (\path\to\class) begins on given $string

If we have:
```php
\animals\wolf
\ant
\animation
```
Than
```php
$database->scan ('.ani');
```
Or
```php
$database->scan ('\\ani');
```
Or
```php
$database->scan ('\ani');
```
Will add \animals\wolf and \animation classes table handlers to orm.

## Get registered table handler of class

By path to class
```php
$database->path->to->class;
```

By string
```php
$database->table('.path.to.class');
```

By object
```php
$object = new \path\to\class();
$database->table($object);
```

Example:

```php
$product = new \shop\product();
$database->add ($product);

$database->shop->product;
$database->table('.shop.product');
$database->table($product);
```

## Create databases for mapped classes
Any databases specified to classes or specified to \db\database constructor which do not exist will be created on specified connection sql server or on specified data source by following function:
```php
$database->update();
```

*$database->update() creates or updates any changes to databases, tables or fields*

## Create tables for mapped classes
Any non existent tables for registered classes in orm will be created on their specified databases on their connection links by following function:
```php
$database->update();
```

*$database->update() creates or updates any changes to databases, tables or fields*

## Update table structures or Update/create new fields
Any recent changes in tables or in fields settings discovered in class or class property descriptions (i.e. field type change for class property) will be affected to databases by following function:
```php
$database->update();
```

*$database->update() creates or updates any changes to databases, tables or fields*

## Generate sql dump for database changes and save to file
Instead of running alter queries when using $database->update() you can save that queries to file for further testing and usage:

```php
$database->update('\path\to\database_changes.sql');
```

*Note: In case of dump file in case of multi sql server/data source architecture you will not be able to distinguish which query belongs to which connection*

# Load

## Load all from table and iterate throught the result
To load all records for table which in case of ORM means to **load all objects of class** use:
```php
$result = $database->path->to->class->load ();
```

Example:
```php
$products = $database->shop->product->load ();

if ($products)
{
    foreach ($products as $product)
    {
        echo $product->name;
    }
}
```

Result represents regular array, keys of array represents ids of objects. If we know that we have product with id 2 we can access it by following code:

```php
echo $products[2]->name;
```

## Load by id from table
To load single object by it for class from table use following:
```php
$database->path->to->class->load (mixed $id);
```

Example:
```
$product = $database->shop->product->load (2);
echo $product->name;
```

If primary field is string like $currency->code:
```php
$currency = $database->cashier->currency->load ('USD');
echo $currency->name;
```

## Load by field equals value or field like value
To load object by custom field value:
```php
$database->path->to->class->load (\db\by(string $field1, mixed $value1)->by(string $field2, mixed $value2)->in(string $field3, mixed $value));
```

\db\by and \db\in create instance of \db\by class which has to methods \db\by:by and \db\by::in each method returns instance of itself therefore you can add another criteria by calling again method 'in' or method 'by'. Table handler than generates a query with cross criterias with 'AND'.

For example:
```php
$database->user->user->load (\db\by('login','administrator')->by('password','1234')->in('group',1));
```

Will generate query something like this:
```
... where login='administrator' and password='1234' and group like '%|1|%'
```
'in' method generates 'like' statement for enumerated fields which in db.php are stored like '|value1|value2|value3|'. Enumerated fields are subject of further reading and we will not describe it here in details.

**value** parameter can also be objects or boolean values except of regular strings:
```php
$group = $database->user->group->load (1);

$database->user->user->load (\db\by('group',$group)->by('active',true));
```

Each string is escaped and protected from injection.

Dont get confused because of \db\ prefix in \db\by function call followed by simply ->by in \db\by()->by. \db\ is namespace prefix and it is necessary because by function resides in db namespace.

## Load using pager from table
db.php has nice pager. When using \db\pager table handler automaticaly calculates total count of results and paginates to specified size of chunks.

```php
$pager = new \db\pager(integer $page, integer $count);
```

**page** is current page

**count** is count of items per page

To use pager simply pass it to load function:

```php
$database->path->to->class->load (\db\pager $pager);
```

Example:

```php
$pager = new \db\pager (null, 8);

$products = $database->shop->product->load ($pager);

echo count ($products);
```

Outputs 8 if items in products table are not less than 8


To load items from page 2:
```php
$pager = new \db\pager (3, 8);
$products = $database->shop->product->load ($pager);

\db\debug($pager);
```

Pager object will look like this if we have total 30 products in table:

```php
db\pager::__set_state(array(
      'page'  =  3,
      'pages'  =  4,
      'total'  =  30,
      'count'  =  8,
      'from'  =  16,
))
```

Current page
```php
$pager->page
```

Number of pages available
```php
$pager->pages
```

Total items in table
```php
$pager->total
```

Item count per page
```php
$pager->count
```

First item number of result
```php
$pager->from
```

Table handler simply uses
```php
... limit $pager->from, $pager->count
```
To generate query

To be simple you can do pager loading in one line:
```php
$database->shop->product->load(new \db\pager($_REQUEST['page'], 10));
```

Where $_REQUEST['page'] holds current page number. If you pass greater number in page parameter than available pages than current page will be set to last page. If you pass page number less than 1 or null or not integer than current page will be set to 1.

Pager also has method next it is rarely usefull but allows to load items from table by small chunks not to overfill memory and iterate through them:
```php
$pager = new \db\pager (null, 10);

do
{
    $result = $database->shop->product->load ($pager);
    if ($result)
    {
        foreach ($result as $product)
        {

        }
    }
}
while ($pager->next());
```
This will iterate throgh every product and will load them 10 by 10. Sometimes if you have 99999 records in table and you have to affect them all it is extremly important not to load them all because objects in result might overload memory if they are large.

# Query

## Load using custom query
```php
$query = new \db\query();
$result = $database->path->to->table->load($query);
```

Next chapters will discuss how to adjust query order, specify custom select criteria in where, group using custom field, specify limit or even attach pager to custom query.

## Query helper functions
Before using queries whe need to now about few helper functions:
```php
namespace shop
{
    class product
    {
        public $id;
        public $name;
    }
}
$database->add ('shop.product');
$database->update ();
```
In this case table with name 'shop_product' will be created with fields 'id' and 'name'. Full table name for class looks like `my_db`.`shop_product` and full field name for property 'name' looks like `my_db`.`shop_product`.`name`.

In custum queries you will need to use full table and field names to avoid ambigous field name errors.

##### \db\table::name
To get **table name** use:
```php
$database->shop->product->table();
//Outputs something like `my_db`.`shop_product`
```
Or:
```php
$database->shop->product->name();
//Outputs something like `my_db`.`shop_product`
```

##### \db\table::field
To get **field name** use:
```php
$database->shop->product->field('name');
//Outputs something like `my_db`.`shop_product`.`name`
```

The **name** parameter for 'field' function must be property name, this means if you have public $login than property name is 'login' and to get full field name for login use $database->path->to->table->field('login')

##### \db\table::value
**value** function gerenates something useful also:
```php
$database->shop->product->value('name','Milk');
//`my_db`.`shop_product`.`name`='Milk'
```

##### \db\string
To **escape string** use function \db\string. Anything that does not go through the db.php functions like custom strings and comes from user input and used in queries must be escaped.
```php
$database->shop->product->field('cost').">".\db\string($_REQUEST['cost']);
//`my_db`.`shop_product`.`name`>5.1
```

## Query where
First initialize query object:
```php
$query = new \db\query ();
```
Than let us add custom select criteria:
```php
$query->where($database->hero->field('damage').">".\db\string($_REQUES['damage'])
." or ".$database->hero->value('race',$_REQUEST['race']));
```
Run query:
```php
$heroes = $database->hero->load ($query);
```

You can also use:
```php
$query->where->string .= ' AND ...';
```

And also set something that is string or reacts properly on strval()
```php
$query->where = $my_strval_object;
```

With queries you can adjust order, limit, group, join, cache type, pager usage and debug option. Next chapters will show you these tricks. But make sure you have read previous chapter about query helper functions like \db\table::name and \db\table::field or \db\string or \db\id.

## Query order
In previous chapters we talked about query initialization and simle usage. Now let us list step by step query object features.

Example class:
```
public product
{
    public $name;
    public $type;
    public $color;
}
```

Query can be ordered many ways but two common behaviors needs to be remembered as single field order simpliest way is to call \db\query::order
```php
$query->order ('color','desc');
//OR
$query->order ('color');
//OR
$query->order ('color','asc');
//asc is required when calling directly \db\query::order
```


And multi field order technics:
```php
$query->order->add ('color','desc');
$query->order->add ('name', 'asc');
$query->order->add ('type');
```
This will generate something like: order by color desc, name asc, type asc but with full field names


Single field order can by done with \db\order functions also
```php
$query->order->field ('color');
$query->order->method ('desc');
```

The very simple and not recomended usage also:
```php
$query->order->field = 'color';
$query->order->method = 'desc';
```

Some little usefull trick
```php
$query->order->method->swap();
```
It will swap order method 'asc' to 'desc', or if set to 'desc' than will swap to 'asc'.

Well this is also possible:
```php
$query->order->add (new \db\order('color','desc'));
$query->order->add (new \db\order('name'));
```

Have a look at \db\order class and \db\method class for further details.

\db\order has
```
\db\order::items - an array which stores \db\order objects if multi order takes place
\db\order::field - here order stores field
\db\order::method - here order stores method
\db\order::add(field, method)
\db\order::field(field)
\db\order::method(method)
```

\db\method has
```
\db\method::mode - here method stores \db\method::asc or \db\method::desc
\db\method::mode($mode) - set mode
\db\method:swap() - swap modes
\db\method::asc() - set mode asc
\db\method::desc() - set mode desc
```

## Query limit
With two parameters from and count
```php
$query->limit(integer $from, integer $count);
```

With only one parameter count
```php
$query->limit(integer $count);
```

Example:
```php
$query->limit (10, 30);
```
Generates *limit 10, 30*

Example:
```php
$query->limit (30);
```
Generates *limit 30*

## Load using custom query with pager
Query pager overrides query limit behavior if you call:

```php
$query->pager (integer page[, integer count=50])

```

This will create pager object at $query->pager and you can later access it. To know more about \db\pager class object please read [Load using pager from table](#load-using-pager-from-table).


## Load using query and return single object instead of object array
When using \db\query or \db\by by default \db\table returns array of objects. If you want to get only first object than call:
```php
$database->path->to->table->load ($query, true);
```
Second parameter of load notifies \db\table if user wants single object as result or array. True means single false means array. Default is false.

## Load affecting default load behavior or changing table default query
Every table handler has built in query object. When you call it without passing query handler uses built in query to process your request. For example if you want to change default behavior of load without creating query object:
```php
$database->shop->product->load ();
```
Let us say you want to chane default order field for all load calls of products, than you should:
```php
$database->shop->product->query->order('name');
```
After this where ever you call load result will be ordered by name, default query is active even when you are using \db\by
```php
$database->shop->product->load (\db\by('type',$type));
```
For more information about queries see: [Query where](#query-where), [Query order](#query-order), [Query limit](#query-limit)

# Save

## save single object to table
## save with boolean result
## save with saved object result
## save without knowing object table
## save objects to table

# Delete

## delete single object
## delete by id
## delete table objects
## delete various type of object same time
## delete by query from table

# Debug

# Cache

## cache user - cache table records on user level (default session)
## cache long - table records on server level (default apc_cache)
## cache temp - table records for script runtime (default memory)
## develop and plug custom cache engine for desired cache level


# Table

# table modifiers
## use custom name table for class
## use custom name field of table for class property

# Field

## field locaization

# Tools
## universal time handling