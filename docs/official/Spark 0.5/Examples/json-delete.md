## Spark 0.5 - json-delete

Delete a single JSON file in the database.
<br><br>
The user runs a command like `jsons-list` to list the json files in the database,
then runs `json-delete` to delete a single json file.
<br><br>


## Command Syntax
`json-delete FILE_NAME [PRESENTATION_FLAGS]`<br>
`FILE_NAME` - The name of the json file in the database.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php json-delete "root.name"`<br>
Delete a json file
<br><br>
`php spark.php json-delete "root.name" theme=DEFAULT`<br>
Delete a json file with presentation flags
<br><br>
`php spark.php json-delete "root.name" -hb theme=DEFAULT`<br>
Delete a json file with presentation flags
<br><br>
`php spark.php json-delete "root.name" -auto -hb theme=DEFAULT`<br>
Delete a json file with presentation flags
<br><br>