<?php

    namespace db
    {
        /**
         * goals:
         * 1. code first (generate database tables, maintain class property changes, alter field alter field properties)
         * 2. database abstraction interface
         * 3. caching engine
         * 4. cache abstraction interface
         * 5. language interface
         * 6. multilang field support (field_1_en, field_1_ge, field_1_fr, field_1_fr, ...)
         * 7. permission support
         * 8. user abstrction interface
         * 9. group abstraction interface
         *
         * objects with which database can work via interfaces:
         * user - your instance of user
         * group - your instance of user group
         * culture - your instance of culture
         * cultures - your instance of cultures
         * cache - your instance of cache
         * link - your database connection
         */

        /*
         * table sample_foo
         * prefix foo
         */
        class foo extends \db\entity
        {
            /**
             *required
             * field users_bio
             * type integer|boolean|float|text|binary|date
             * data
             * length 32
             * unsigned
             * zerofill
             * default/value 3
             * primary
             * foreign \db\bar
             * deny insert
             * allow update
             * deny insert for user biohazard
             * @var \test\master
             */
            public $name;

        }

        class bar extends db\entity
        {

        }

        class flag
        {
            public $name;
            public $value;
            public function __construct ($name, $value=null)
            {
                $this->name = $name;
                $this->value = $value;
            }
            /**
             *
             * @param string $line
             * @return \db\flag
             */
            public static function flag ($line)
            {
                $line = trim ($line);
                $start = strpos('*', $line);
                if ($start!==false)
                {
                    $crop = trim(substr($line,$start));
                    $set = explode(" ", $crop);
                    if (is_array($set) && count($set))
                    {
                        if (count($set)==1)
                        {
                            return new \db\flag($set[0]);
                        }
                        else
                        {
                            return new \db\flag($set[0],$set[1]);
                        }
                    }
                }
            }
            /**
             * @param \ReflectionProperty $value
             * @param \ReflectionClass $value
             * @return flag[] array of flags
             */
            public static function set ($value)
            {
                if ($value!=null)
                {
                    $comment = $value->getDocComment();
                }
                if ($comment)
                {
                    $result = array();
                    $comment = $value->getDocComment();
                    $lines = explode (" ", $comment);
                    if (is_array($lines) && $lines)
                    {
                        foreach ($lines as $line)
                        {
                            $flag = self::flag($line);
                            if ($flag!=null)
                            {
                                $result[] = $flag;
                            }
                        }
                        return $result;
                    }
                }
                return array();
            }
        }

        class type
        {
            const integer = 1;
            const boolean = 2;
            const float = 3;
            const string = 4;
            const binary = 5;
            const date = 6;
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
             * name of field
             * @var string
             */
            public $name;

            /**
             * basic type of field
             * @var int
             */
            public $type = type::string;

            /**
             * field class if any
             * @var \ReflectionClass
             */
            public $class = null;

            /**
             * foreign field table id
             * @var \ReflectionClass
             */
            public $foreign = null;

            /**
             * sql store type of field
             * @var string
             */
            public $data;

            /**
             * @var bool
             */
            public $required = false;

            /**
             * @var primary
             */
            public $primary = false;

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
             * @var string
             */
            public $length;
            /**
             * not null
             * defualt hihu
             * required
             * length
             * @var boolean
             */
            public $unsigned = false;
            public $null = false;
            public $zero = false;

            public function __construct (\db\table &$table, \ReflectionProperty $value)
            {
                if ($value==null)
                {
                    throw new \Exception();
                }
                $this->event = new event ();
                $this->config = new config ();
                $this->name = $value->getName();
                if (strtolower($this->name)=='id')
                {
                    $table->primary = &$this;
                }
                $flags = flag::set($value);
                if (is_array($flags))
                {
                    foreach ($flags as &$flag)
                    {
                        /* @var flag \db\flag */
                        if ($flag->name=='@var')
                        {
                            if ($flag->value=='')
                            {
                                throw new \Exception('@var doc comment required for '.$table->name.'.'.$this->name.' property');
                            }
                            if ($flag->value=='integer')
                            {
                                $this->type = type::integer;
                            }
                            else if ($flag->value=='text')
                            {
                                $this->type = type::string;
                            }
                            else if ($flag->value=='date')
                            {
                                $this->type = type::date;
                            }
                            else if ($flag->value=='boolean')
                            {
                                $this->type = type::boolean;
                            }
                            else if ($flag->value=='float')
                            {
                                $this->type = type::float;
                            }
                            else if ($flag->value=='binary')
                            {
                                $this->type = type::binary;
                            }
                            else
                            {
                                $this->class= new \ReflectionClass($flag->value);
                                if ($this->class!=null)
                                {
                                    if ($this->class->isSubclassOf('\db\entity'))
                                    {
                                        $this->type = type::integer;
                                        $this->foreign = table::id ($flag->value);
                                    }
                                    else if ($this->class->implementsInterface('\db\value'))
                                    {
                                        $this->type = type::string;
                                    }
                                    else
                                    {
                                        throw new \Exception ('field not needed');
                                    }
                                }
                            }
                        }
                        elseif ($flag->name=='type')
                        {
                            $this->data = strtolower($flag->value);
                        }
                        elseif ($flag->name=='required')
                        {
                            $this->required = true;
                        }
                        elseif ($flag->name=='primary')
                        {
                            $this->primary = true;
                        }
                        elseif ($flag->name=='deny')
                        {
                            if ($flag->value=='insert')
                            {
                                $this->insert = false;
                            }
                            else if ($flag->value=='update')
                            {
                                $this->update = false;
                            }
                            else if ($flag->value=='select')
                            {
                                $this->select = false;
                            }
                        }
                        else if ($flag->name=='length')
                        {
                            $this->length = $flag->value;
                        }
                        else if ($flag->name=='unsigned')
                        {
                            $this->unsigned = true;
                        }
                        else if ($flag->name=='null')
                        {
                            $this->null = true;
                        }
                        else if ($flag->name=='zerofill' || $flag->name=='zero')
                        {
                            $this->zero = true;
                        }

                    }
                }
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
             *
             * @var string
             */
            public $id;
            /**
             * @var \db\database
             */
            public $database;
            /**
             * @var string
             */
            public $name;
            /**
             * @var \ReflectionClass
             */
            public $class;
            /**
             * @var \db\field[]
             */
            public $fields = array();
            /**
             * @var \db\field
             */
            public $primary;
            /**
             * @var string
             */
            public $prefix;
            /**
             * @return string get id for class
             * @param string $class
             */
            public static function id ($class)
            {
                $class = str_replace ("\\", ".", $class);
                if ($class[0]!='.')
                {
                    $class = '.'.$class;
                }
                if ($class[strlen($class)-1]=='.')
                {
                    $class = substr($class, 0, -1);
                }
                $this->id = $class;
            }
            /**
             * @param \db\database $database
             * @param type $class
             */
            public function __construct (\db\database &$database, $class)
            {
                $this->database = &$database;
                $class = str_replace ("\\", ".", $class);
                if ($class[0]!='.')
                {
                    $class = '.'.$class;
                }
                if ($class[strlen($class)-1]=='.')
                {
                    $class = substr($class, 0, -1);
                }
                $this->id = $class;
                if (strripos($class,"."))
                {
                    $this->name = substr($class,strripos($class,"."));
                }
                else
                {
                    $this->name = $class;
                }
                $this->class = new \ReflectionClass (str_replace (".", "\\", $class));
                foreach ($this->class->getProperties() as $value)
                {
                    /* @var $value \ReflectionProperty */
                    try
                    {
                        $field = new \db\field($value);
                        $this->fields[$field->name] = $field;
                    }
                    catch (Exception $fail)
                    {

                    }
                    //$type = new \db\type($value);
                    //$type->
                }
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
             * @var \db\table[]
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

        interface culture
        {
            /**
             * @return string get culture name
             */
            public function name ();
            /**
             *
             * @param string $name
             * @return boolean check if culture exists
             */
            public function exists ($name);
            /**
             * @return culture[] get defined cultures
             */
            public function items ();
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
