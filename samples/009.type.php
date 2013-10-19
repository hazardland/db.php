<?php

    //value ?

    //here we will discuss field types

    include './000.config.php';

    include '../db.php';

    class order
    {
        /**
         * primary
         * @var integer
         */
        public $id;
        /**
         * default null
         * length 50
         * @var string
         */
        public $name;
        /**
         * default null
         * @var integer
         */
        public $count;
        /**
         * default null
         * @var float
         */
        public $weight;
        /**
         * default null
         * @var float
         */
        public $height;
        /**
         * default null
         * @var time
         */
        public $income;
        /**
         * default null
         * @var date
         */
        public $return;
        /**
         * default null
         * @var bool
         */
        public $ready;
        /**
         * default null
         * @var client
         */
        public $client;

        /**
         * @var string
         */
        public $worker;
    }

    class client extends \db\entity
    {
        public $id;
        public $name;
    }


    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    $database->add ('order');
    $database->add ('client');

    $database->update ();

    $client = new client();
    $client->name = 'Bill Gates';

    $order = new order();
    $order->name = 'Apple';
    $order->client = $client;

    //the value of ready must be boolean
    //but we put 5 here
    $order->ready = 5;

    $order->income = 'there must be date time value';

    //here must be an int
    $order->count = 1.5;

    //and here float
    $order->weight = true;

    //it will become string after load
    $order->worker = 10;

    //let's see all the mess inside order before save
    \db\debug ($order);

    $database->client->save ($client);
    $database->order->save ($order);

    //object was modified during save
    //let us see how engine corrected values
    \db\debug ($order);

    //let us see if something changed
    $order = $database->order->load ($order->id);

    \db\debug ($order);

    //create order


?>
