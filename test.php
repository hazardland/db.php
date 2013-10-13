<?php

    include 'd:/web/lib/util.php';
    include 'd:/web/www/_db/db2.php';

//    $link = new \db\link ('mysql','mysql:host=127.0.0.1','root','1234');
//    foreach ($link->select ("select site_blank.`users_groups`.`group_id`,site_blank.`users_groups`.`group_name`,site_dating.`users_groups`.`group_name` from site_blank.users_groups left join site_dating.users_groups on site_blank.users_groups.group_id=site_dating.users_groups.group_id") as $row)
//    {
//        debug ($row);
//    }
//    debug ($link->error());
//
//    $result = $link->value ("select group_name from site_blank.users_groups");
//    debug ($result);
//
//    $class = new \ReflectionClass('\\db\\foo');
//    $property = $class->getProperty ('name');
//
//    $result = \db\flag::set ($property);
//    debug ($result);


//    $table = new \db\table ('user');
//    debug ($table);

    /**
     * charset utf8
     * engine myisam
     * unique name
     * unique search id,name
     * index fast id,name
     * engine myisam
     */
    class user extends \db\entity
    {
        /**
         * @var int
         * length 10
         * unsigned
         */
        public $id;
        /**
         * @var string
         * rename name
         * ignore
         */
        public $login;
        /**
         * @var group
         */
        public $group;
        /**
         * after id
         * @var string
         */
        public $name;
        /**
         * @var float
         */
        public $mobile;
    }

    /**
     */
    class group extends \db\entity
    {
        /**
         * length 11
         * primary
         * default 1
         * first
         * after id
         * @var int
         */
        public $id;
        public $name;
        public $caption;
    }

    $database = new \db\database ('blackhole_blank');
    $database->link (new \db\link ('mysql','mysql:host=127.0.0.1','root','1234'));
    $database->add ('user');
    $database->add ('group');
    $database->update ('./db.log'); //creates all the tables and fields automatically or affects changes

    //debug ($database->group);

//    $user = new user();
//    $user->name = 'biohazard';
//    $user = $database->user->save ($user);
//
//    $user = $database->user->load (1);

//    $config->path = '100';
//    $config->system->path = '200';
//    echo $config->path;
//    echo $config->system->path;
//
//    include 'd:/web/lib/db.php';
//    include 'd:/web/lib/core.php';
//
//    echo \core\file::type ('menu.php');

//    class test
//    {
//        public function __get ($name)
//        {
//            return "bio";
//        }
//    }
//
//    $test = new test ();
//    $test->hi;
//    echo $test->hi;

//    $a = '1,2,3';
//    $b = 'a,b,c,d';
//    $result = explode(',',$a);
//    $result += explode (',',$b);
//    var_export ($result);
//
//    $result = array ();
//    $result['a[bc]'] = 'it works';
//    $result['abc'] = 'it works not';
//
//    echo $result['a[bc]'];

//    class test
//    {
//        public function __construct()
//        {
//            var_export(func_get_args());
//        }
//    }
//
//    new test ('abs','abc',$system->project->moject, false);

//    include "d:/web/lib/ui.php";
//
//    $skins['form'] = "
//        <div>
//        {user_login}
//        {user_password}
//        </div>
//    ";
//
//    $skins['edit'] = "
//        <input type='edit' value='{value}'>
//    ";
//
//    $skins['user_login'] = "
//        <input type='edit' value='{value}' style='background:yellow'>
//    ";
//
//    $form = new form ('login');
//    $form->edit ('user_login','default');
//
//    echo $form->render();


?>
