## Spark 0.5 - config-make

Make a config file, by asking the user to input values on the CMD.
<br><br>
This is a REPL (Read-Eval-Print Loop) function - it will ask the user for values,
if they are not provided with arguments. So, in scripting and automation, the script
writer needs to provide all the needed arguments.
<br><br>



## Command Syntax

```
config-make [CONFIG_NAME][-d]
[compiler="<VALUE>"][server="<VALUE>"][database="<VALUE>"][user="<VALUE>"][password="<VALUE>"]
[PRESENTATION_FLAGS]
```
<br>
`CONFIG_NAME` - set a custom name for the config.<br>
`-d` flag - a config name including a timestamp in the filename.<br>
`compiler` - provides the compiler url for the config, instead of asking the user for the input.<br>
`server` - provides the SQL server name for the config, instead of asking the user for the input.<br>
`database` - provides the SQL database for the config, instead of asking the user for the input.<br>
`user` - provides the SQL database username for the config, instead of asking the user for the input.<br>
`password` - provides the SQL user's password for the config, instead of asking the user for the input.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php config-make`<br>
Create a config with default name. REPL.
<br><br>
`php spark.php config-make -d`<br>
Create a config with default name. Add date to the name. REPL.
<br><br>
`php spark.php config-make ConfigA.php`<br>
Create a config with a name. REPL.
<br><br>
`php spark.php config-make ConfigA.php -d`<br>
Create a config with a name. Add date to the name. REPL.
<br><br>
`php spark.php config-make compiler="VALUE" server="VALUE" database="VALUE" user="VALUE" password="VALUE"`<br>
Create a config with default name. Provide the needed values on the command line.
<br><br>
`php spark.php config-make ConfigA.php compiler="VALUE" server="VALUE" database="VALUE" user="VALUE" password="VALUE"`<br>
Create a config with a name. Provide the needed values on the command line.
<br><br>
`php spark.php config-make ConfigA.php -d compiler="VALUE" server="VALUE" database="VALUE" user="VALUE" password="VALUE"`<br>
Create a config with a name. Add date to the name. Provide the needed values on the command line.
<br><br>
