<?php

	//this code only demonstrates basic
	//working principles of the framework


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

	class table
	{
		public static $database;
	}

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

	class user
	{
		public $id;
		public $name;
		public function __construct ($name=null)
		{
			$this->name = $name;
		}
	}

	$database = new database (new link('127.0.0.1', 'system_db', 'root', '1234'));
	$database->tables['users'] = new users();

	if (!$database->tables['users']->save (new user('Admin')))
	{
		var_export($database->link->error());
	}

	var_export($database->tables['users']->load(2));


?>