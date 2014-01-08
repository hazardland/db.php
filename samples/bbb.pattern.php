What if I told you that there are more beatuiful ways to put things on their places.
A very simple case might contain 3 basic components to work:

  1. Db framework - Which handles data access.
  2. Table repsotor classes - Which know how to map classes to tables,
how to create classes from table data and how to create data from table classes.
  3. Model or business layer which contain actual classes.

For better understanding imagine you have database object mapper framework.
The framework can be far complex but in few lines we can demonstrate how it`s basic
concepts work.

So the 'Framework':

<?php

//This class is for making link for db framework
class link
{
	public $link;
	public function __construct ($hostname, $database, $gamename, $password)
	{
		$this->link = new \PDO ('mysql:host='.$hostname.';dbname='.$database, $gamename, $password);
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

//This class collects table repositories and connections
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

//This is basic table repositor class
class table
{
	public static $database;
}

?>

Now as we have our db framework let us make some table repositor which knows
how to save/load/delete game:

<?php

class games extends table
{
	public function create ($row)
	{
		$return = new game ();
		$return->id = $row[0];
		$return->name = $row[1];
		var_export($row);
		return $return;
	}
	public function load ($id=null)
	{
		if ($id==null)
		{
			$result = self::$database->link->fetch("select * from games");
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
			$result = self::$database->link->fetch("select * from games where id='".$id."'");
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
	public function save ($game)
	{
		if (is_array($save))
		{
			foreach ($save as $item) $this->save ($item);
		}
		if ($game->id==null)
		{
			return self::$database->link->query("insert into games set
											     name='".$game->name."'");
		}
		else
		{
			return self::$database->link->query("update games set name='".$game->name."'
										  		 where id='".$game->id."'");
		}
	}
	public function delete ($game)
	{
		self::$database->link->query ("delete from games where id='".$game->id."'");
	}
}
?>

Now  we can make our model which in this case will contain actuall game class.

<?php
class game
{
	public $id;
	public $name;
	public function __construct ($name=null)
	{
		$this->name = $name;
	}
}
?>


And than actually use it:

<?php
$database = new database (new link('127.0.0.1', 'system_db', 'root', '1234'));
$database->tables['games'] = new games();

if (!$database->tables['games']->save (new game('Admin')))
{
	var_export($database->link->error());
}

var_export($database->tables['games']->load(2));
?>

For the moment I prefere this pattern for working with db in my projects. Using it I can achieve
that my actuall business objects(In this case class game) will know nothing about
where and how they are saved. This gives me an ability to be indipendent from
actuall storage and focus on project logics.

Also there is one lightweight framework so called db.php (http://dbphp.net) and it even
gives me ability to avoid to write table repositories and even creates/modifies tables
needed for my business classes on the fly but uses almost same concept I described here.