Well let us make some ORM on the fly. As you marked php tag let us code it in PHP.

But before we write it we must know some basic concepts or some basic terminology
about orm related subjects. In this example we will have:

1. ORM - ORM takes responsibility to take care about server connections and server connection abstractions. (Full ORMs also support automatic class to table mappings).

2. Data layer - This part is responsible for mapping classes to tables.
For example data access layer knows how to save specific class object to actual table and how to load specific table to specific class object. (NOTE: Almost any recent ORM can avoid you from this layer. For example http://dbphp.net or Doctrine will support every aspect of this layer plus relations and even auto table generation).

3. Business layer - This layer contains your actual working classes business layer often stands for model or model includes business layer

Let us begin our example from Business layer or model. Our very very simple project which saves and loads users will have one class business layer:

    <?php
    class user
    {
    	public $id;
    	public $name
    	public function __construct ($name=null)
    	{
    		$this->name = $name;
    	}
    }
    ?>

As you see your business layer or model knows nothing about where and how it is saved or loaded. It just does only handle project related business. That's the point the layer name comes from.

Second, let us make a simple ORM:

    <?php

    //The connection link which can be changed any time
    class link
    {
    	public $link;
    	public function __construct ($hostname, $database, $username, $password)
    	{
    		$this->link = new \PDO ('mysql:host='.$hostname.';dbname='.$database, $username, $password);
    		$this->link->query('use '.$database);
    	}
    	public function fetch ($query)
    	{
    		$result = $this->link->query($query)->fetch();
    	}
    	public function query ($query)
    	{
    		return $this->link->query($query);
    	}
    	public function error ()
    	{
    		return $this->link->errorInfo();
    	}
    }

    //A structure which collects all link(s) and table/class handlers togather
    class database
    {
    	public $link;
    	public $tables = array ();
    	public function __construct ($link)
    	{
    		$this->link = $link;
    		table::$database = $this;
    	}
    }

    //A basic table handler class
    //In recent ORMs they do all the default mappings
    class table
    {
    	public static $database;
    }
    ?>

As you noticed our table class in our ORM seems very poor. But if this framework was a
complex framework it would support also data layer and had all functionality to work with any table.

But because you need to know how ORMs work in this case we will make data layer
handlers for every class in our business layer.

So this is your data layer. It is so self descriptive that I think it does not
need any documentation:

    <?php
    class users extends table
    {
    	public function create ($row)
    	{
    		$return = new user ();
    		$return->id = $row[0];
    		$return->name = $row[1];
    		var_export($row);
    		return $return;
    	}
    	public function load ($id=null)
    	{
    		if ($id==null)
    		{
    			$result = self::$database->link->fetch("select * from users");
    			if ($result)
    			{
    				$return = array();
    				foreach ($result as $row)
    				{
    					$return[$row[0]] = $this->create($row);
    				}
    				return $return;
    			}
    		}
    		else
    		{
    			$result = self::$database->link->fetch("select * from users where id='".$id."'");
    			if ($result)
    			{
    				return $this->create(reset($result));
    			}
    			else
    			{
    				echo ("no result");
    			}
    		}
    	}
    	public function save ($user)
    	{
    		if (is_array($save))
    		{
    			foreach ($save as $item) $this->save ($item);
    		}
    		if ($user->id==null)
    		{
    			return self::$database->link->query("insert into users set
    											     name='".$user->name."'");
    		}
    		else
    		{
    			return self::$database->link->query("update users set name='".$user->name."'
    										  		 where id='".$user->id."'");
    		}
    	}
    	public function delete ($user)
    	{
    		self::$database->link->query ("delete from users where id='".$user->id."'");
    	}
    }
    ?>

1. At last let us init $database object
2. Establish some to some sql server link.
3. Add user class handler to database.
4. Use it.

Here is it in work:

    <?
    $database = new database (new link('127.0.0.1', 'system_db', 'root', '1234'));
    $database->tables['users'] = new users();

    if (!$database->tables['users']->save (new user('Admin')))
    {
    	var_export($database->link->error());
    }

    var_export($database->tables['users']->load(2));
    ?>

If you need to dive in other concepts of php ORM's feel free to visit
  1. Doctrine - http://www.doctrine-project.org/ - Full functional complex php ORM
  2. db.php - http://dbphp.net/ - Full functional but very easy php ORM.