<?php

    namespace core
    {
        class solution
        {
            public $id;
            public $name;
            /**
             * ignore
             * @var project
             */
            public $projects;
            /**
             * ignore
             */
            public function __construct ($name)
            {
                $this->name = $name;
            }
        }

        //the directive 'locale' specifies that the field is localized
        //if locales are defined
        class project
        {
            public $id;
            public $name;
            /**
             * locale
             */
            public $title;
            /**
             * type integer
             * @var \core\solution
             */
            public $solution;
            public function __construct ($name, &$solution)
            {
                $this->name = $name;
                $this->solution = &$solution;
            }
        }


        /**
         * cache long
         */
        class page
        {
            public $id;
            public $name;
            /**
             * enum
             * @var widget
             */
            public $widget;
            public function __construct ($name,$widget=array())
            {
                $this->name = $name;
                $this->widget = $widget;
            }
        }

        /**
         * cache long
         */
        class widget
        {
            public $id;
            public $name;
            public function __construct ($name)
            {
                $this->name = $name;
            }
        }
    }

?>
