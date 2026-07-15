## Spark 0.5 - file-compile

Compile a single Describe file in the database to JSON.
<br><br>
The user runs a command like `files-list` to list the raw files in the database,
then runs `file-compile` to compile/recompile a single file.
<br><br>
This command is useful when there is some kind of a bug, and we want to recompile
a file, where something went wrong.
<br><br>


## Command Syntax
`file-compile FILE_NAME [PRESENTATION_FLAGS]`<br>
`FILE_NAME` - The name of the file in the database.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php file-compile "root.name"`<br>
Compile/recompile a file
<br><br>
`php spark.php file-compile "root.name" theme=DEFAULT`<br>
Compile/recompile a file with presentation flags
<br><br>
`php spark.php file-compile "root.name" -hb theme=DEFAULT`<br>
Compile/recompile a file with presentation flags
<br><br>
`php spark.php file-compile "root.name" -auto -hb theme=DEFAULT`<br>
Compile/recompile a file with presentation flags
<br><br>