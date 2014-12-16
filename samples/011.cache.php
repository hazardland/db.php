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
    // project and solution concepts are base concepts of system.php framework
    // wich is indipendet framework from db framework

    include './000.config.php';

    include '../db.php';

//    class solution
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
//    class project
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
         * @var part
         */
        public $part;
        public function __construct ($name,$part=array())
        {
            $this->name = $name;
            $this->part = $part;
        }
    }

    /**
     * cache long
     */
    class part
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
    $database->add('part');

    $database->update();

    $parts = $database->part->load ();
    if (!$parts)
    {
        $parts = array (new part('register'),new part('profile'),new part('menu'),new part('password'),new part('news'),new part('gallery'));
        $database->part->save ($parts);
    }

    $pages = $database->page->load ();
    if (!$pages)
    {
        $database->page->save (new page ('home',array($parts[0],$parts[3],$parts[2])));
        $database->page->save (new page ('account',array($parts[1],$parts[4],$parts[5])));
        $database->page->save (new page ('service',array($parts[3],$parts[2],$parts[1])));
        $database->page->save (new page ('about',array($parts[2],$parts[4],$parts[0])));
    }

    //after first populate session this code will never run select queries
    //once it selects it caches for long pages and parts

    \db\debug ($pages);
    \db\debug ($database->context->usage);
?>
