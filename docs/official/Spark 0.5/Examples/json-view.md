## Spark 0.5 - json-view
Fetch a single JSON file from the compiled files in the database.
Output the contents of that file to the console.
<br><br>


## Command Syntax
`json-view FILE_NAME [PRESENTATION_FLAGS]`<br>
`FILE_NAME` - The name of the JSON file in the database to be viewed<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php json-view "root.name"`<br>
view a json file
<br><br>
`php spark.php json-view "root.name" theme=DEFAULT`<br>
view a json file with presentation flags
<br><br>
`php spark.php json-view "root.name" -hb theme=DEFAULT`<br>
view a json file with presentation flags
<br><br>
`php spark.php json-view "root.name" -auto -hb theme=DEFAULT`<br>
view a json file with presentation flags
<br><br>