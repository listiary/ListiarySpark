## Spark Commands - Configs

	A Spark CLI instance can be used to manage different Listiary wiki installs, and set up new ones, straight from your PC or server.
	So here is how this works - every Listiary wiki has a main config - that contains the information needed for the wiki to
	call the Describe compiler micro-service, connect to the SQL database, etc. Spark works with those same configs. 
	
	Users can create a new one by providing the needed secret data, or download/copy one from their wiki, 
	and place it in the `_configs` folder of their local Spark CLI instance. 

	In this article, we will look at how Spark handles Listiary configs.


## More on Configs

	*Why create new configs instead of importing/copying ones from their wikis?*
	Well, it might be a convenience or operational thing, but also, Spark is used to install
	new Listiary instances - and this process starts with the user creating a new config from scratch.
	
	*Config storage*
	The Spark CLI is a portable app, and inside are few folders, like `_downloads` and `_configs`.
	The former is where downloaded information goes, and the later, where configs are stored.
	So, one instance of Spark can hold many configs, although, currently, only one is selected and operational at a time - 
	we do not support commands against multiple wiki instances simultaneously.
	
	Much better way to store those configs will be in an SQLight database, that can be password protected, instead
	of plain text php files in a folder - but this is what we are currently using while prototyping.
	
	*Config selection*
	Currently - again - in prototyping phase, for simplicity sake we use the config that is called `config.php`
	as the working config (is considered the selected one Spark CLI will use to issue commands against).
	When we want to administer another Listiary instance, we go and rename the configs, manually.
	It is primitive, but works for now.
	
	Much better will be to have a mechanism, like in Windows diskpart utility - we run `php spark.php configs-list` - like we would
	the selected one has a star before its name, and we can run something like `php spark.php config-select 2` - to select another
	one, by number, not only by name. And we can store that selection in a temporary JSON Spark config file
	
	

## REPL or Not?

	REPL CLI app is one that is interactive - that - asking user for input is REPL - Read-Eval-Print Loop.
	It is very convenient for human users, but it breaks automated scripting, and takes away the expressive power of the shell
	used to invoke the app.
	
	Spark Cli currently does not allow a user to run many commands interactively, one after another,
	however, some commands have a REPL variation. The way this works is basically, if the user provides the data
	with flags, when invoking the script, everything works without interaction, but if the user omits
	providing, data, he will be asked for the input, interactively.

	We might have a `no-repl` also abbreviated `nr` flag, that will disallow REPL mode completely, and if something is missing, 
	in terms of data, will throw an error. We might also have a repl variants of commands, but this is a bit of an overkill for now.


## The 'config-make' commands

	Creates a new config file for a Listiary database connection, and save it in the configs folder.
	`config-make` and `config-make-online` are the same command implementation, under the hood - just the 
	default config-make is the config-make-online. Online here stands for running few tests against the actual database server
	compiler micro-service, etc - to verify that the provided data and credentials are active, not just looking valid - thus going online.
	The offline variant is the opposite - skip the active verification, trust the user with the data, as long as it looks good.
	
	'CONFIG_NAME' - set a custom name for the config
	'-d' flag - a config name including a timestamp in the filename.
	compiler - provides the compiler url for the config, instead of asking the user for the input.
	server - provides the SQL server name for the config, instead of asking the user for the input.
	database - provides the SQL database for the config, instead of asking the user for the input.
	user - provides the SQL database username for the config, instead of asking the user for the input.
	password - provides the SQL user's password for the config, instead of asking the user for the input.
	NB: THIS COMMAND HAS REPL MODE
	
	```
	config-make [CONFIG_NAME][-d]
	[compiler="<VALUE>"][server="<VALUE>"][database="<VALUE>"][user="<VALUE>"][password="<VALUE>"]
	[<PRESENTATION_FLAGS>]
	```
	```
	config-make-online [CONFIG_NAME][-d]
	[compiler="<VALUE>"][server="<VALUE>"][database="<VALUE>"][user="<VALUE>"][password="<VALUE>"]
	[<PRESENTATION_FLAGS>]
	```
	```
	config-make-offline [CONFIG_NAME][-d]
	[compiler="<VALUE>"][server="<VALUE>"][database="<VALUE>"][user="<VALUE>"][password="<VALUE>"]
	[<PRESENTATION_FLAGS>]
	```
	
	
## The 'configs-list' command

	List all the configs in the configs folder.

	```
	spark.php configs-list [<PRESENTATION_FLAGS>]
	```


## The 'config-show' command

	Show the constants inside the selected config.
	If none is selected, it will try to show the contents of a default config named 'config.php'.
	'CONFIG_NAME' - set a custom name for the config

	```
	spark.php config-show [CONFIG_NAME][<PRESENTATION_FLAGS>]
	```


## The 'config-test' command

	Test the selected config against the backend servers.
	If none is selected, it will try to test the a default config named 'config.php'.
	'CONFIG_NAME' - set a custom name for the config
	
	```
	spark.php config-test [CONFIG_NAME][<PRESENTATION_FLAGS>]
	spark.php config-test-compiler [CONFIG_NAME][<PRESENTATION_FLAGS>]
	```


## Further commands
	
_**config-select - use with configs-list to select a config and save it to spark's temp config**_
_**config-set - use it to edit a config's value**_
_**config-edit - use it to open a config in default editor**_
_**config-rename - use it to rename a config**_