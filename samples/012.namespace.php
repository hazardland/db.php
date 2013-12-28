<?php

    include './000.config.php';

    include '../db.php';

    //we include source with namespace 'core'
    include './012.source.php';


    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    //here we scan all the classes which name begins with '\core\'
    //you can use '.' instead of '\' if you wish
    //as we do:
    $database->scan('core');

    //define locales for localized fields
    //first locale is treated as default locale
    $database->locales(array(new \db\locale('en'),new \db\locale('ge')));

    $database->update();


    //you can access class handler on $database->namespace->[namespace->]name
    $result = $database->core->solution->load (\db\by('name','blog'));
    if ($result)
    {
        $solution = reset ($result);
    }
    else
    {
        $solution = new \core\solution ('blog');
        $database->save ($solution);
    }

    $result = $database->core->project->load (\db\by('name','site'));
    if ($result)
    {
        $project = reset ($result);
    }
    else
    {
        $project = new \core\project('site',$solution);
        $project->title_ge = 'საიტი';
        $project->title_en = 'site';
        $database->save ($project);
    }

    \db\debug ($solution);
    \db\debug ($project);

    \db\debug ($database->context->usage);

?>
