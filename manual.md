#Things to know before using orm
================

##What is MVC ? What is model view controller pattern ?

**MVC** stands for **Model View Controller**. If you about heard it before but still dont know what it is you might be afraid of it.

But MVC is nothing than bunch of terms describing best practices how code in big projects should be organized for not getting lost in them.

If you have developed at least some kind of web application you already have used **MVC** concepts withought knowing it. I could tell you that **Controller** is a portion of code which displays **Views** to user and also receives **input** from user, **processes** it using **Model** and than decides in what **View** to parse **output** data but nobody understands sentences like that.

Therefore I ll show you an example. (*Note that while using db.php our goal is only knowing how to architect **Model***)

View is very simple thing. Dont get anything what you read here literally I m trieng to come out with the simpliest examples. 

Imagine a task. We must build a product page where user will purchase product.

We have two html files: product.html and success.html

####product.html####
```html
<h1>{product_name}</h1>
<a href="{project_link}?page=purchase&product={product_id}">
    Buy product
</a>
```

####purchase.html####
```html
You have successfuly purchased {product_name}
```

This are two stupid static html files and we can totally consider them as **views**. In {product_name} there goes actual product name. When user clicks "Buy product" we will have page and product variables incoming in our script. Congratulations you know what views are !

But how to use that views in actual task ? Here we need a controller. Let us assume we are so stupid we build our entire site php script in only index.php?

####index.php####
```php
if ($_REQUEST['page']=='product')
{
    $product = $database->product->load ($_REQUEST['product']);
    
    $output = array ();
    $output['project_link'] = $_SERVER['REMOTE_ADDR'];
    $output['product_name'] = $product->name;
    $output['product_id'] = $product->id;

    $view = file_get_contents ('./product.html');
    foreach ($output as $field = > $value)
    {
        $view = str_replace ('{'.$field.'}', $value);
    }
    echo $view;
    exit;
}
else if ($_REQUEST['page']=='purchase')
{
    $product = $database->product->load ($_REQUEST['product']);

    ### MAGIC HAPPENS HERE !
    $product->buy();

    $output = array ();
    $output['project_link'] = $_SERVER['REMOTE_ADDR'];
    $output['product_name'] = $product->name;
    $output['product_id'] = $product->id;

    $view = file_get_contents ('./purchase.html');
    foreach ($output as $field = > $value)
    {
        $view = str_replace ('{'.$field.'}', $value);
    }
    echo $view;
    exit;

}
```

So this is **Controller**. If you look closer it containts two sections. They are almost identical. One parses page "product" and another parses page "purchase". That sections are almost identicall except in section "purchase" user buys a product while in section "product" user views product page. View variables like {product_name} and {product_id} are replaced using simple str_replace function. Views files are loaded simply by file_get_contents. And line $product->buy() actually does what it says. But where is that method code called **buy** ?

####shop.php####
```
namespace shop
{
    class proudct
    {
        public $id;
        public $name;
        public function buy ()
        {
            /*
            Here you should imagine the code where actual happens buy
            */
        }
    }
}
```

This file is **Model**. If that does not make sense for you we strongly recommend you to sell burgers.


##how can i develop my projcect with classes ?

##why do i have low sallary as a php programmer ?

#intermediate level

##installation
  the only file you need is db.php iself. just include db.php in your project and you are ready to go. for some reasons disable php notice level messages.
##setup connection
  simple way to start is to specify server username password and database name to db.database constructor. but remember you can connect to many servers same time and you can use many databases

##map class to table
##map namespace classes to tables
##map classes by pattern to tables

##get table by class
##get table by object

##create databases for mapped classes
##create tables for mapped classes

##synch changes to table structures

##use custom name table for class
##use custom name field of table for class property

##load all from table
##load by id from table
##load using pager from table
##load by field equals value field like value
##load using custom query from table
##load using custom query with pager

##save single object to table
##save with boolean result
##save with saved object result
##save without knowing object table
##save objects to table

##delete single object
##delete by id
##delete table objects
##delete varius type of object same time
##delete by query from table

##cache user - cache table records on user level (default session)
##cache long - table records on server level (default apc_cache)
##cache temp - table records for script runtime (default memory)
##develop and plug custom cache engine for desired cache level

##universal time handling

##table modifiers

##field modifiers

##field locaization