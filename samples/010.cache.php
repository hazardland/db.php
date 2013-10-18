<?php

    //here we will discuss cache
    //engine cache is affected by 2 parameterers - cache type and scope
    //cache types are :
    // \db\cache::none - default no cache use
    // \db\cache::load - cache data per script load
    // \db\cache::user - cache data in user session until session is alive
    // \db\cache::long - long cache, default implementation uses apc_cache to store data
    //
    // cache scope options are
    // \db\scope:project - cache data in solution.project so data will not mess
    //                     if there are other projects and solutions with simillar names
    // \db\scope:solution - cache data in solution scope
    // project and solution concepts are base concepts of blackhole framework
    // wich is indipendet framework from db framework

    include './000.config.php';

    include '../db.php';

//    class solution extends \db\entity
//    {
//        public $id;
//        public $name;
//        /**
//         * ignore
//         * @var project
//         */
//        public $project;
//        /**
//         * ignore
//         */
//        public $pages;
//        /**
//         * ignore
//         */
//        public $projects;
//        public function __construct ($name)
//        {
//            $this->name = $name;
//        }
//    }
//
//    class project extends \db\entity
//    {
//        public $id;
//        public $name;
//        /**
//         * @var solution
//         */
//        public $solution;
//        public function __construct ($name)
//        {
//            $this->name = $name;
//        }
//    }


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
    class widget extends \db\entity
    {
        public $id;
        public $name;
        public function __construct ($name)
        {
            $this->name = $name;
        }
    }


    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    $database->add('page');
    $database->add('widget');

    $database->update();

    $widgets = $database->widget->load ();
    if (!$widgets)
    {
        $widgets = array (new widget('register'),new widget('profile'),new widget('menu'),new widget('password'),new widget('news'),new widget('gallery'));
        $database->widget->save ($widgets);
    }

    $pages = $database->page->load ();
    if (!$pages)
    {
        $database->page->save (new page ('home',array($widgets[0],$widgets[3],$widgets[2])));
        $database->page->save (new page ('account',array($widgets[1],$widgets[4],$widgets[5])));
        $database->page->save (new page ('service',array($widgets[3],$widgets[2],$widgets[1])));
        $database->page->save (new page ('about',array($widgets[2],$widgets[4],$widgets[0])));
    }

    //after first populate session this code will never run select queries
    //once it selects it caches for long pages and widgets

    \db\debug ($pages);
    \db\debug ($database->context->usage);
?>
