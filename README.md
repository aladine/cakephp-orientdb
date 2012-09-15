This is repo for cakephp with orientdb driver.

To use this driver, you have to use orientdb driver orientdb by AntonTerekhov:
https://github.com/AntonTerekhov/OrientDB-PHP

In this project i place it in Vendor folder. It can be placed in Plugin folder. 

The datasource file OrientSource is to store all the basic function CRUD of a datasource. Since my project involved vertex, most of functions interact with vertex instead of a specific table in orientdb. Remind that you have to run orientdb daemon beforehand. 

When calling a find/query function in model or controller, it will based on these Create, Read, Write, Delete function in datasource. For a customize query, please use command_query  function as follows:
$this->Orient->command_query($query) 

You can pass any orientdb query by using this function. 
