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

        class table
        {
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
        }

        class database
        {
            public $link;
            public $tables = array ();
            public function __construct (link $link)
            {
                $this->link = $link;
            }
            public function add ()
            {
            }
            public function table ($name)
            {
                return $this->tables[$name];
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
