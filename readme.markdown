db.php - represents Code First style ORM
================

Visit http://dbphp.net for more info as readme.md update is in progress.

1. It reads your class definitions and creates/modifies databases/tables/fields
according extracted data from classes and its properties.
2. To collect additional info it uses doc comments.
3. Supports relations between classes and its properties one to one, one to many and many to many.
4. Has extendable caching engine. Uses apc_cache for long cache type by default and session for user type cache
5. Supports localization. Just one directive to localize field and it creates and handles fields for localized values

**Imagine you have a simple user class namespace in 'user.php'**

```php
namespace user;

class user
{
	public $id
	public $name;
	/**
	* @var date
	*/
	public $birth;
	/**
	* @var \user\group
	*/
	public $group;
	public function __construct ($name=null, $birth=null, $group=null)
	{
		$this->name = $name;
		$this->birth = $birth;
		$this->group = $group;
	}
	public function age ()
	{
		return intval((time()-strtotime($this->birth))/(3600*24*365));
	}
}

class group
{
	public $id;
	/**
	* length 32
	*/
	public $name;
	public function __construct ($name=null)
	{
		$this->name = $name;
	}
}
```

**And imagine you have simple 'action.php'**
```php

	include './db.php';

	//init database with simple db connection link (multi links and multi databases are also possible)
	$database = new \db\database ('mysql:host=127.0.0.1', 'test', 'root', '1234');

	//add single class to database to handle
	$database->add ('\user\user');

	//or scan and add every class in namespace
	$database->scan ('\user');

	//update database means in this case:
	//1. create database test if not exists
	//2. create table for class \user\user and class \user\group if not exists
	//   or if they exists update its structure if developer changed something
	//	 in class definition
	$database->update ();

	//from this now you are ready to work with \user\user and \user\group classes

	//create and save simple group called 'Administrator'
	$administrator = $database->save (new \user\group ('Administartor'));

	//create user 'John' with group 'Administrator'
	$database->save (new \user\user ('John','1985-12-15',$administrator));

	//so if you want to load group 'Administrator' and you know that its id is 1
	$administrator = $database->user->group->load (1);
	//address user->group repeats address of class with namespace \user\group

	//so user handler address will be user->user as it's class is \user\user
	$user = $database->user->user->load (1);

	//and now relation happened
	//as in class definition user::group property has doc annotation @var \user\group
	//this means that user::group property is related to \user\group class
	//so if we:

	echo $user->group->name;

	//we will have 'Administrator' as output result

	//now if we rename user's name
	$user->name = 'Mark';

	$database->save ($user);
	//we now updated user record in database

	$database->delete ($user);
	//we now deleted user

```

Dont think this few functionality was that db.php could do. It can do as far as everithing you might remember you need to do with data including queries and caching and multi server multi database connections and including localisation and so on. You should see examples in examples dir for further information.