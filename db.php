<?php

    namespace db
    {
        const title = 'Db';
        const build = 1;
        const label = 0.9;

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
         * solution
         * project
         */

        /*
         * table sample_foo (use table named sample_foo for this class)
         * database simple_db (table is located in simple_db)
         * link mysql_simpl4 (table is using link mysql_simpl4)
         * prefix foo_ (table field prefix is foo_)
         * order field:[asc|desc] (order name:asc,date,count:desc please avoid space)
         * charset utf8 (table default charset)
         * engine myisam
         * rename oldname (rename table 'oldname' to this current name if exists)
         * cache none|load|user|long (select your cache type)
         * scope project|solution (select your cache scope)
         * unique name (define simple unique index)
         * unique search id, name (define compound unique index)
         * index fast id, name (define compound index)
         * ignore (ignore this t)
         * deny insert|select|update|delete (deny some for this table)
         */

        class foo
        {
            /**
             * required (this field is requrired)
             * field/column some_field (use field named 'some_field' for this property)
             * type integer|boolean|float|text|binary|date|time
             * length 32 (column length)
             * locale (specify if this field is localized)
             * enum (if this field stores same type object list)
             * unsigned (if column unsigned)
             * zerofill (if zerofill for column)
             * default 3 (default value)
             * primary (if this field is primary)
             * rename 'name_old' (rename field 'name_old' to 'name' if exists)
             * first (this column is first)
             * after id (this column goes after property 'id' column)
             * ignore (ignore this field)
             * foreign \db\bar (set up relation to different class object)
             * deny insert|select|update (allow this field in insert qeuery)
             * allow insert|select|update (allow this field in update qeuery)
             * deny insert for user biohazard (coming soon)
             * on insert set date
             * on update set date
             * @var \test\master (define basic type of field or setup relation)
             */
            public $name;

        }

        class bar
        {

        }

        class link
        {
            public $name;
            public $debug = false;
            public $engine;
            /**
             * @var PDO
             */
            public $link;
            public $config = array (\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
            public function __construct ($name=null, $database='mysql:host=127.0.0.1', $username='root', $password='1234', $settings=null)
            {
                if (strpos($database,'mysql:')===0)
                {
                    $this->engine = 'myisam';
                }
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
                            $count = count ($set);
                            if ($count===1)
                            {
                                return new flag($set[0]);
                            }
                            else if ($count===2)
                            {
                                return new flag($set[0],$set[1]);
                            }
                            else if ($count>2)
                            {
                                return new flag($set[0],$set);
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
            public $type;

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
            public $lazy = false;
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
            public $value = false;
            public function __construct (\ReflectionProperty $value)
            {
                if ($value==null || $value->isStatic())
                {
                    throw new \Exception();
                }
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
                        else if ($flag->name=='@var' || $flag->name=='type')
                        {
                            if ($flag->value=='')
                            {
                                throw new \Exception('@var doc comment required for '.$this->name.' property');
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
                            else if ($flag->name!=='type')
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
                                    if ($this->class->isSubclassOf('\db\value'))
                                    {
                                        $this->type = type::string;
                                        $this->value = true;
                                    }
                                    else// ($this->class->isSubclassOf('\db\entity'))
                                    {
                                        if (!$this->enum)
                                        {
                                            if ($this->type==null)
                                            {
                                                $this->type = type::integer;
                                            }
                                            if ($this->data===null)
                                            {
                                                $this->data = 'int';
                                            }
//                                            if ($this->length===null)
//                                            {
//                                                $this->length = 10;
//                                            }
//                                            if ($this->type==type::integer)
//                                            {
//                                                $this->unsigned = true;
//                                            }
                                        }
                                        $this->foreign = type ($flag->value);
                                    }
                                    // else
                                    // {
                                    //     throw new \Exception ('field not needed');
                                    // }
                                }
                            }
                        }
                        elseif ($flag->name=='on')
                        {
                            if ($flag->value[1]=='insert')
                            {
                                if ($flag->value[3]=='date')
                                {
                                    $this->event->insert->action = action::date;
                                }
                                else if ($flag->value[2]=='user')
                                {
                                    $this->event->insert->action = action::user;
                                }
                            }
                            else if ($flag->value[1]=='update')
                            {
                                if ($flag->value[3]=='date')
                                {
                                    $this->event->update->action = action::date;
                                }
                                else if ($flag->value[3]=='user')
                                {
                                    $this->event->update->action = action::user;
                                }
                            }
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
                        elseif ($flag->name=='lazy')
                        {
                            $this->lazy = true;
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
                        if ($flag->name=='type' && $this->data==null)
                        {
                            $this->data = strtolower($flag->value);
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
                if (strtolower($this->default)==='null')
                {
                    $this->null = true;
                }
                else if ($this->null && $this->default===null)
                {
                    $this->default = 'null';
                }
                if ($this->primary)
                {
                    $this->primary();
                }
                if ($this->type==null)
                {
                    $this->type = type::string;
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
                $this->default = null;
                $this->null = false;
                if (!$this->primary && $this->name=='id')
                {
                    $this->type = type::integer;
                    $this->data = 'int';
                    $this->length = 10;
                }
                $this->primary = true;
            }
            public function extra ()
            {
                $result = ' ';
                if ($this->primary && $this->type==type::integer)
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
                    if (strtolower($this->default)==='null')
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
            public $engine;

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
            public $link;
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
             * @param \db\database $database
             * @param type $class
             */
            public function __construct ($class)
            {
                $this->query = new query();
                $this->id = type ($class);
                $this->table = str_replace('.','_',substr($this->id,1));
                if (strripos($this->id,".")!==false)
                {
                    $this->name = substr($class,strripos($this->id,".")+1);
                }
                else
                {
                    $this->name = $this->id;
                }
                $input = new \stdClass();
                $this->class = new \ReflectionClass (str_replace (".", "\\", $this->id));
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
                            $input->order = explode(',', $flag->value);
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
                        if ($field->primary)
                        {
                            $this->primary = &$this->fields[$field->name];
                        }
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
                if (is_array($input->order))
                {
                    if (count($input->order)==1)
                    {
                        if (strpos($input->order[0],':'))
                        {
                            $input->order = explode(':',$input->order[0]);
                            if (isset($this->fields[$input->order[0]]))
                            {
                                $this->query->order->field($input->order[0]);
                                if ($input->order[1]=='asc' || $input->order[1]=='desc')
                                {
                                    $this->query->order->method ($input->order[1]);
                                }
                            }
                            // debug ($this->query->order);
                            // exit;
                        }
                        else if (isset($this->fields[$input->order[0]]))
                        {
                            $this->query->order->field($input->order[0]);
                        }
                    }
                    else
                    {
                        foreach ($input->order as $data)
                        {
                            if (strpos($input->order[0],':'))
                            {
                                $data = explode(':',$data);
                                if (isset($this->fields[$data[0]]))
                                {
                                    $this->query->order->add($data[0], $data[1]);
                                }
                            }
                            else
                            {
                                if (isset($this->fields[$data]))
                                {
                                    $this->query->order->add($data);
                                }
                            }
                        }
                        // debug ($this->query->order);
                        // debug ($this->query->order->result($this));
                        // exit;
                    }
                }
            }
            public function value ($field, $value)
            {
                return $this->field($field)."='".id($value)."'";
            }
            public function field ($name)
            {
                global $database;
                return $this->name($name,$database->locale());
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
            public static function enum ($set,$table=null)
            {
                if (is_array($set))
                {
                    $result = '|';
                    foreach ($set as $item)
                    {
                        if (id($item,$table->primary->name))
                        {
                            $result .= id($item,$table->primary->name).'|';
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
                                    //debug ($table->name);
                                    $object = $table->load ($key);
                                    $result[$object->{$table->primary->name}] = $object;
                                }
                            }
                        }
                        return $result;
                    }
                    return array ();
                }
                return $set;
            }
            public function create ($row, $from=0)
            {
                $database = $this->database();
                $cell = 0;
                if ($from)
                {
                    $cell = $from;
                }
                $result = @$this->class->newInstance();
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
                        else if ($field->value && is_object($field->class))
                        {
                            $result->{$field->name} = @$field->class->newInstance();
                            $result->{$field->name}->set ($row[$cell]);
                            $cell++;
                        }
                        else if ($field->enum && $field->foreign)
                        {
                            if ($field->lazy)
                            {
                                if (strlen($row[$cell])>2)
                                {
                                    $result->{$field->name} = explode ('|',substr($row[$cell],1,-1));;
                                }
                                else
                                {
                                    $result->{$field->name} = array ();
                                }
                            }
                            else
                            {
                                $result->{$field->name} = self::enum($row[$cell],$database->table($field->foreign));
                            }
                            $cell++;
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
                                        if ($row[$cell+1]===null)
                                        {
                                            $result->{$field->name} = $row[$cell];
                                        }
                                        else
                                        {
                                            //debug ($this->name);
                                            $result->{$field->name} = $table->create($row,$cell+1);
                                            if ($result->{$field->name}->{$table->primary->name}===null && $row[$cell]!==null)//***
                                            {
                                                //creating default object from empty warrning error
                                                //raises when using private fields and __set __get
                                                //until now it does not cause malfunction
                                                @$result->{$field->name}->{$table->primary->name} = $row[$cell];//***
                                            }
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
                                        $result->{$field->name} = $table->load($row[$cell]);//***
                                        if ($result->{$field->name}===false)
                                        {
                                            $result->{$field->name} = $row[$cell];//***
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $result->{$field->name} = $row[$cell];
                            }
                            $cell++;
                        }
                        else
                        {

                            if ($row[$cell]!==null && $field->type==type::float)
                            {
                                $result->{$field->name} = (float) $row[$cell];
                            }
                            else if ($row[$cell]!==null && $field->type==type::integer)
                            {
                                $result->{$field->name} = (int) $row[$cell];
                            }
                            else if ($row[$cell]!==null && $field->type==type::boolean)
                            {
                                $result->{$field->name} = (bool) $row[$cell];
                            }
                            else if ($row[$cell]!==null && $field->type==type::date)
                            {
                                $result->{$field->name} = date (null, $row[$cell]);
                            }
                            else if ($row[$cell]!==null && $field->type==type::time)
                            {
                                $result->{$field->name} = time (null, $row[$cell]);
                            }
                            else if ($row[$cell]!==null && $field->type==type::binary)
                            {
                                $result->{$field->name} = base64_encode (null, $row[$cell]);
                            }
                            else
                            {
                                $result->{$field->name} = $row[$cell];
                            }
                            $cell++;
                        }

                    }
                }
                if (method_exists ($result,'create'))
                {
                    $result->create ();
                }
                return $result;
            }
            public function load ($query=null, &$sender=null)
            {
                $debug = debug_backtrace();
//                debug ($this->id);
//                debug ($debug);
                if ($debug>0)
                {
                    $first = false;
                    foreach ($debug as $step)
                    {
                        if (!$first)
                        {
                            $first = true;
                        }
                        else
                        {
                            if (isset($step['class']) && type($step['class']=='.db.table') && $step['function']=='load')
                            {
                                if ($step['object']->id==$this->id)
                                {
                                    return;
                                }
                            }
                        }

                    }
                }
                $database = $this->database();
                if (!$this->select)
                {
                    return false;
                }
                if (is_object($query) && type($query)=='.db.by')
                {
                    $where = $query->result ($this);
                    $query = new query();
                    $query->where = clone $this->query->where;
                    $query->order = clone $this->query->order;
                    $query->limit = clone $this->query->limit;
                    $query->where ($where);
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
                        $database->context->usage->query ++;
                        $request = "select ".$this->fields()." from ".$this->tables()." where ".$this->name($this->primary)."='".string($query)."'";
                        $result = $database->link($this->link)->query ($request);
                        if (!$result)
                        {
                            return false;
                        }
                        $row = $result->fetch();
                        if ($row)
                        {
                            $database->set ($this, string($query), $row);
                        }
                    }
                    else
                    {
                        $database->context->usage->cache ++;
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
                        if (type($query)!='.db.query')
                        {
                            @debug ($query);
                        }
                        $database->context->usage->query ++;
                        $rows = array ();
                        $request = "select ".$this->fields()." from ".$this->tables()." ".$query->where($this)." ".$query->order($this)." ".$query->limit($this);
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
                    else
                    {
                        $database->context->usage->cache ++;
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
                //debug ($event);
                if (is_object($object))
                {
                    $database = $this->database();
                    if ($event===null)
                    {
                        if ($object->{$this->primary->name})
                        {
                            $event = query::update;
                        }
                        else
                        {
                            $event = query::insert;
                        }
                    }
                    if (($event==query::update && $this->update) || ($event==query::insert && $this->insert))
                    {
                        //debug ($object);
                        $set = '';
                        foreach ($this->fields as &$field)
                        {
                            if (($event==query::update && $field->update) || ($event==query::insert && $field->insert))
                            {
                                //debug ($event);
                                if (($event==query::update && $field->primary) || ($field->primary && !$object->{$this->primary->name}))
                                {
                                    continue;
                                }
                                if (($event==query::update && $field->event->update->action==action::date) || ($event==query::insert && $field->event->insert->action==action::date))
                                {
                                    $object->{$field->name} = time(now());
                                    $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                    continue;
                                }
                                //echo $field->name;
                                if ($field->locale && $database->locales())
                                {
                                    if ($object->{$field->name}!==null && $object->{field(null,$field->name,$database->locale())}===null)
                                    {
                                        $object->{field(null,$field->name,$database->locale())} = $object->{$field->name};
                                    }
                                    foreach ($database->locales() as $locale)
                                    {
                                        $set .= $this->name($field,$locale)."='".string($object->{field(null,$field->name,$locale)})."', ";
                                    }
                                }
                                else if ($field->value)
                                {
                                    $set .= $this->name($field)."='".string($object->{$field->name}->get())."', ";
                                }
                                else if ($field->foreign && $field->enum)
                                {
                                    if (!is_array($object->{$field->name}))
                                    {
                                        $object->{$field->name} = array ();
                                    }
                                    $set .= $this->name($field)."='".self::enum($object->{$field->name},$database->table($field->foreign))."', ";
                                }
                                else if ($field->foreign)
                                {
                                    $set .= $this->name($field)."='".id($object->{$field->name},$database->table($field->foreign)->primary->name)."', ";
                                }
                                else
                                {

                                    if ($object->{$field->name}!==null)
                                    {
                                        if ($field->type==type::string)
                                        {
                                            $set .= $this->name($field)."='".id($object->{$field->name})."', ";
                                        }
                                        else if ($field->type==type::integer)
                                        {
                                            if (!is_object($object->{$field->name}))
                                            {
                                                $object->{$field->name} = intval ($object->{$field->name});
                                                $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                            }
                                            else
                                            {
                                                $set .= $this->name($field)."='".intval(id($object->{$field->name}))."', ";
                                            }
                                        }
                                        else if ($field->type==type::float)
                                        {
                                            $object->{$field->name} = floatval ($object->{$field->name});
                                            $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                        }
                                        else if ($field->type==type::boolean)
                                        {
                                            $object->{$field->name} = (bool)($object->{$field->name});
                                            $set .= $this->name($field)."='".intval($object->{$field->name})."', ";
                                        }
                                        else if ($field->type==type::date)
                                        {
                                            $object->{$field->name} = date($object->{$field->name});
                                            $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                        }
                                        else if ($field->type==type::time)
                                        {
                                            $object->{$field->name} = time($object->{$field->name});
                                            $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                        }
                                        else if ($field->type==type::binary)
                                        {
                                            $set .= $this->name($field)."='".base64_encode($object->{$field->name})."', ";
                                        }
                                    }
                                    else if (strtolower($field->default)==='null')
                                    {
                                        $set .= $this->name($field)."=null, ";
                                    }
                                    else
                                    {
                                        $set .= $this->name($field)."='".string($object->{$field->name})."', ";
                                    }
                                    //$set .= $this->name($field)."='".string($object->{$field->name})."', ";
                                }
                            }
                        }
                        if ($set!='')
                        {
                            $set = substr ($set, 0, -2);
                            if ($event==query::update)
                            {
                                $query = "update ".$this->name()." set ".$set." where ".$this->name($this->primary)."='".string($object->{$this->primary->name})."' limit 1";
                                if ($database->link($this->link)->query ($query))
                                {
                                    @$database->set ($this,$object->{$this->primary->name},false);
                                    if ($clear===null)
                                    {
                                        $clear = new query();
                                    }
                                    @$database->set ($this,$clear,false);
                                    return true;
                                }
                            }
                            else
                            {
                                $query = "insert into ".$this->name()." set ".$set;
                                if ($database->link($this->link)->query ($query))
                                {
                                    if ($object->{$this->primary->name}===null)
                                    {
                                        $object->{$this->primary->name} = $database->link($this->link)->id();
                                    }
                                    $database->set ($this,$object->{$this->primary->name},false);
                                    if (!isset($clear) || $clear===null)
                                    {
                                        $clear = new query();
                                    }
                                    @$database->set ($this,$clear,false);
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
            public function reset ($query=null)
            {
                if (is_object($query))
                {
                    $database = $this->database();
                    $database->set ($this, $query, false);
                }
                else
                {
                    $this->columns = null;
                    $this->tables = null;
                    $this->hash = null;
                }
            }
            public function delete ($object)
            {
                if (is_array($object))
                {
                    $result = true;
                    foreach ($object as $item)
                    {
                        if (!$this->delete($item))
                        {
                            $result = false;
                        }
                    }
                    return $result;
                }
                if (is_object($object) && type($object)=='.db.by')
                {
                    $query = new query();
                    $query->where ($object->result($this));
                    $database = $this->database();
                    $request = "delete from ".$this->name()." where ".$query->where->result($this);
                    $result = $database->link($this->link)->query ($request);
                    if ($result)
                    {

                    }
                }
                else
                {
                    $database = $this->database();
                    $request = "delete from ".$this->name()." where ".$this->name($this->primary)."='".id($object,$this->primary->name)."' limit 1";
                    $result = $database->link($this->link)->query ($request);
                    if ($result)
                    {
                        $database->set ($this, id($object,$this->primary->name), false);
                    }
                }
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
                    $database = $this->database();
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
            public function table ()
            {
                return $this->name();
            }
            public function tables ()
            {
                if ($this->tables===null)
                {
                    $database = $this->database();
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
                    $this->hash = md5($this->name()."|".$this->fields()."|".$this->tables());
                }
                return $this->hash;
            }
            public function next ()
            {
                return intval($this->database()->link($this->link)->value("select max(".$this->name($this->primary).") from ".$this->name()))+1;
            }
            public function database ()
            {
                return database::$object;
            }
        }

        class database
        {
            public static $object;
            public $context;
             /**
             * @param string $default default database name
             * @param \db\link $link default connection to database
             */
            public function __construct ($default=null, $link=null, $username=null, $password=null)
            {
                if (!is_object($link) && $link!=null && $username!==null)
                {
                    $database = $link;
                    $hostname = $default;
                    $default = $database;
                    $link = new link ('default', $hostname, $username, $password);
                }
                $this->context = new \stdClass();
                $this->context->links = array ();
                $this->context->tables = array ();
                $this->context->locales = null;
                $this->context->locale = null;
                $this->context->caches = array ();
                $this->context->default = $default;
                $this->context->usage = new \stdClass();
                $this->context->usage->query = 0;
                $this->context->usage->cache = 0;
                $this->context->readonly = false;
                if ($link)
                {
                    $this->context->links[$link->name] = $link;
                }
                $this->context->caches[cache::load] = new load ();
                $this->context->caches[cache::long] = new long ();
                $this->context->caches[cache::user] = new user ();
                self::$object = $this;
            }
            public function __destruct ()
            {
                if ($this->link()->debug)
                {
                    debug ($this->context->usage);
                }
            }
            /**
             * @param \db\table $table
             */
            public function add ($class)
            {
                try
                {
                    $table = new table ($class);
                }
                catch (\Exception $error)
                {
                    return;
                }
                if ($table->database===null)
                {
                    $table->database = $this->context->default;
                }
                if ($table->id[0]=='.')
                {
                    $source = substr($table->id, 1);
                }
                else
                {
                    $source = $table->id;
                }
                if ($table->link===null)
                {
                    $table->link = $this->link()->name;
                }
                if ($table->engine===null && $this->link($table->link))
                {
                    $table->engine = $this->link($table->link)->engine;
                }
                $result = explode ('.',$source);
                if ($result[0]!='context')
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
                    $this->context->tables[$table->id] = $table;
                }
            }
            public function save (&$object, $event=null)
            {
                if (is_array($object))
                {
                    foreach ($object as $item)
                    {
                        $this->save ($item, $event);
                    }
                }
                else
                {
                    $table = $this->table($object);
                    if (!$table)
                    {
                        debug ($object);
                        exit;
                    }
                    $table->save ($object, $event);
                }
                return $object;
            }
            public function scan ($prefix)
            {
                $prefix = type ($prefix);
                $result = get_declared_classes ();
                if ($prefix!=null)
                {
                    foreach ($result as $class)
                    {
                        $class = type ($class);
                        if (strpos($class,$prefix)===0)
                        {
                            $this->add ($class);
                            //debug ($class);
                        }
                    }
                }
            }
            public function table ($id)
            {
                if (is_object($id))
                {
                    return $this->context->tables[type($id)];
                }
                return $this->context->tables[$id];
            }
            /**
             * @param link $link
             * @return link
             */
            public function link ($link=null)
            {
                if ($link===null)
                {
                    return reset($this->context->links);
                }
                if (is_object($link))
                {
                    $this->context->links[$link->name] = $link;
                }
                else
                {
                    return $this->context->links[$link];
                }
            }
            public function readonly ($value=true)
            {
                $this->context->readonly = $value;
            }
            public function update ($log=null)
            {
                if ($this->context->readonly)
                {
                    return;
                }
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
                foreach ($this->context->tables as &$table)
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
                                    else if ($field->length!==null && $columns[field($table->prefix,$field->column,$locale)]['length']!=$field->length)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("length mismatch",$field,$table);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['default']!==null && $columns[field($table->prefix,$field->column,$locale)]['default']!=$field->default)
                                    {
                                        //debug ($columns[field($table->prefix,$field->column,$locale)]);
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
                                    //echo field($table->prefix,$field->column,$locale)."<br>";
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
                                $columns[field($table->prefix,$field->column,$field->ignore)] = &$columns[field($table->prefix,$field->column)];
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
                                $last = @end ($this->locales());
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
                                //i cant really tell you what is going down there
                                //but its really working : D
                                //just as an advice dont code complex parts of frameworks midnights
                                //if you want to remember how it is working
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
            function debug ($value=true)
            {
                if (is_bool($value))
                {
                    if ($value==true)
                    {
                        $this->link()->debug = true;
                    }
                    else
                    {
                        $this->link()->debug = false;
                    }
                }
                // else if ($nam)
                // return;
                // echo "<span style='font-family:\"dejavu sans mono\";font-size:11pt;font-weight:bold;'>"
                // .$table->name." ".$name." on field ".$field->name."(".$field->column.")</span>";
                // debug ($field);
            }
            public function locales ($locales=null)
            {
                if ($locales!==null)
                {
                    $this->context->locales = $locales;
                    foreach ($this->context->tables as &$table)
                    {
                        $table->reset();
                    }
                }
                else
                {
                    if (is_array($this->context->locales) && count($this->context->locales))
                    {
                        return $this->context->locales;
                    }
                    return false;
                }
            }
            public function locale ($locale=null)
            {
                if ($locale===true)
                {
                    return @reset ($this->locales());
                }
                if (is_object($locale))
                {
                    $this->context->locale = $locale;
                }
                else
                {
                    if ($this->context->locale===null && $this->locales())
                    {
                        return @reset ($this->locales());
                    }
                    return $this->context->locale;
                }
            }
            public function set (table &$table, $query, $value)
            {
                $scope = null;
                $cache = null;
                if (is_object($query))
                {
                    if ($query->cache==null)
                    {
                        $cache = $table->cache;
                    }
                    else
                    {
                        $cache = $query->cache;
                    }
                    if ($query->scope==null)
                    {
                        $scope = $table->scope;
                    }
                    else
                    {
                        $scope = $table->scope;
                    }
                }
                else
                {
                    $cache = $table->cache;
                    $scope = $table->scope;
                }
                if ($cache==cache::load || $cache==cache::long || $cache==cache::user)
                {
                    $this->context->caches[$cache]->set ($scope, $table, $query, $value);
                }
            }
            public function get (table &$table, $query)
            {
                $scope = null;
                $cache = null;
                if (is_object($query))
                {
                    if ($query->cache==null)
                    {
                        $cache = $table->cache;
                    }
                    else
                    {
                        $cache = $query->cache;
                    }
                    if ($query->scope==null)
                    {
                        $scope = $table->scope;
                    }
                    else
                    {
                        $scope = $table->scope;
                    }
                }
                else
                {
                    $cache = $table->cache;
                    $scope = $table->scope;
                }
                if ($cache==cache::load || $cache==cache::long || $cache==cache::user)
                {
                    return $this->context->caches[$cache]->get ($scope, $table, $query);
                }
                return false;
            }
            public function base (&$object)
            {
                if (is_string($object))
                {
                    return $this->{before('\\',$object)};
                }
                return $this->{before('\\',get_class($object))};
            }
        }

        abstract class value
        {
            public function set ($value)
            {

            }
            public function get ()
            {
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
            const select = 1;
            const insert = 2;
            const update = 3;
            const delete = 4;
            public $cache;
            public $scope;
            /**
             * @var where
             */
            public $where;
            /**
             * @var order
             */
            public $order;
            /**
             * @var limit
             */
            public $limit;
            public $debug = false;
            public function __construct ()
            {
                $this->where = new where ();
                $this->order = new order ();
                $this->limit = new limit ();
            }
            public function where ($table)
            {
                if (is_object($table))
                {
                    if (is_object($this->where))
                    {
                        return $this->where->result ($table);
                    }
                    return $this->where;
                }
                if (is_object($this->where))
                {
                    $this->where->string = $table;
                }
                else
                {
                    $this->where = $table;
                }
            }
            public function order ($table, $method=null)
            {
                if (is_object($table))
                {
                    return $this->order->result ($table);
                }
                $this->order->method($method);
                $this->order->field ($table);
            }
            public function limit ($table, $count=null)
            {
                if (is_object($table))
                {
                    return $this->limit->result ($table);
                }
                if ($count===null)
                {
                    $this->limit->count = $table;
                }
                else
                {
                    $this->limit->from = $table;
                    $this->limit->count = $count;
                }

            }
            public function hash ($table)
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
            public function result ($table)
            {
                if ($this->string!='')
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
                    $database = $table->database();
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

        abstract class cache
        {
            const none = 1; //no store
            const load = 2; //store in array
            const user = 3; //store in session [database|]
            const long = 4; //store in apc cache
            public $store = array ();
            public function __construct ()
            {

            }
            function set ($scope, &$table, $query, $value)
            {
                $path = 'database|'.scope($scope).$table->hash();
                if (is_object($query))
                {
                    if (is_array($value))
                    {
                        $hash = $query->hash($table);
                        foreach ($value as $item)
                        {
                            $this->store ($path.'|entry|'.$item[$table->primary->position], $item);
                            $about = $this->fetch ($path.'|about|'.$item[$table->primary->position]);
                            if (!is_array($about))
                            {
                                $about = array ();
                            }
                            $about[$path.'|query|'.$hash] = true;
                            $this->store ($path.'|about|'.$item[$table->primary->position], $about);
                        }
                        $this->store ($path.'|query|'.$hash, $value);
                    }
                }
                else
                {
                    if (is_bool($value))
                    {
                        $about = $this->fetch ($path.'|about|'.$query);
                        if (is_array($about))
                        {
                            foreach ($about as $key => $temp)
                            {
                                $this->store ($key, false);
                                unset ($about[$key]);
                            }
                            $this->store($path.'|about|'.$query, $about);
                        }
                    }
                    $this->store ($path.'|entry|'.$query, $value);
                }
            }
            function get ($scope, &$table, $query)
            {
                $path = 'database|'.scope($scope).$table->hash();
                if (is_object($query))
                {
                    return $this->fetch ($path.'|query|'.$query->hash($table));
                }
                return $this->fetch ($path.'|entry|'.$query);
            }
            function count ()
            {
                return $this->count;
            }
            function store ($name, $value)
            {
                $this->store [$name] = $value;
            }
            function fetch ($name)
            {
                return $this->store[$name];
            }
            function clear ()
            {
                $this->store = array ();
            }
        }

        class scope
        {
            const project = 1; //[database|project|[project_name]|]
            const solution = 2; //[database|solution|[solution_name]|]
        }

        class load extends cache
        {

        }

        class user extends cache
        {
            public $store = array ();
            public function __construct ()
            {
                if (isset($_SESSION))
                {
                    if (!isset($_SESSION['database']))
                    {
                        $_SESSION['database'] = array ();
                    }
                    $this->store = &$_SESSION['database'];
                }
            }

        }

        class long extends cache
        {
            function store ($name, $value)
            {
                if (is_bool($value))
                {
                    apc_delete ($name);
                }
                else
                {
                    apc_store ($name, $value);
                }
                // debug ($name);
                // debug ($value);
                //exit;
            }
            function fetch ($name)
            {
                return apc_fetch ($name);
            }
            function clear ()
            {
                apc_clear_cache ('user');
            }
        }

        class locale
        {
            public $id;
            public $name;
            public $order;
            public function __construct ($name)
            {
                $this->name = $name;
            }
        }

        //$pages->load (by('name','home')->and('name',more,'dad'));

        class by
        {
            private $items = array();
            public function by ($field, $value)
            {
                $this->items[$field] = $value;
                return $this;
            }
            public function result (&$table)
            {
                global $database;
                if (is_object($table) && $this->items)
                {
                    $result = '';
                    foreach ($this->items as $field=>$value)
                    {
                        $primary = null;
                        if (is_object($value))
                        {
                            $parent = $database->table(\db\type($value));
                            if ($parent)
                            {
                                $primary = $parent->primary->name;
                            }
                        }
                        $result .= $table->name($field,$database->locale())."='".id($value,$primary)."' and ";
                    }
                    if ($result!=='')
                    {
                        return substr($result, 0, -5);
                    }
                }
            }
        }

        function by ($field, $value)
        {
            $object = new by ();
            return $object->by ($field,$value);
        }

        function string ($input)
        {
            if (is_object($input) || is_array($input))
            {
                debug ($input);
            }
            if (!get_magic_quotes_gpc())
            {
                return addslashes($input);
            }
            return $input;
        }

        function id (&$object,$field='id')
        {
            if (is_object($object))
            {
                if (is_object($field))
                {
                    $from = $field->primary->name;
                }
                else
                {
                    $from = $field;
                }
                if (isset($object->{$from}))
                {
                    $id = $object->{$from};
                }
                else
                {
                    foreach ($object as $value)
                    {
                        $id = $value;
                        break;
                    }
                }
            }
            else
            {
                $id = $object;
            }
            return string ($id);
        }

        /**
         * @return string get id for class
         * @param string $class
         */
        function type ($input)
        {
            if (is_object($input))
            {
                // $reflection = new \ReflectionClass ($input);
                // $class = $reflection->getName();
                $class = get_class ($input);
            }
            else
            {
                $class = $input;
            }
            if ($class==null)
            {
                return null;
            }
            $class = str_replace ("\\", ".", $class);
            if ($class[0]!='.')
            {
                $class = '.'.$class;
            }
            return $class;
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

        function date ($destroy=null, $restore=null)
        {
            //debug ("des ".$destroy." res ".$restore);
            $user = 0;
            //$zone = intval(\date('Z'));
            $zone = 0;
            if (isset($GLOBALS['system']->user->zone))
            {
                $user = intval(strval($GLOBALS['system']->user->zone));
            }
            if ($restore!==null)
            {
                $restore = strtotime ($restore);
                if ($restore===false)
                {
                    return '0000-00-00';
                }
                if ($user)
                {
                    $zone = $user;
                }
                return \date('Y-m-d',$restore+$zone);
            }
            if ($destroy===null)
            {
                return \date('Y-m-d H:i:s',\time()-$zone);
            }
            $destroy = strtotime ($destroy);
            if (!$destroy)
            {
                return '0000-00-00';
            }
            if ($user)
            {
                $zone = $user;
            }
            return \date('Y-m-d',$destroy-$zone);
        }

        //პარამეტრის გარეშე აბრუნებს მიმდინარე უნვერსალური დროს
        //პირველი პარამეტრის შემთხვევაში აკონვერტირებს მითითებულ დროს უნვერსალური დროში
        //მეორე პარამეტრის შემთხვევაში აკონვერტირებს მითითებულ დროს უნვერსალური დროდან
        function time ($destroy=null, $restore=null)
        {
            $user = 0;
            $zone = intval(\date('Z'));
            if (isset($GLOBALS['system']->user->zone))
            {
                $user = intval(strval($GLOBALS['system']->user->zone));
            }
            if ($restore!==null)
            {
                $restore = strtotime ($restore);
                if ($restore===false)
                {
                    return '0000-00-00 00:00:00';
                }
                if ($user)
                {
                    $zone = $user;
                }
                return \date('Y-m-d H:i:s',$restore+$zone);
            }
            if ($destroy===null)
            {
                return \date('Y-m-d H:i:s',\time()-$zone);
            }
            $destroy = strtotime ($destroy);
            if (!$destroy)
            {
                return '0000-00-00 00:00:00';
            }
            if ($user)
            {
                $zone = $user;
            }
            return \date('Y-m-d H:i:s',$destroy-$zone);
        }

        function now ($date=null)
        {
            if ($date!==null)
            {
                return \date ('Y-m-d H:i:s',$date);
            }
            return \date ('Y-m-d H:i:s');
        }

        function debug ($input)
        {
            $backtrace = debug_backtrace();
            $result = "<div style=\"font-family:'dejavu sans mono','consolas','monospaced','monospace';font-size:10pt;width:600px;margin-bottom:20px;margin-left:20px;background:#fafafa\"><div style='background:#f0f0f0'>";
            foreach ($backtrace as $key => $value)
            {
                $result .= $value['file']." [".$value['line']."] <font color=red>".$value['function']."</font><br>";
            }
            $result .= '</div>';
            if (is_string($input))
            {
                $result .= color ($input);
            }
            else
            {
                $result .= str_replace (array("\\'","\n"," ","var","array","class","=&gt;","&nbsp;&nbsp;&nbsp;'","'&nbsp;&nbsp;<b><font color=green>="),array("'","<br>\n",'&nbsp;&nbsp;',"<b><font color=blue>var</font></b>","<b><font color=red>array</font></b>","<b><font color=green>class</font></b>","<b><font color=green>=</font></b>","&nbsp;&nbsp;&nbsp;<font color=green>'","'</font>&nbsp;&nbsp;<b><font color=green>="), htmlspecialchars (var_export($input,true),ENT_NOQUOTES,'UTF-8'));
            }
            $result .= "</div>";
            echo $result;
        }

        function scope ($scope)
        {
            global $system;
            if ($scope==scope::project)
            {
                if (isset($system->solution->path) && isset($system->project->name))
                {
                    return md5($system->solution->path.'|'.$system->project->name)."|";
                }
            }
            if ($scope==scope::solution)
            {
                if (isset($system->solution->name))
                {
                    return md5($system->solution->path.'|')."|";
                }
            }
        }

        function cache ($table, $object, $delete=false)
        {
            global $database;
            if (is_object($object))
            {
                if (!$database->save ($object))
                {
                    return;
                }
                $id = \db\id($object,$table);
                $flush = true;
            }
            else if ($clean)
            {
                if ($table->delete($object))
                {
                    apc_delete (id($object,$table));
                }
                return;
            }
            else
            {
                $id = id ($object,$table);
            }
            if (!$id)
            {
                return;
            }
            $key = 'database|'.$table->class->getName().'|'.$id;
            if ($flush || !apc_exists($key))
            {
                $result = $table->load($id);
                if (!$result)
                {
                    apc_delete ($key);
                    return $id;
                }
                apc_store ($key, $result);
                return $result;
            }
            else if (apc_exists($key))
            {
                return apc_fetch ($key);
            }
            return $id;
        }

        function color ($query)
        {
            return str_replace(
            array(
            '`',
            '.',
            'show ',
            'describe ',
            'select ',
            'from ',
            'left join',
            'insert ',
            'update ',
            'delete ',
            'where ',
            'order by ',
            'like ',
            'limit ',
            'group by ',
            'and ',
            'or ',
            ' asc',
            ' desc',
            ' on '),
            array(
            '<b>`</b>',
            '<b>.</b>',
            '<span style="color:green;font-weight:bold">SHOW </span>',
            '<span style="color:green;font-weight:bold">DESCRIBE </span>',
            '<span style="color:green;font-weight:bold">SELECT </span>',
            '<span style="color:brown;font-weight:bold"><br>FROM </span>',
            '<span style="color:brown;font-weight:bold"><br>LEFT JOIN</span>',
            '<span style="color:green;font-weight:bold">INSERT </span>',
            '<span style="color:green;font-weight:bold">UPDATE </span>',
            '<span style="color:green;font-weight:bold">DELETE </span>',
            '<span style="color:blue;font-weight:bold"><br>WHERE </span>',
            '<span style="color:red;font-weight:bold"><br>ORDER BY </span>',
            '<span style="color:brown;font-weight:bold">LIKE </span>',
            '<span style="color:red;font-weight:bold"><br>LIMIT </span>',
            '<span style="color:red;font-weight:bold"><br>GROUP BY </span>',
            '<b>AND </b>',
            '</b>OR </b>',
            '<b>ASC</b>',
            '<b>DESC</b>',
            '<span style="color:brown;font-weight:bold"> ON </span>',
            ),$query);
        }
    }

?>
