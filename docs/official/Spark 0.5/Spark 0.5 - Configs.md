## Spark v0.5 - Configs

A Spark CLI instance can be used to manage different Listiary wiki installs, and set up new ones, straight from your PC or server.
So here is how this works - every Listiary wiki has a main config - that contains the information needed for the wiki to
call the Describe compiler micro-service, connect to the SQL database, etc. Spark works with those same configs. 
<br>
Users can create a new one by providing the needed secret data, or download/copy one from their wiki, 
and place it in the `_configs` folder of their local Spark CLI instance. 
<br>
In this article, we will look at how Spark handles Listiary configs.
<br><br>



## More on Configs

*Why create new configs instead of importing/copying ones from their wikis?*
Well, it might be a convenience or operational thing, but also, Spark is used to install
new Listiary instances - and this process starts with the user creating a new config from scratch.
<br>
*Config storage*
The Spark CLI is a portable app, and inside are few folders, like `_downloads` and `_configs`.
The former is where downloaded information goes, and the later, where configs are stored.
So, one instance of Spark can hold many configs, although, currently, only one is selected and operational at a time - 
we do not support commands against multiple wiki instances simultaneously.
<br>
Much better way to store those configs will be in an SQLight database, that can be password protected, instead
of plain text php files in a folder - but this is what we are currently using while prototyping.
<br>
*Config selection*
Currently - again - in prototyping phase, for simplicity sake we use the config that is called `config.php`
as the working config (is considered the selected one Spark CLI will use to issue commands against).
When we want to administer another Listiary instance, we go and rename the configs, manually.
It is primitive, but works for now.
<br>
Much better will be to have a mechanism, like in Windows diskpart utility - we run `php spark.php configs-list` - like we would
the selected one has a star before its name, and we can run something like `php spark.php config-select 2` - to select another
one, by number, not only by name. And we can store that selection in a temporary JSON Spark config file
<br><br>



## Functions
`config-make` - Make a config file, by asking the user to input values on the CMD.<br>
`config-make-online` - Make a config file, by asking the user to input values on the CMD. Test connection and credentials.<br>
`config-make-offline` - Make a config file, by asking the user to input values on the CMD. Don't test connection and credentials.<br>
`configs-list` - List all the configs in the `_configs` folder.<br>
`config-show` - Show the constants inside the selected config.<br>
`config-test` - Test the selected config against the backend server.<br>
`config-test-compiler` - Test the compiler service for the current config.<br>
<br><br>
