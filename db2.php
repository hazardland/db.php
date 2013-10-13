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
         * charset utf8
         * engine myisam
         * rename oldname
         * unique name
         * unique search id,name
         * index fast id,name
         * ignore
         */
        class foo extends \db\entity
        {
            /**
             *required
             * field/column users_bio
             * type integer|boolean|float|text|binary|date
             * data
             * length 32
             * locale
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

        class bar extends \db\entity
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
                        $debug .= $query."\n".$error[2];
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
                        $debug .= $query."\n".$error[2];
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
                        $debug .= $query."\n".$error[2];
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
                        $debug .= $query."\n".$error[2];
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
                                return new \db\flag($set[0]);
                            }
                            else
                            {
                                return new \db\flag($set[0],$set[1]);
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
                                        $this->unsigned = true;
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
                        elseif ($flag->locale=='locale')
                        {
                            $this->locale = true;
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
                    $this->length = 128;
                }
                if ($this->data=='char' && $this->length!=null)
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
            /**
             * @var \db\field
             */
            public $primary;
            /**
             * @var query
             */
            private $select;
            private $from;
            public $query;
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
                    }
                }
                foreach ($this->class->getProperties() as $value)
                {
                    /* @var $value \ReflectionProperty */
                    try
                    {
                        $field = new \db\field($value);
                        $this->fields[$field->name] = $field;
                    }
                    catch (\Exception $fail)
                    {

                    }
                    //$type = new \db\type($value);
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
            public function name ($field=null, $full=true)
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
                    if (!is_object($field))
                    {
                        $field = $this->fields[$field];
                    }
                    if (!is_object($full) && $full)
                    {
                        return $this->name().".`".$this->prefix.$field->column."`";
                    }
                    else if (is_object($full) && isset($full->name))
                    {
                        return $this->name().".`".$this->prefix.$field->column."_".$full->name."`";
                    }
                    else
                    {
                        return "`".$this->prefix.$field->column."`";
                    }
                }
            }
            public function enum ($set)
            {
                return $set;
            }
            public function field ($name)
            {

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
                    if ($field->locale && $database->locales && is_array($field))
                    {
                        foreach ($database->locales as $locale)
                        {
                            $result->{$field->name."_".$locale->name} = $row[$cell];
                            $cell++;
                        }
                        $result->{$field->name} = &$result->{$field->name."_".$database->locale->name};
                    }
                    else
                    {
                        $result->{$field->name} = $row[$cell];
                        $cell++;
                    }
                    if ($field->enum && $field->foreign)
                    {
                        $result->{$field->name} = $this->enum($row[$cell]);
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
                                        if ($foreign->locale && $database->locales && is_array($database->locales))
                                        {
                                            $cell += count($database->locales);
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
                return $result;
            }
            public function load ($query, $single=false)
            {
                global $database;
                if (!is_object($query))
                {
                    $single = true;
                    $request = "select ".$this->fields()." from ".$this->tables()." where ".$this->name($this->primary)."='".query::string($query)."'";
                    debug ($request);
                    $database->links[$this->link]->debug = true;
                    $result = $database->links[$this->link]->query ($request);
                }
                else
                {

                }
                if (!$result)
                {
                    return false;
                }
                if ($single)
                {
                    $row = $result->fetch();
                    return $this->create ($row);
                }
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
                if ($this->select===null)
                {
                    global $database;
                    foreach ($this->fields as $field)
                    {
                        if ($field->locale && $database->locales && is_array($database->locales))
                        {
                            foreach ($database->locales as $locale)
                            {
                                $this->select .= $this->name($field,$locale).', ';
                            }
                        }
                        else
                        {
                            $this->select .= $this->name($field).', ';
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
                                    if ($foreign->locale && $database->locales && is_array($database->locales))
                                    {
                                        foreach ($database->locales as $locale)
                                        {
                                            $this->select .= $table->name($foreign,$locale).', ';
                                        }
                                    }
                                    else
                                    {
                                        $this->select .= $table->name($foreign).', ';
                                    }
                                }
                            }
                        }
                    }
                    if ($this->select!==null)
                    {
                        $this->select = substr($this->select,0,-2);
                    }
                }
                return $this->select;
            }
            public function tables ()
            {
                if ($this->from===null)
                {
                    global $database;
                    $this->from .= $this->name();
                    foreach ($this->fields as $field)
                    {
                        if ($field->foreign)
                        {
                            $table = $database->table ($field->foreign);
                            if ($table)
                            {
                                $this->from .= " left join ".$table->name()." on ".$this->name($field)."=".$table->name($table->primary);
                            }
                        }
                    }
                }
                return $this->from;
            }
        }

        class database
        {
            /**
             * @var \db\link[]
             */
            public $links = array();
            /**
             * @var \db\table[]
             */
            public $tables = array ();
            /**
             * @param \db\link $link
             */
            public $default = null;
            /**
             * just an array of objects which have property name
             * @var locale[]
             */
            public $locales;
            /**
             * @param string $default default database name
             * @param \db\link $link default connection to database
             */
            public function __construct ($default=null, \db\link $link=null)
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
                            debug ($class);
                        }
                    }
                }
            }
            public function table ($id)
            {
                return $this->tables[$id];
            }
            public function link (\db\link $link)
            {
                $this->links[$link->name] = $link;
                if (!isset($this->links['default']))
                {
                    $this->links['default'] = &$this->links[$link->name];
                }
            }
            public function update ($log=null)
            {
                if ($log)
                {
                    $file = $log;
                    $log = '';
                }
                $databases = array ();
                foreach ($this->tables as &$table)
                {
                    //debug ("in table ".$table->name);
                    $link = $this->links[$table->link];
                    $result = $link->select ("describe ".$table->name());

                    if (!$result && $link->error('42S02'))
                    {
                        if (!isset($databases[$table->link][$table->database]))
                        {
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
                        if ($table->rename)
                        {
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
                            //debug ("in create table ".$table->name);
                            $query = 'create table '.$table->name()." (";
                            foreach ($table->fields as &$field)
                            {
                                $query .= " ".$table->name($field,false)." ".$field->type()." ".$field->extra().",";
                            }
                            if ($table->primary)
                            {
                                $query .= "primary key (".$table->name($table->primary,false).")";
                            }
                            //$query = substr($query, 0, -1);
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
                            $columns[$column[name]] = $column;
                            //debug ($row);
                        }
                        //debug ($columns);

                        foreach ($table->fields as &$field)
                        {
                            if (isset($columns[$field->rename]))
                            {
                                $update[] = &$field;
                            }
                            else if (isset($columns[$field->column]))
                            {
                                if ($columns[$field->column]['data']!=$field->data)
                                {
                                    $update[] = &$field;
                                    $this->debug ("type mismatch",$field);
                                }
                                else if ($columns[$field->column]['length']!=$field->length)
                                {
                                    $update[] = &$field;
                                    $this->debug ("length mismatch",$field,$table);
                                }
                                else if ($columns[$field->column]['default']!=null && $columns[$field->column]['default']!=$field->default)
                                {
                                    $update[] = &$field;
                                    $this->debug ("default mismatch",$field);
                                }
                                else if($columns[$field->column]['null']!=$field->null)
                                {
                                    $update[] = &$field;
                                    $this->debug ("null mismatch",$field);
                                }
                                else if ($columns[$field->column]['zero']!=$field->zero)
                                {
                                    $update[] = &$field;
                                    $this->debug ("zero mismatch",$field);
                                }
                                else if ($columns[$field->column]['unsigned']!=$field->unsigned)
                                {
                                    $update[] = &$field;
                                    $this->debug ("unsigned mismatch",$field);
                                }
                            }
                            else if (!isset($columns[$field->column]))
                            {
                                $insert[] = &$field;
                            }
                        }
                        if ($update)
                        {
                            foreach ($update as &$field)
                            {
                                $query = "alter table ".$table->name()." change ".($field->rename ? ("`".$table->prefix.$field->rename."`") : $table->name($field,false))." ".$table->name($field,false)." ".$field->type()." ".$field->extra();
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
                        if ($insert)
                        {
                            foreach ($insert as &$field)
                            {
                                $query = "alter table ".$table->name()." add ".$table->name($field,false)." ".$field->type()." ".$field->extra();
                                if ($field->first)
                                {
                                    $query .= " first";
                                }
                                else if ($field->after)
                                {
                                    if (isset($table->fields[$field->after]))
                                    {
                                        //debug ($table->fields[$field->after]);
                                        $query .= " after ".$table->name($field->after,false);
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

        class index
        {
            const primary = 1;
            const key = 2;
            const unique = 3;
            const text = 4;
            public $name;
            public $type = self::index;
            public $fields;
            public function __construct ($name, $type, $fields=array())
            {
                $this->name = $name;
                $this->type = $type;
                $this->fields = $fields;
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
            const time = 6;
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
            /**
            * @var \db\where
            */
            public $where;
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
            public function string ($input)
            {
                return $input;
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
                    return " where ".$this->query." ";
                }
                return "";
            }
        }

        class order
        {
            public $items = array();
            public $field;
            public $method;
            public function __construct ($field=null, $method=null)
            {
                $this->field = $field;
                $this->method = $method;
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
                return $this->field;
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
                    $result = " order by ";
                    foreach ($this->items as $item)
                    {
                        $result .= substr($item,10).",";
                    }
                    return " "+substr($item,0,-1)." ";
                }
                return " order by ".$this->field." ".$this->method." ";
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
                    return " limit ".intval($this->count)." ";
                }
                return " limit ".intval($this->from).",".intval($this->count)." ";
            }
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
    }

?>
