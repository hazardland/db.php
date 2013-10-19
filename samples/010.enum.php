<?php

    //here we will discuss enumerated types
    //it is the simple way to establishi one to many relation

    include './000.config.php';

    include '../db.php';

    class boy
    {
        public $id;
        public $name;
        /**
         * enum
         * @var girl
         */
        public $girl;
        public function __construct ($name)
        {
            $this->name = $name;
        }
    }

    //take a note we specified in public $girl comment 'enum' and also master class 'girl' as @var

    class girl extends \db\entity
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

    $database->add ('boy');
    $database->add ('girl');

    $database->update ();

    $john = new boy('John');

    //\db\debug ($database->boy);

    $database->boy->save($john);

    //lets see if john is single
    \db\debug ($john);

    $girls = array (new girl('Anne'),new girl('Fiona'),new girl('Nino'));
    //by the way this is multi save example too
    $database->girl->save ($girls);

    $john->girl[] = $girls[0];
    $john->girl[] = $girls[2];

    //well john is single
    //let us create some girls

    $database->boy->save ($john);
    //lets see what happend to john during save
    \db\debug($john);

    $john = $database->boy->load($john->id);
    //lets load john
    \db\debug ($john);

    //now john is not single any more
    //as result you will see:
    /*
    boy::__set_state(array(
          'id'  =  39,
          'name'  =  'John',
          'girl'  =
        array  (
            7  =
            girl::__set_state(array(
                  'id'  =  7,
                  'name'  =  'Anne',
            )),
            9  =
            girl::__set_state(array(
                  'id'  =  9,
                  'name'  =  'Nino',
            )),
        ),
    ))
     */

    //after load john->girl is array and its keys are of girl.id

    //now let us delete one girl of john

    unset ($john->girl[$girls[0]->id]);
    $database->boy->save ($john);

    $john = $database->boy->load($john->id);
    \db\debug ($john);

    //result:
    /*
    boy::__set_state(array(
          'id'  =  40,
          'name'  =  'John',
          'girl'  =
        array  (
            12  =
            girl::__set_state(array(
                  'id'  =  12,
                  'name'  =  'Nino',
            )),
        ),
    ))
     */

    //you can also just set id is in john.girl without girl objects
    //this is effective if you have some checkbox group in ui with john girls
    $john->girl[] = $girls[1]->id;
    $database->boy->save ($john);
    $john = $database->boy->load ($john->id);
    \db\debug ($john);
    //as result we added fiona by id
    /*
       boy::__set_state(array(
      'id'  =  44,
      'name'  =  'John',
      'girl'  =
    array  (
        24  =
        girl::__set_state(array(
              'id'  =  24,
              'name'  =  'Nino',
        )),
        23  =
        girl::__set_state(array(
              'id'  =  23,
              'name'  =  'Fiona',
        )),
    ),
))
     */

    $john->girl = null;
    $database->boy->save ($john);


    \db\debug ($database->girl->pimary);

    $john = $database->boy->load ($john->id);
    \db\debug ($john);

    //the end

    \db\debug ($database->context->usage);

?>
