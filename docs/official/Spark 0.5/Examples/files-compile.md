## Spark 0.5 - files-compile

Compile/recompile the files in the database.



## Command Syntax
`files-compile [PRESENTATION_FLAGS]`<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php files-compile`<br>
Compile all the files in the database.
<br><br>
`php spark.php files-compile theme=DEFAULT`<br>
Compile all the files in the database, with presentation flags.
<br><br>
`php spark.php files-compile -hb theme=DEFAULT`<br>
Compile all the files in the database, with presentation flags.
<br><br>
`php spark.php files-compile -auto -hb theme=DEFAULT`<br>
Compile all the files in the database, with presentation flags.
<br><br>