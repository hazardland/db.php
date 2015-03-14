#Things to know before using ORM
##What is MVC ? What is model view controller pattern ?

**MVC** stands for **Model View Controller**. If you heard about it before but still dont know what it is you might be afraid of it.

But MVC is nothing than bunch of terms describing best practices how code in big projects should be organized for not getting lost in them.

If you have developed at least some kind of web application you already have used **MVC** concepts withought knowing it. I could tell you that **Controller** is a portion of code which displays **Views** to user and also receives **input** from user, **processes** it using **Model** and than decides in what **View** to parse **output** data but nobody understands sentences like that.

Therefore I ll show you an example. (*Note that while using db.php our goal is only knowing how to architect Model*)

View is very simple thing. Dont get anything what you read here literally I m trieng to come out with the simpliest examples. 

Imagine a task. We must build a product page where user will purchase product.

We have two html files: product.html and success.html

####product.html####
```html
<h1>{product_name}</h1>
<a href="{project_link}?page=purchase&product={product_id}">
    Buy product
</a>
```

####purchase.html####
```html
You have successfuly purchased {product_name}
```

This are two stupid static html files and we can totally consider them as **views**. In {product_name} there goes actual product name. When user clicks "Buy product" we will have page and product variables incoming in our script. Congratulations you know what views are !

But how to use that views in actual task ? Here we need a controller. Let us assume we are so stupid we build our entire site php script in only index.php?

####index.php####
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

So this is **Controller**. If you look closer it containts two sections. They are almost identical. One parses page "product" and another parses page "purchase". That sections are almost identicall except in section "purchase" user buys a product while in section "product" user views product page. View variables like {product_name} and {product_id} are replaced using simple str_replace function. Views files are loaded simply by file_get_contents. And line $product->buy() actually does what it says. But where is that method code called **buy** ?

####shop.php####
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

##How can I develop my projcect model with classes ?

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

#What is PHPDoc ?
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

##What is ORM ? What is Object Relational Mapper ?
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

##Why do i have low sallary as a php programmer ?
Because you dont use classes in your model or you dont have model in your projects at all.

Nothing great can be done without it.

#Intermediate level

##installation
The only file you need is **db.php** iself. just include db.php in your project and you are ready to go.

```php
include './db.php';
```

**db.php** requires **apc_cache** because it is default caching engine for it. You can also override default caching engine but it is subject for further reading. A link for apc_cache installation instructions is here http://php.net/manual/en/apc.installation.php. On windows you just need to download proper php_apc.dll and enable it in php.ini by uncommenting line extension=php_apc.dll

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

For some real reasons **db.php** generates notice level errors. If you have not set restricted notices in error reporting section in your php.ini file you can set it manually on runtime like:

```php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
```

For additional information read http://php.net/manual/en/function.error-reporting.php

##setup connection
  simple way to start is to specify server username password and database name to db.database constructor. but remember you can connect to many servers same time and you can use many databases

##map class to table
##map namespace classes to tables
##map classes by pattern to tables

##get table by class
##get table by object

##create databases for mapped classes
##create tables for mapped classes

##synch changes to table structures

##use custom name table for class
##use custom name field of table for class property

##load all from table
##load by id from table
##load using pager from table
##load by field equals value field like value
##load using custom query from table
##load using custom query with pager

##save single object to table
##save with boolean result
##save with saved object result
##save without knowing object table
##save objects to table

##delete single object
##delete by id
##delete table objects
##delete varius type of object same time
##delete by query from table

##cache user - cache table records on user level (default session)
##cache long - table records on server level (default apc_cache)
##cache temp - table records for script runtime (default memory)
##develop and plug custom cache engine for desired cache level

##universal time handling

##table modifiers

##field modifiers

##field locaization