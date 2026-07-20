## Spark 0.5 - json-download


Fetch a single JSON file from the database.
Save that file to the `_downloads` folder of the SparkCli,
creating a new folder with a timestamp for the result of the execution.
<br><br>
For example, user that runs : `php spark.php json-download "root.name"`<br>
will get a folder with a file : `_downloads\20260706-221700-file-download\root.name.json`
<br><br>


## Command Syntax
`json-download FILE_NAME [PRESENTATION_FLAGS]`<br>
`FILE_NAME` - The name of the json file in the database to be downloaded<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php json-download "root.name"`<br>
Download a json file
<br><br>
`php spark.php json-download "root.name" theme=DEFAULT`<br>
Download a json file with presentation flags
<br><br>
`php spark.php json-download "root.name" -hb theme=DEFAULT`<br>
Download a json file with presentation flags
<br><br>
`php spark.php json-download "root.name" -auto -hb theme=DEFAULT`<br>
Download a json file with presentation flags
<br><br>