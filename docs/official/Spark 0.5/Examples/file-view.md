## Spark 0.5 - file-view
Fetch a single file from the database.
Output the contents of that file to the console.
<br><br>


## Command Syntax
`file-view FILE_NAME [PRESENTATION_FLAGS]`<br>
`FILE_NAME` - The name of the raw source file in the database to be viewed<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php file-view "root.name"`<br>
view a file
<br><br>
`php spark.php file-view "root.name" theme=DEFAULT`<br>
view a file with presentation flags
<br><br>
`php spark.php file-view "root.name" -hb theme=DEFAULT`<br>
view a file with presentation flags
<br><br>
`php spark.php file-view "root.name" -auto -hb theme=DEFAULT`<br>
view a file with presentation flags
<br><br>