<?php

    namespace db
    {
        class type
        {
            const integer = 1;
            const boolean = 2;
            const float = 3;
            const text = 4;
            const blob = 5;
            const date = 6;
            const datetime = 7;
        }

        class action
        {
            const none = 0;
            const date = 1;
            const user = 2;
            /**
             * @var int
             */
            public $action = 0;
        }

        class event
        {
            const select = 1;
            const insert = 2;
            const update = 3;
            const delete = 4;
            /**
             * @var action
             */
            public $insert;
            /**
             * @var action
             */
            public $select;
            /**
             * @var action
             */
            public $update;
            /**
             * @var action
             */
            public $delete;
            public function __construct ()
            {
                $this->insert = new action();
                $this->select = new action();
                $this->update = new action();
                $this->delete = new action();
            }
        }

        class config
        {

        }

        class field
        {
            /**
             * @var string
             */
            public $name;
            /**
             * @var int
             */
            public $type = 0;
            /**
             * @var bool
             */
            public $required = false;
            /**
             * @var \ReflectionClass
             */
            public $foreign = null;
            /**
             * @var primary
             */
            public $primary = false;
            /**
             * @var \ReflectionClass
             */
            public $value = null;
            /**
             * @var bool
             */
            public $insert = true;
            /**
             * @var bool
             */
            public $select = true;
            /**
             * @var bool
             */
            public $update = true;
            /**
             *
             * @var event
             */
            public $event = null;
            /**
             * @var config
             */
            public $config = null;
            /**
             * @var int
             */
            public $length = 0;
            /**
             * not null
             * defualt hihu
             * required
             * length
             * @var boolean
             */
            public $unsigned = false;
            /**
             * @var field
             */
            public $after;
            /**
             * @var field
             */
            public $before;

            public $null;
            public function __construct ($name)
            {
                $this->name = $name;
                $this->event = new event ();
                $this->config = new config ();
            }
            public function type ()
            {
                if ($this->type==type::integer || $this->type==type::boolean)
                {
                    return "INTEGER";
                }
                if ($this->type==type::float)
                {
                    return "FLOAT";
                }
                if ($this->type==type::blob)
                {
                    return "BLOB";
                }
                return "TEXT";
            }
        }

        interface link
        {
            public function open ($hostname, $username, $password, $database);
            public function close ();
            public function query ($query);
        }

        interface user
        {
            public function id ();
        }

        class table
        {
            /**
             * @var string
             */
            public $name;
            public function __construct ()
            {

            }
            public function field ($name)
            {
            }
            public function load ($query)
            {

            }
            public function save ($query)
            {
            }
            public function delete ($query)
            {
            }
            public function exists ($query)
            {
            }
            public function of ($object)
            {
            }
            public function fields ()
            {
            }
            public function tables ()
            {
            }
        }

        class query
        {
            /**
             * @var \db\select
             */
            public $select;
            /**
             * @var \db\from
             */
            public $from;
            /**
             * @var \db\where
             */
            public $where;
            /**
             * @var \db\set
             */
            public $set;
            /**
             * @var \db\order
             */
            public $order;
            /**
             * @var \db\limit
             */
            public $limit;
            /**
             * @var bool
             */
            public $debug = false;
            /**
             * @var bool
             */
            public $single = false;
            public function __construct (\db\table &$table)
            {
                $this->select = new \db\select ($table);
                $this->from = new \db\from ($table);
                $this->where = new \db\where ();
                $this->set = new \db\set ();
                $this->order = new \db\order ($table);
                $this->limit = new \db\limit ();
            }
        }

        class select
        {
            /**
             * @var \db\table
             */
            private $table;
            public function __construct (\db\table &$table)
            {
                $this->table = &$table;
            }
            public function __toString ()
            {
                return " SELECT " + $this->table->fields() + " ";
            }
        }

        class from
        {
            /**
             * @var \db\table
             */
            private $table;
            public function __construct (\db\table &$table)
            {
                $this->table = &$table;
            }
            public function __toString ()
            {
                return " FROM " + $this->table->tables() + " ";
            }
        }

        class where
        {
            /**
             * @var string
             */
            public $query;
            public function __toString()
            {
                if ($this->query!="")
                {
                    return " WHERE ".$this->query." ";
                }
                return "";
            }
        }

        class set
        {
            /**
             * @var string
             */
            public $query;
            public function __toString()
            {
                if ($this->query!="")
                {
                    return " ".$this->query." ";
                }
                return "";
            }
        }

        class order
        {
            /**
             * @var \db\order[]
             */
            public $items = array();
            /**
             * @var \db\table
             */
            public $table;
            /**
             * @var \db\field
             */
            public $field;
            /**
             * @var \db\method
             */
            public $method;
            public function __construct (\db\table &$table, \db\field &$field=null, \db\method &$method=null)
            {
                $this->table = &$table;
                $this->field = &$field;
                $this->method = &$method;
            }
            public function add (\db\order $order)
            {
                $this->items[] = $order;
            }
            /**
             * @return string
             */
            public function field ()
            {
                if ($this->field!=null)
                {
                    return "`".$this->table->name."`.`".$this->field->name."`";
                }
                return "`".$this->table->name."`.`id`";
            }
            /**
             * @return string
             */
            public function method()
            {
                return $this->method->name;
            }
            public function __toString ()
            {
                if (is_array($this->items) && $this->items)
                {
                    $result = "ORDER BY ";
                    foreach ($this->items as $item)
                    {
                        $result .= substr($item,10).",";
                    }
                    return " "+substr($item,0,-1)." ";
                }
                return " ORDER BY ".$this->field." ".$this->method." ";
            }
        }

        class method
        {
            const asc = "ASC";
            const desc = "DESC";
            public $name = self::asc;
            public function __construct ($name=self::asc)
            {
                if ($name==self::asc || $name==self::desc)
                {
                    $this->name = $name;
                }
            }
            public function swap ()
            {
                if ($this->name==self::asc)
                {
                    $this->name = self::desc;
                }
                else
                {
                    $this->name = self::asc;
                }
            }
        }

        class limit
        {
            /**
             * @var int
             */
            public $from;
            /**
             * @var int
             */
            public $count;
            public function __construct ($from=null, $count=null)
            {
                if ($count==null)
                {
                    $this->count = $from;
                }
                else if ($from!=null)
                {
                    $this->from = $from;
                    $this->count = $count;
                }
            }
            public function __toString ()
            {
                if ($this->count==null)
                {
                    return "";
                }
                if ($this->from==null)
                {
                    return " LIMIT ".intval($this->count)." ";
                }
                return " LIMIT ".intval($this->from).",".intval($this->count)." ";
            }
        }

        interface value
        {
            public function set ();
            public function get ();
        }

        class database
        {
            /**
             * @var \db\link
             */
            public $link;
            /**
             * @var array[\db\table]
             */
            public $tables = array ();
            /**
             * @param \db\link $link
             */
            public function __construct (\db\link $link)
            {
                $this->link = $link;
            }
            /**
             * @param \db\table $table
             */
            public function add (\db\table $table)
            {
                $this->tables[$table->name] = $table;
            }
            public function table ($name)
            {
                return $this->tables[$name];
            }
        }

        abstract class entity
        {
            /**
             * @var int
             */
            public $id;
            /**
             * @return int
             */
            public function id ()
            {
                return $this->id;
            }
        }
    }

    namespace db\link
    {
        class mysql implements \db\link
        {
            public $link;
            public function open ($hostname, $username, $password, $database)
            {
                $this->link = mysql_connect ($hostname, $username, $password);
                mysql_select_db ($database, $this->link);
            }
            public function close ()
            {
                mysql_close ($this->link);
            }
            public function query ($query)
            {
                return mysql_query ($query);
            }
        }
    }

?>
