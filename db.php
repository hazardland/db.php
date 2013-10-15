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
         * database site_blank
         * link default
         * prefix foo
         * order field [asc|desc]
         * charset utf8
         * engine myisam
         * rename oldname
         * cache none|load|user|long
         * scope project|solution
         * unique name
         * unique search id,name
         * index fast id,name
         * ignore
         * deny insert|select|update|delete
         */
        class foo extends entity
        {
            /**
             *required
             * field/column users_bio
             * type integer|boolean|float|text|binary|date
             * data
             * length 32
             * locale
             * enum
             * unsigned
             * zerofill
             * default 3
             * primary
             * rename users
             * first
             * after id
             * ignore
             * foreign \db\bar
             * deny insert
             * allow update
             * deny insert for user biohazard
             * @var \test\master
             */
            public $name;

        }

        class bar extends entity
        {

        }

        class link
        {
            public $name;
            public $debug = false;
            /**
             * @var PDO
             */
            public $link;
            public $config = array (\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
            public function __construct ($name, $database='127.0.0.1', $username='root', $password='1234', $settings=null)
            {
                $this->name = $name;
                if ($settings)
                {
                    $this->settings = $settings;
                }
                try
                {
                    $this->link = new \PDO ($database, $username, $password, $this->config);
                }
                catch (PDOException $error)
                {
                    echo $error->getMessage();
                }
            }
            public function select ($query)
            {
                if ($this->debug)
                {
                    $result = $this->link->query ($query);
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"\n".$error[2]:"");
                        debug ($debug);
                    }
                    else
                    {
                        debug($query);
                    }
                    return $result;
                }
                return $this->link->query ($query);
            }
            public function query ($query)
            {
                if ($this->debug)
                {
                    $result = $this->link->query ($query);
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"\n".$error[2]:"");
                        debug ($debug);
                    }
                    else
                    {
                        debug($query);
                    }
                    return $result;
                }
                return $this->link->query ($query);
            }
            public function value ($query)
            {
                $result = $this->link->query ($query);
                if ($this->debug)
                {
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"\n".$error[2]:"");
                        debug ($debug);
                    }
                    else
                    {
                        debug($query);
                    }
                }
                if ($result)
                {
                    return $result->fetchColumn(0);
                }
            }
            public function fetch ($query)
            {
                $result = $this->link->query ($query);
                if ($this->debug)
                {
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"\n".$error[2]:"");
                        debug ($debug);
                    }
                    else
                    {
                        debug($query);
                    }
                }
                if ($result)
                {
                    return $result->fetch();
                }
            }
            public function error ($code=null)
            {
                if ($code!==null)
                {
                    $error = $this->link->errorInfo();
                    if ($error && $error[0]==$code)
                    {
                        return true;
                    }
                    return false;
                }
                return $this->link->errorInfo();
            }
            public function id ()
            {
                return $this->link->lastInsertId();
            }
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
                if ($line)
                {
                    $start = strpos($line, '*');
                    if ($start!==false)
                    {
                        $start++;
                        $crop = trim(substr($line,$start));
                        $set = explode(" ", $crop);
                        if (is_array($set) && count($set))
                        {
                            if (count($set)==1)
                            {
                                return new flag($set[0]);
                            }
                            else
                            {
                                return new flag($set[0],$set[1]);
                            }
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
                    $lines = explode ("\n", $comment);
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

        class field
        {
            /**
             * name of field
             * @var string
             */
            public $name;

            /**
             * actual name of field
             * @var string
             */
            public $column;

            /**
             * basic type of field
             * @var int
             */
            public $type = type::string;

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
            public $locale = false;
            /**
             * @var bool
             */
            public $enum = false;
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
             * @var string
             */
            public $length;

            public $default;
            /**
             * not null
             * defualt hihu
             * required
             * length
             * @var boolean
             */
            public $unsigned = false;
            /**
             * @var bool
             */
            public $null = false;
            /**
             * @var bool
             */
            public $zero = false;
            /**
             * @var bool
             */
            public $after = null;
            /**
             * @var bool
             */
            public $first = false;
            public $last = null;
            /**
             * rename from
             * @var string
             */
            public $rename = null;
            /**
             * @var config
             */
            public $config = null;
            /**
             *
             * @var event
             */
            public $event = null;

            public $position;
            public $ignore = null;
            /**
             * field class if any
             * @var \ReflectionClass
             */
            public $class = null;
            public function __construct (\ReflectionProperty $value)
            {
                if ($value==null)
                {
                    throw new \Exception();
                }
                $this->value = $value;
                $this->event = new event ();
                $this->config = new config ();
                $this->name = $value->getName();
                $this->column = $value->getName();
//                if (strtolower($this->name)=='id')
//                {
//                    $table->primary = &$this;
//                }
                $flags = flag::set($value);
                if (is_array($flags))
                {
                    foreach ($flags as &$flag)
                    {
                        if ($flag->name=='ignore')
                        {
                            throw new \Exception ('field ignored');
                        }
                        /* @var flag \db\flag */
                        else if ($flag->name=='@var')
                        {
                            if ($flag->value=='')
                            {
                                throw new \Exception('@var doc comment required for '.$table->name.'.'.$this->name.' property');
                            }
                            if ($flag->value=='integer' || $flag->value=='int')
                            {
                                $this->type = type::integer;
                                if ($this->data==null)
                                {
                                    $this->data = 'int';
                                }
                            }
                            else if ($flag->value=='string')
                            {
                                $this->type = type::string;
                                if ($this->data==null)
                                {
                                    $this->data = 'char';
                                }
                                if ($this->length==null)
                                {
                                    $this->length = 128;
                                }
                            }
                            else if ($flag->value=='text')
                            {
                                $this->type = type::string;
                                if ($this->data==null)
                                {
                                    $this->data = 'text';
                                }
                            }
                            else if ($flag->value=='date')
                            {
                                $this->type = type::date;
                                if ($this->data==null)
                                {
                                    $this->data = 'date';
                                }
                            }
                            else if ($flag->value=='time')
                            {
                                $this->type = type::time;
                                if ($this->data==null)
                                {
                                    $this->data = 'datetime';
                                }
                            }
                            else if ($flag->value=='boolean' || $flag->value=='bool')
                            {
                                $this->type = type::boolean;
                                if ($this->data==null)
                                {
                                    $this->data = 'smallint';
                                }
                                if ($this->length==null)
                                {
                                    $this->length = 1;
                                }
                            }
                            else if ($flag->value=='float')
                            {
                                $this->type = type::float;
                                if ($this->data==null)
                                {
                                    $this->data = 'float';
                                }
                            }
                            else if ($flag->value=='binary')
                            {
                                $this->type = type::binary;
                                if ($this->data==null)
                                {
                                    $this->data = 'blob';
                                }
                            }
                            else
                            {
                                try
                                {
                                    $this->class= new \ReflectionClass($flag->value);
                                }
                                catch (\Exception $error)
                                {

                                }
                                if ($this->class!=null)
                                {
                                    if ($this->class->isSubclassOf('\db\entity'))
                                    {
                                        $this->type = type::integer;
                                        if ($this->data===null)
                                        {
                                            $this->data = 'int';
                                        }
                                        if ($this->length===null)
                                        {
                                            $this->length = 10;
                                        }
                                        if ($this->type==type::integer)
                                        {
                                            $this->unsigned = true;
                                        }
                                        $this->foreign = table::id ($flag->value);
                                    }
                                    else if ($this->class->isSubclassOf('\db\entity'))
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
                        else if ($flag->name=='default')
                        {
                            $this->default = $flag->value;
                        }
                        else if ($flag->name=='field' || $flag->name=='column')
                        {
                            $this->column= $flag->value;
                        }
                        elseif ($flag->name=='rename')
                        {
                            $this->rename = $flag->value;
                        }
                        elseif ($flag->name=='locale')
                        {
                            $this->locale = true;
                        }
                        elseif ($flag->name=='enum')
                        {
                            $this->type = type::string;
                            $this->data = 'char';
                            if ($this->length===null)
                            {
                                $this->length = 32;
                            }
                            $this->enum = true;
                        }
                        elseif ($flag->name=='first')
                        {
                            $this->first = true;
                        }
                        elseif ($flag->name=='after')
                        {
                            $this->after = $flag->value;
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
                if ($this->data===null)
                {
                    $this->data = 'char';
                    //$this->length = 128;
                }
                if ($this->data=='char' && $this->length==null)
                {
                    $this->length = 128;
                }
                if ($this->primary)
                {
                    $this->primary();
                }
            }
            public function type ()
            {
                $result = strtolower($this->data);
                if ($this->length!=null)
                {
                    $result .= "(".$this->length.")";
                }
                if ($this->unsigned)
                {
                    $result .= ' unsigned';
                }
                return $result;
            }
            public function primary ()
            {
                $this->primary = true;
                $this->default = null;
                $this->null = false;
                if ($this->type!=type::integer)
                {
                    $this->type = type::integer;
                    $this->data = 'int';
                    $this->length = 10;
                }
            }
            public function extra ()
            {
                $result = ' ';
                if ($this->primary)
                {
                    $result .= ' auto_increment';
                }
                if ($this->zero)
                {
                    $result .= ' zerofill';
                }
                if ($this->null)
                {
                    $result .= ' null';
                }
                else
                {
                    $result .= ' not null';
                }
                if ($this->default!==null)
                {
                    $result .= ' default ';
                    if (($this->default==='null' || $this->default==='NULL' || $this->default==='Null') || ($this->null && $this->default===null))
                    {
                        $result .= 'null';
                    }
                    else
                    {
                        $result .= "'".$this->default."'";
                    }
                }
                return $result;
            }
        }

        class table
        {
            /**
             *
             * @var string
             */
            public $id;
            /**
             * @var string
             */
            public $database;
            /**
             * @var string
             */
            public $name;
            /**
             * @var string
             */
            public $table;

            /**
             * @var string
             */
            public $engine = 'myisam';

            /**
             * @var string
             */
            public $charset = 'utf8';

            /**
             * @var bool
             */
            public $rename = null;

            /**
             * @var string
             */
            public $link = 'default';
            /**
             * @var string
             */
            public $prefix;
            /**
             * @var \ReflectionClass
             */
            public $class;
            public $cache = cache::none;
            public $scope = scope::project;
            /**
             * @var \db\field
             */
            public $primary;
            /**
             * @var query
             */
            private $columns;
            private $tables;
            public $query;
            private $hash;
            public $insert = true;
            public $select = true;
            public $update = true;
            public $delete = true;
            /**
             * @var \db\field[]
             */
            public $fields = array();
            /**
             * @return string get id for class
             * @param string $class
             */
            public static function id ($class, $trim=true)
            {
                $class = str_replace ("\\", ".", $class);
                if ($class[0]!='.')
                {
                    $class = '.'.$class;
                }
                if ($trim && $class[strlen($class)-1]=='.')
                {
                    $class = substr($class, 0, -1);
                }
                return $class;
            }
            /**
             * @param \db\database $database
             * @param type $class
             */
            public function __construct ($class)
            {
                $this->query = new query();
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
                if (strripos($class,".")!==false)
                {
                    $this->name = substr($class,strripos($class,".")+1);
                }
                else
                {
                    $this->name = $class;
                }
                $this->table = $this->name;
                $this->class = new \ReflectionClass (str_replace (".", "\\", $class));
                $flags = flag::set($this->class);
                if (is_array($flags))
                {
                    foreach ($flags as &$flag)
                    {
                        if ($flag->name=='ignore')
                        {
                            throw new \Exception ('table ignored');
                        }
                        elseif ($flag->name=='database')
                        {
                            $this->database = $flag->value;
                        }
                        else if ($flag->name=='prefix')
                        {
                            $this->prefix = $flag->value;
                        }
                        else if ($flag->name=='link')
                        {
                            $this->link = $flag->value;
                        }
                        else if ($flag->name=='table')
                        {
                            $this->table = $flag->value;
                        }
                        else if ($flag->name=='engine')
                        {
                            $this->engine = $flag->value;
                        }
                        else if ($flag->name=='charset')
                        {
                            $this->charset = $flag->value;
                        }
                        else if ($flag->name=='rename')
                        {
                            $this->rename = $flag->value;
                        }
                        else if ($flag->name=='order')
                        {
                            debug ($flag->value);
                        }
                        else if ($flag->name=='limit')
                        {
                            debug ($flag->value);
                        }
                        else if ($flag->name=='cache')
                        {
                            if ($flag->value=='long')
                            {
                                $this->cache = cache::long;
                            }
                            else if ($flag->value=='user')
                            {
                                $this->cache = cache::user;
                            }
                            else if ($flag->value=='load')
                            {
                                $this->cache = cache::load;
                            }
                            else if ($flag->value=='none')
                            {
                                $this->cache = cache::none;
                            }
                        }
                        else if ($flag->name=='scope')
                        {
                            if ($flag->value=='solution')
                            {
                                $this->scope = scope::solution;
                            }
                            else if ($flag->value=='project')
                            {
                                $this->project = scope::project;
                            }
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
                            else if ($flag->value=='delete')
                            {
                                $this->delete = false;
                            }
                        }
                    }
                }
                foreach ($this->class->getProperties() as $value)
                {
                    /* @var $value \ReflectionProperty */
                    try
                    {
                        $field = new field($value);
                        $this->fields[$field->name] = $field;
                    }
                    catch (\Exception $fail)
                    {

                    }
                    //$type = new type($value);
                    //$type->
                }
                if ($this->primary==null)
                {
                    if (isset($this->fields['id']))
                    {
                        $this->primary = &$this->fields['id'];
                        $this->primary->primary();
                    }
                }
            }
            public function name ($field=null, $locale=false, $short=false)
            {
                if ($field===null)
                {
                    $result = '';
                    if ($this->database!==null)
                    {
                        $result .= "`".$this->database."`.";
                    }
                    $result .= "`".$this->table."`";
                    return $result;
                }
                else
                {
                    if ($locale===false || $locale===true)
                    {
                        $short = $locale;
                    }
                    if (!is_object($field))
                    {
                        $field = $this->fields[$field];
                    }
                    if ($short===true)
                    {
                        if ($field->locale && is_object($locale) && isset($locale->name))
                        {
                            return "`".$this->prefix.$field->column."_".$locale->name."`";
                        }
                        else
                        {
                            return "`".$this->prefix.$field->column."`";
                        }

                    }
                    else if ($field->locale && is_object($locale) && isset($locale->name))
                    {
                        return $this->name().".`".$this->prefix.$field->column."_".$locale->name."`";
                    }
                    else
                    {
                        return $this->name().".`".$this->prefix.$field->column."`";
                    }
                }
            }
            public function enum ($set,&$table=null)
            {
                if (is_array($set))
                {
                    $result = '|';
                    foreach ($set as $item)
                    {
                        if (id($item))
                        {
                            $result .= $item.'|';
                        }
                    }
                    if (strlen($result)==1)
                    {
                        return '';
                    }
                    return $result;
                }
                else if ($table)
                {
                    if (strlen($set)>2)
                    {
                        $result = array();
                        $keys = explode ('|',substr($set,1,-1));
                        if ($keys && is_array($keys))
                        {
                            foreach ($keys as $key)
                            {
                                if ($key)
                                {
                                    $object = $table->load ($key);
                                    $result[$object->{$table->primary->name}] = $result;
                                }
                            }
                        }
                        return $result;
                    }
                    return array ();
                }
                return $set;
            }
            public function field ($name)
            {
                return $this->fields[$name];
            }
            public function create ($row, $from=0)
            {
                global $database;
                $cell = 0;
                if ($from)
                {
                    $cell = $from;
                }
                $result = $this->class->newInstance();
                foreach ($this->fields as $field)
                {
                    if ($field->select)
                    {
                        if ($field->locale && $database->locales())
                        {
                            foreach ($database->locales() as $locale)
                            {
                                $result->{$field->name."_".$locale->name} = $row[$cell];
                                $cell++;
                            }
                            $result->{$field->name} = &$result->{$field->name."_".$database->locale()->name};
                            if ($result->{$field->name."_".$database->locale(true)->name}!='')
                            {
                                foreach ($database->locales() as $locale)
                                {
                                    if ($result->{$field->name."_".$locale->name}=='')
                                    {
                                        $result->{$field->name."_".$locale->name} = $result->{$field->name."_".$database->locale(true)->name};
                                    }
                                }
                            }
                        }
                        else
                        {
                            $result->{$field->name} = $row[$cell];
                            $cell++;
                        }
                        if ($field->enum && $field->foreign)
                        {
                            $result->{$field->name} = $this->enum($row[$cell],$database->table($this->foreign));
                        }
                        else if ($field->foreign)
                        {
                            if (!$from)
                            {
                                $table = $database->table ($field->foreign);
                                if ($table)
                                {
                                    if ($table->link==$this->link)
                                    {
                                        $result->{$field->name} = $table->create($row,$cell);
                                        if ($result->{$field->name}->id===null && $row[$cell-1]!==null)
                                        {
                                            $result->{$field->name}->id = $row[$cell-1];
                                        }
                                        foreach ($table->fields as $foreign)
                                        {
                                            if ($foreign->locale && $database->locales())
                                            {
                                                $cell += count($database->locales());
                                            }
                                            else
                                            {
                                                $cell++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $result->{$field->name} = $table->load($row[$cell-1]);
                                        if ($result->{$field->name}===false)
                                        {
                                            $result->{$field->name} = $row[$cell-1];
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $result->{$field->name} = $row[$cell];
                                $cell++;
                            }
                        }
                    }
                }
                return $result;
            }
            public function load ($query=null)
            {
                global $database;
                if (!$this->select)
                {
                    return false;
                }
                if ($query===null)
                {
                    $query = $this->query;
                }
                if (!is_object($query))
                {
                    $row = $database->get ($this, string($query));
                    if (!$row)
                    {
                        $request = "select ".$this->fields()." from ".$this->tables()." where ".$this->name($this->primary)."='".string($query)."'";
                        $database->link($this->link)->debug = true;
                        $result = $database->link($this->link)->query ($request);
                        if (!$result)
                        {
                            return false;
                        }
                        $row = $result->fetch();
                        if ($row)
                        {
                            $database->set ($this, string($input), $row);
                        }
                    }
                    if ($row)
                    {
                        return $this->create ($row);
                    }
                }
                else
                {
                    $rows = $database->get ($this, $query);
                    if (!$rows)
                    {
                        $rows = array ();
                        $request = "select ".$this->fields()." from ".$this->tables()." ".$query->where($this)." ".$query->order($this)." ".$query->limit($this);
                        $database->link($this->link)->debug = true;
                        $result = $database->link($this->link)->query ($request);
                        if ($result)
                        {
                            foreach ($result as $row)
                            {
                                $rows[$row[$this->primary->position]] = $row;
                            }
                            $database->set ($this, $query, $rows);
                        }
                    }
                    if (is_array($rows) && count($rows))
                    {
                        $result = array();
                        foreach ($rows as $row)
                        {
                            $result[$row[$this->primary->position]] = $this->create ($row);
                        }
                        return $result;
                    }
                }
                return false;
            }
            public function save (&$object, $event=null)
            {
                if (is_object($object))
                {
                    global $database;
                    if ($event===null || ($event!==event::insert && $event!==event::update))
                    {
                        if ($object->{$this->primary->name})
                        {
                            $event = event::update;
                        }
                        else
                        {
                            $event = event::insert;
                        }
                    }
                    if (($event==event::update && $this->update) || ($event==event::insert && $this->insert))
                    {
                        debug ($object);
                        $set .= '';
                        foreach ($this->fields as &$field)
                        {
                            if (($event==event::update && $field->update) || ($event==event::insert && $field->insert))
                            {
                                if (($event==event::update && $field->primary) || ($field->primary && !$object->{$this->primary->name}))
                                {
                                    continue;
                                }
                                echo $field->name;
                                if ($field->locale && $database->locales())
                                {
                                    foreach ($database->locales() as $locale)
                                    {
                                        $set .= $this->name($field,$locale)."='".string($object->{field(null,$field->name,$locale)})."', ";
                                    }
                                }
                                else if ($field->foreign && $field->enum)
                                {
                                    $set .= $this->name($field)."='".enum($object->{$field->name})."', ";
                                }
                                else if ($field->foreign)
                                {
                                    $set .= $this->name($field)."='".string(id($object->{$field->name}))."', ";
                                }
                                else
                                {
                                    $set .= $this->name($field)."='".string($object->{$field->name})."', ";
                                }
                            }
                        }
                        if ($set!='')
                        {
                            $set = substr ($set, 0, -2);
                            if ($event==event::update)
                            {
                                $query = "update ".$this->name()." set ".$set." where ".$this->name($this->primary)."='".string($object->{$this->primary->name})."' limit 1";
                                if ($database->link($this->link)->query ($query))
                                {
                                    $database->set ($this,$object->{field(null,$field->name,$locale)},null);
                                    return true;
                                }
                            }
                            else
                            {
                                $query = "insert into ".$this->name()." set ".$set;
                                if ($database->link($this->link)->query ($query))
                                {
                                    $object->{field(null,$field->name,$locale)} = $database->link($this->link)->id();
                                    $database->set ($this,$object->{field(null,$field->name,$locale)},null);
                                    return true;
                                }
                            }

                        }
                    }
                    return false;
                }
                else if (is_array($object))
                {
                    $result = true;
                    foreach ($object as &$item)
                    {
                        if (!$this->save($item))
                        {
                            $result = false;
                        }
                    }
                    return $result;
                }
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
                if ($this->columns===null)
                {
                    global $database;
                    $cell = 0;
                    foreach ($this->fields as $field)
                    {
                        if ($field->select)
                        {
                            $field->position = $cell;
                            if ($field->locale && $database->locales())
                            {
                                foreach ($database->locales() as $locale)
                                {
                                    $this->columns .= $this->name($field,$locale).', ';
                                    $cell++;
                                }
                            }
                            else
                            {
                                $this->columns .= $this->name($field).', ';
                                $cell++;
                            }
                            if ($field->enum && $field->foreign)
                            {

                            }
                            else if ($field->foreign)
                            {
                                $table = $database->table ($field->foreign);
                                if ($table->link==$this->link)
                                {
                                    foreach ($table->fields as $foreign)
                                    {
                                        if ($foreign->locale && $database->locales())
                                        {
                                            foreach ($database->locales() as $locale)
                                            {
                                                $this->columns .= $table->name($foreign,$locale).', ';
                                                $cell++;
                                            }
                                        }
                                        else
                                        {
                                            $this->columns .= $table->name($foreign).', ';
                                            $cell++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($this->columns!==null)
                    {
                        $this->columns = substr($this->columns,0,-2);
                    }
                }
                return $this->columns;
            }
            public function tables ()
            {
                if ($this->tables===null)
                {
                    global $database;
                    $this->tables .= $this->name();
                    foreach ($this->fields as $field)
                    {
                        if ($field->foreign && !$field->enum)
                        {
                            $table = $database->table ($field->foreign);
                            if ($table)
                            {
                                $this->tables .= " left join ".$table->name()." on ".$this->name($field)."=".$table->name($table->primary);
                            }
                        }
                    }
                }
                return $this->tables;
            }
            public function hash ()
            {
                if ($this->hash===null)
                {
                    $this->name()."|".$this->fields()."|".$this->tables();
                }
                return $this->hash;
            }
        }

        class database
        {
            /**
             * @var \db\link[]
             */
            private $links = array();
            /**
             * @var \db\table[]
             */
            private $tables = array ();
            /**
             * @param \db\link $link
             */
            public $default = null;
            /**
             * just an array of objects which have property name
             * @var locale[]
             */
            public $locales;
            public $locale;
            public $caches = array();
            /**
             * @param string $default default database name
             * @param \db\link $link default connection to database
             */
            public function __construct ($default=null, link $link=null)
            {
                $this->default = $default;
                if ($link)
                {
                    $this->links[$link->name] = $link;
                    if (!isset($this->links['default']))
                    {
                        $this->links['default'] = &$this->links[$link->name];
                    }
                }
                $this->caches[cache::load] = new load ();
                $this->caches[cache::long] = new long ();
                $this->caches[cache::user] = new user ();
            }
            /**
             * @param \db\table $table
             */
            public function add ($class)
            {
                try
                {
                    $table = new table($class);
                }
                catch (\Exception $error)
                {
                    return;
                }
                if ($table->database===null)
                {
                    $table->database = $this->default;
                }
                if ($table->id[0]=='.')
                {
                    $source = substr($table->id, 1);
                }
                else
                {
                    $source = $table->id;
                }
                $result = explode ('.',$source);
                if (!isset($this->{$result[0]}))
                {
                    $space = &$this;
                    foreach ($result as $item)
                    {
                        if ($item!='' && !isset($space->{$item}))
                        {
                            $space->{$item} = new \stdClass();
                        }
                        $space = &$space->{$item};
                    }
                    $space = $table;
                }
                $this->tables[$table->id] = $table;
            }
            function scan ($prefix)
            {
                $prefix = table::id($prefix,false);
                $result = get_declared_classes ();
                if ($prefix!=null)
                {
                    foreach ($result as $class)
                    {
                        $class = table::id($class);
                        if (strpos($class,$prefix)===0)
                        {
                            $this->add ($class);
                            debug ($class);
                        }
                    }
                }
            }
            public function table ($id)
            {
                return $this->tables[$id];
            }
            public function link ($link)
            {
                if (is_object($link))
                {
                    $this->links[$link->name] = $link;
                    if (!isset($this->links['default']))
                    {
                        $this->links['default'] = &$this->links[$link->name];
                    }
                }
                else
                {
                    return $this->links[$link];
                }
            }
            public function update ($log=null)
            {
                if ($log)
                {
                    $file = $log;
                    $log = '';
                }
                else
                {
                    $file = false;
                }
                $databases = array ();
                foreach ($this->tables as &$table)
                {
                    //debug ("in table ".$table->name);
                    $link = $this->link($table->link);
                    $result = $link->select ("describe ".$table->name());
                    //check if table exists
                    if (!$result && $link->error('42S02'))
                    {
                        //table does not exist
                        //check if database exists
                        if (!isset($databases[$table->link][$table->database]))
                        {
                            //create database as it does not exist
                            $query = "create database if not exists `".$table->database."` default character set utf8 collate utf8_general_ci";
                            $databases[$table->link][$table->database] = true;
                            if ($file)
                            {
                                $log .= $query.";\n";
                            }
                            else
                            {
                                $link->query($query);
                            }
                        }
                        //check if table does not exist
                        //because programmer renamed class
                        //and hinted to rename from old name
                        if ($table->rename)
                        {
                            //okey rename table
                            $from = '';
                            if ($table->database!==null)
                            {
                                $from .= "`".$table->database."`.";
                            }
                            $from .= "`".$table->rename."`";
                            $query = "rename table ".$from." to ".$table->name();
                            if ($file)
                            {
                                $log .= $query.";\n";
                            }
                            else
                            {
                                $link->query($query);
                            }
                        }
                        else
                        {
                            //well we have to create talble at last
                            //as it does not exist and it does not require rename
                            //debug ("in create table ".$table->name);
                            $query = 'create table '.$table->name()." (";
                            foreach ($table->fields as &$field)
                            {
                                if ($field->locale && $this->locales())
                                {
                                    foreach ($this->locales() as $locale)
                                    {
                                        $query .= " ".$table->name($field,$locale,true)." ".$field->type()." ".$field->extra().",";
                                    }
                                }
                                else
                                {
                                    $query .= " ".$table->name($field,true)." ".$field->type()." ".$field->extra().",";
                                }
                            }
                            if ($table->primary)
                            {
                                $query .= "primary key (".$table->name($table->primary,true).")";
                            }
                            $query .= ") engine=".$table->engine." default charset=".$table->charset;
                            if ($file)
                            {
                                $log .= $query.";\n";
                            }
                            else
                            {
                                $link->query($query);
                            }
                        }
                        $result = false;
                    }
                    //damn table update
                    //multilangual fields complicate
                    //everithing
                    //but it alows to:
                    //1. rename field and all its child multilang fields will be renamed
                    //2. localize field add language fields and rename original field for default language field
                    //3. add new language causes to add fields right aftet fields last localized child
                    //4. add field after field
                    //5. natsionaluri modzraoba
                    //6. compare changes and alter changes
                    //7. add new fields
                    if ($result)
                    {
                        if ($table->database!==null)
                        {
                            $query = "show table status from `".$table->database."` where name = '".$table->table."'";
                        }
                        else
                        {
                            $query = "show table status where name = '".$table->table."'";
                        }
                        $info = $link->fetch($query);
                        if ($info)
                        {
                            //debug ($info);
                            if (strtolower($table->engine)!=strtolower($info['Engine']))
                            {
                                $query = "alter table ".$table->name()." engine=".$table->engine;
                                if ($file)
                                {
                                    $log .= $query.";\n";
                                }
                                else
                                {
                                    $link->query($query);
                                }
                            }
                        }

                        //debug ("in modify table ".$table->name);
                        $update = array ();
                        $columns = array ();
                        $insert = array ();
                        $localize = array ();
                        foreach ($result as $row)
                        {
                            $column = array ();
                            $column['name'] = $row['Field'];
                            $column['length'] = null;
                            $column['data'] = null;
                            $column['unsigned'] = false;
                            if (strpos($row['Type'],'('))
                            {
                                $column['data'] = substr($row['Type'], 0, strpos($row['Type'],'('));
                                $column['length'] = substr($row['Type'], strpos($row['Type'],'(')+1, strpos($row['Type'],')')-strpos($row['Type'],'(')-1);
                            }
                            else if (strpos($row['Type'],' '))
                            {
                                $column['data'] = substr($row['Type'], 0, strpos($row['Type'],' ')+1);
                            }
                            else
                            {
                                $column['data'] = $row['Type'];
                            }
                            if (strpos($row['Type'],'zerofill'))
                            {
                                $column['zero'] = true;
                            }
                            else
                            {
                                $column['zero'] = false;
                            }
                            if (strpos($row['Type'],'unsigned'))
                            {
                                $column['unsigned'] = true;
                            }
                            else
                            {
                                $column['unsigned'] = false;
                            }

                            $column['default'] = $row['Default'];
                            if ($row['Null']=='YES')
                            {
                                $column['null'] = true;
                            }
                            else
                            {
                                $column['null'] = false;
                            }
                            $columns[$column['name']] = $column;
                            //debug ($row);
                        }
                        //debug ($columns);
                        foreach ($table->fields as &$field)
                        {
                            $locales = $this->locales();
                            if (!$locales || !$field->locale)
                            {
                                $locales = array (null);
                            }
                            foreach ($locales as $locale)
                            {
                                //debug ($columns[field($table->prefix,$field->column,$locale)]['length']." ".$field->length);
                                if (isset($columns[field($table->prefix,$field->rename,$locale)]))
                                {
                                    $update[$field->name] = &$field;
                                }
                                else if (isset($columns[field($table->prefix,$field->column,$locale)]))
                                {
                                    $field->rename = null;
                                    if ($columns[field($table->prefix,$field->column,$locale)]['data']!=$field->data)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("type mismatch",$field);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['length']!=$field->length)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("length mismatch",$field,$table);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['default']!=null && $columns[field($table->prefix,$field->column,$locale)]['default']!=$field->default)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("default mismatch",$field);
                                    }
                                    else if($columns[field($table->prefix,$field->column,$locale)]['null']!=$field->null)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("null mismatch",$field);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['zero']!=$field->zero)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("zero mismatch",$field);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['unsigned']!=$field->unsigned)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("unsigned mismatch",$field);
                                    }
                                }
                                else if (!isset($columns[field($table->prefix,$field->column,$locale)]))
                                {
                                    if (isset($columns[field($table->prefix,$field->column)]))
                                    {
                                        $field->ignore = $this->locale(true);
                                        $localize[$field->name]  = &$field;
                                    }
                                    echo field($table->prefix,$field->column,$locale)."<br>";
                                    $insert[$field->name] = &$field;
                                }
                            }
                        }
                        ////
                        //debug ($localize);
                        if ($localize)
                        {
                            foreach ($localize as &$field)
                            {
                                $query = "alter table ".$table->name()." change ".$table->name($field,true)." ".$table->name($field,$field->ignore,true)." ".$field->type()." ".$field->extra();
                                //debug ($query);
                                if ($file)
                                {
                                    $log .= $query.";\n";
                                }
                                else
                                {
                                    $link->query($query);
                                }
                            }
                        }
                        //debug ($update);
                        if ($update)
                        {
                            foreach ($update as &$field)
                            {
                                $locales = $this->locales();
                                if (!$locales || !$field->locale)
                                {
                                    $locales = array (null);
                                }
                                foreach ($locales as $locale)
                                {
                                    $query = "alter table ".$table->name()." change ".($field->rename ? ("`".field($table->prefix,$field->rename,$locale)."`") : $table->name($field,$locale,true))." ".$table->name($field,$locale,true)." ".$field->type()." ".$field->extra();
                                    //debug ($query);
                                    if ($file)
                                    {
                                        $log .= $query.";\n";
                                    }
                                    else
                                    {
                                        $link->query($query);
                                    }
                                }
                            }
                        }
                        //debug ($insert);
                        if ($insert)
                        {
                            $last = null;
                            if ($this->locales())
                            {
                                $last = end ($this->locales());
                            }
                            foreach ($insert as &$field)
                            {
                                $locales = $this->locales();
                                if (!$locales || !$field->locale)
                                {
                                    $locales = array (null);
                                }
                                $after = null;
                                $previous = null;
                                if ($field->locale && $this->locales())
                                {
                                    foreach ($this->locales() as $item)
                                    {
                                        if (isset($columns[field($table->prefix,$field->column,$item)]))
                                        {
                                            $previous = $table->name ($field, $item, true);
                                        }
                                    }
                                }
                                foreach ($locales as $locale)
                                {
                                    if ((!isset($field->ignore) && !$locale) || ($field->ignore->name!=$locale->name && !isset($columns[field($table->prefix,$field->column,$locale)])))
                                    {
                                        $query = "alter table ".$table->name()." add ".$table->name($field,$locale,true)." ".$field->type()." ".$field->extra();
                                        if ($field->first)
                                        {
                                            $query .= " first";
                                        }
                                        else if ($field->after && !$previous)
                                        {
                                            if (!$after && isset($table->fields[$field->after]))
                                            {
                                                $after = $table->name($field->after,$last,true);
                                            }
                                            $query .= " after ".$after;
                                            $after = $table->name ($field, $locale, true);
                                        }
                                        else if ($previous)
                                        {
                                            if (!$after)
                                            {
                                                $after = $previous;
                                            }
                                            if ($after)
                                            {
                                                $query .= " after ".$after;
                                                $after = $table->name ($field, $locale, true);
                                            }
                                        }
                                        //debug ($query);
                                        if ($file)
                                        {
                                            $log .= $query.";\n";
                                        }
                                        else
                                        {
                                            $link->query($query);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($file)
                {
                    debug ($log);
                    file_put_contents($file, $log);
                }
            }
            function debug ($name, $field, $table=null)
            {
                return;
                echo "<span style='font-family:\"dejavu sans mono\";font-size:11pt;font-weight:bold;'>"
                .$table->name." ".$name." on field ".$field->name."(".$field->column.")</span>";
                debug ($field);
            }
            public function locales ($locales=null)
            {
                if ($locales!==null)
                {
                    $this->locales = $locales;
                }
                else
                {
                    if (is_array($this->locales) && count($this->locales))
                    {
                        return $this->locales;
                    }
                    return false;
                }
            }
            public function locale ($locale=null)
            {
                if ($locale===true)
                {
                    return reset ($this->locales());
                }
                if (is_object($locale))
                {
                    $this->locale = $locale;
                }
                else
                {
                    if ($this->locale===null && $this->locales())
                    {
                        return reset ($this->locales());
                    }
                    return $this->locale;
                }
            }
            public function cache ($type, $table, $field, $id)
            {

            }
            public function set (table &$table, $query, $value)
            {

            }
            public function get (table &$table, $query)
            {
                return false;
            }
        }

        abstract class value
        {
            public function set ()
            {
            }
            public function get ()
            {
            }
        }

        abstract class entity
        {
            /**
             * primary
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

        class type
        {
            const integer = 1;
            const boolean = 2;
            const float = 3;
            const string = 4;
            const binary = 5;
            const date = 6;
            const time = 7;
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

        class query
        {
            public $type;
            public $where;
            public $order;
            public $limit;
            public $debug = false;
            public function __construct ()
            {
                $this->where = new where ();
                $this->order = new order ();
                $this->limit = new limit ();
            }
            public function where (table &$table)
            {
                return $this->where->result ($table);
            }
            public function order (table &$table)
            {
                return $this->order->result ($table);
            }
            public function limit (table &$table)
            {
                return $this->limit->result ($table);
            }
            public function hash (table &$table)
            {
                $result = array ();
                $result[] = $this->where->result($table);
                $result[] = $this->limit->result($table);
                $source = '';
                foreach ($result as $item)
                {
                    if ($item!==null)
                    {
                        $source .= $item;
                    }
                }
                if ($source!=='')
                {
                    return md5($source);
                }
                return null;
            }
        }

        class where
        {
            /**
             * @var string
             */
            public $string;
            public function result (table &$table)
            {
                if ($this->query!="")
                {
                    return " where ".$this->string." ";
                }
                return "";
            }
        }

        class order
        {
            private $items = array();
            private $field;
            private $method;
            public function __construct ($field=null, $method=null)
            {
                $this->field = $field;
                if (!is_object($method))
                {
                    $method = new method($method);
                }
                $this->method = $method;
            }
            public function add ($field, $method=null)
            {
                if (is_object($field) && $method===null)
                {
                    $this->items[] = $field;
                }
                else
                {
                    $this->items[] = new order ($field, $method);
                }
            }
            public function field ($field)
            {
                $this->field = $field;
            }
            public function method ($method)
            {
                $this->method->name = $method;
            }
            public function result (table &$table)
            {
                global $database;
                if (is_array($this->items) && $this->items)
                {
                    $result = " order by ";
                    foreach ($this->items as $item)
                    {
                        $result .= substr($item->result($table),10).",";
                    }
                    return " ".substr($result,0,-2)." ";
                }
                if ($this->field)
                {
                    return " order by ".$table->name($this->field,$database->locale())." ".$this->method->result($table)." ";
                }
                //return " order by ".(is_object($database->locale()) ? $table->name($this->field,$database->locale()) : $table->name($this->field))." ".$this->method->result($table)." ";
            }
        }

        class method
        {
            const asc = "asc";
            const desc = "desc";
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
                return $this->name;
            }
            public function result (table &$table)
            {
                return $this->name;
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
            public function result (table &$table)
            {
                if ($this->count==null)
                {
                    return "";
                }
                if ($this->from==null)
                {
                    return " limit ".intval($this->count)." ";
                }
                return " limit ".intval($this->from).",".intval($this->count)." ";
            }
        }

        class cache
        {
            const none = 1; //no store
            const load = 2; //store in array
            const user = 3; //store in session [database|]
            const long = 4; //store in apc cache
            public $type;
            public $scope;
            public $table;
            public $query;
            public function __construct ($type, $scope, $table, $query)
            {
                $this->type = $type;
                $this->scope = $scope;
                $this->table = $table;
                $this->query = $query;
            }
        }

        class scope
        {
            const project = 1; //[database|project|[project_name]|]
            const solution = 2; //[database|solution|[solution_name]|]
        }

        class none
        {

        }

        class load
        {

        }

        class user
        {

        }

        class long
        {

        }

        function string ($input)
        {
            return $input;
        }

        function id (&$object)
        {
            if (is_object($object))
            {
                if ($object->id)
                {
                    $id = $object->id;
                }
            }
            else
            {
                $id = $object;
            }
            return intval ($id);
        }

        function field ($prefix, $column, $locale=null)
        {
            if ($column==null)
            {
                return null;
            }
            if ($locale!=null)
            {
                return $prefix.$column."_".$locale->name;
            }
            return $prefix.$column;
        }

        function debug ($input)
        {
            $backtrace = debug_backtrace();
            $result = "<div style=\"font-family:'dejavu sans mono,consolas,monospaced,monospace';font-size:10pt;width:600px;margin-bottom:20px\"><div style='background:#f0f0f0'>";
            foreach ($backtrace as $key => $value)
            {
                $result .= $value['file']." [".$value['line']."] <font color=red>".$value['function']."</font><br>";
            }
            $result .= '</div>';
            $result .= str_replace (array("\n"," ","var","array","class","=&gt;","&nbsp;&nbsp;&nbsp;'","'&nbsp;&nbsp;<b><font color=green>="),array("<br>\n",'&nbsp;&nbsp;',"<b><font color=blue>var</font></b>","<b><font color=red>array</font></b>","<b><font color=green>class</font></b>","<b><font color=green>=</font></b>","&nbsp;&nbsp;&nbsp;<font color=green>'","'</font>&nbsp;&nbsp;<b><font color=green>="), htmlspecialchars (var_export($input,true),ENT_NOQUOTES,'UTF-8'));
            $result .= "</div>";
            echo $result;
        }

    }

?>
