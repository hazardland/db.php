<?php


        class database
        {
            private $context;
             /**
             * @param string $default default database name
             * @param \db\link $link default connection to database
             */
            public function __construct ($default=null, link $link=null)
            {
                $this->context = new \stdClass();
                $this->context->links = array ();
                $this->context->tables = array ();
                $this->context->locales = null;
                $this->context->locale = null;
                $this->context->caches = array ();
                $this->context->default = $default;
                if ($link)
                {
                    $this->context->links[$link->name] = $link;
                    if (!isset($this->context->links['default']))
                    {
                        $this->context->links['default'] = &$this->context->links[$link->name];
                    }
                }
                $this->context->caches[cache::load] = new load ();
                $this->context->caches[cache::long] = new long ();
                $this->context->caches[cache::user] = new user ();
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
                $this->context->tables[$table->id] = $table;
            }
            function scan ($prefix)
            {
                $prefix = table($prefix,false);
                $result = get_declared_classes ();
                if ($prefix!=null)
                {
                    foreach ($result as $class)
                    {
                        $class = table($class);
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
                return $this->context->tables[$id];
            }
            public function link ($link)
            {
                if (is_object($link))
                {
                    $this->context->links[$link->name] = $link;
                    if (!isset($this->context->links['default']))
                    {
                        $this->context->links['default'] = &$this->context->links[$link->name];
                    }
                }
                else
                {
                    return $this->context->links[$link];
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
                    $this->context->locales = $locales;
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
                    return reset ($this->locales());
                }
                if (is_object($locale))
                {
                    $this->context->locale = $locale;
                }
                else
                {
                    if ($this->context->locale===null && $this->locales())
                    {
                        return reset ($this->locales());
                    }
                    return $this->context->locale;
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
?>
