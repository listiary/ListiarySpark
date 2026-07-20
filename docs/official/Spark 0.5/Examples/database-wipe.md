## Spark 0.5 - database-wipe

Wipe a database empty. The nuclear option.
<br><br>
This command is used to uninstall a Listiary wiki, or to prepare a database for a wiki installation.
<br><br>
This command is dangerous.



## Command Syntax

`database-wipe [PRESENTATION_FLAGS]`<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php database-wipe`<br>
Wipe a database for wiki installation.
<br><br>
`php spark.php database-wipe theme=DEFAULT`<br>
Wipe a database for wiki installation with presentation flags
<br><br>
`php spark.php database-wipe -hb theme=DEFAULT`<br>
Wipe a database for wiki installation with presentation flags
<br><br>
`php spark.php database-wipe -auto -hb theme=DEFAULT`<br>
Wipe a database for wiki installation with presentation flags
<br><br>