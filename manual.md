	Things to know before using orm

. What is business model ? and also what is MVC ?
  seriously who pays you if you dont know that. business model is the only way not to get lost in big project. simply talking it describes your project logic in classes dont get the project logic term wrong there is also a portion of project which interacts with user and offers user interface. folks call it view. and what delivers views to user? a part of project which is called controller. controller controls user interaction to your model using views. first it gives a user to view an interface. than receives input from user and passes properly to model. model thinks and rerturns data. controller parses that data into result view.

or what takes input and passes to model which by itself processes input and passes back output ready to represented in view ?

 and also a portion which takes input data from view passes properly to model. model thinks gives back data and that portion passes back model given data as output in view. an intermediate part between view and model which offers comunication between those to is called controler.

therefore they named that kind of project arcitechure model view controller pattern. than they got tired pronouncing such long three word and called it mvc and there you go. no matter how you solve your developement tasks you always have this three subjects in your code. sometimes messed in each other sometimes separated without knowing names that enterprise developers gave them. but if you cant recall what part in your recent project is what than that is because you dont use model. i even feel sorry for your brain cause you make him to deal with huge unorganized thougts and concepts. show some manners think gently with nice thoughts and write organized code. and well formed code in big solutions begins with model. as we mentioned in the begining model is nothing than classes in namespaces. the only thing you need to manage therefore is how to bind your tasks in classes, methods and fields.

. how can i develop my projcect with classes ?
. why do i have low sallary as a php programmer ?

intermediate level

. installation
  the only file you need is db.php iself. just include db.php in your project and you are ready to go. for some reasons disable php notice level messages.
. setup connection
  simple way to start is to specify server username password and database name to db.database constructor. but remember you can connect to many servers same time and you can use many databases

map class to table
map namespace classes to tables
map classes by pattern to tables

get table by class
get table by object

create databases for mapped classes
create tables for mapped classes

synch changes to table structures

use custom name table for class
use custom name field of table for class property

load all from table
load by id from table
load using pager from table
load by field equals value field like value
load using custom query from table
load using custom query with pager

save single object to table
save with boolean result
save with saved object result
save without knowing object table
save objects to table

delete single object
delete by id
delete table objects
delete varius type of object same time
delete by query from table

cache user - cache table records on user level (default session)
cache long - table records on server level (default apc_cache)
cache temp - table records for script runtime (default memory)
develop and plug custom cache engine for desired cache level

universal time handling

table modifiers

field modifiers

field locaization