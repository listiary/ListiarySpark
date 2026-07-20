## Spark 0.5 - json-upload

Upload given data representing a JSON file to the database.

The user copies a file to upload in the `_uploads` directory,
might put it inside a folder or not, gives that relative path to the function,
and the file gets uploaded to the database's table for compiled JSON documents.
<br><br>
For example, user that runs : `php spark.php json-upload "json-1\root.name.json"`<br>
will get a file in the database : `root.name`
<br><br>


## Command Syntax
`json-upload LOCAL_FILE_PATH [PRESENTATION_FLAGS]`<br>
`LOCAL_FILE_PATH` - The local path of the raw source file in the `_uploads` folder to be uploaded<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php json-upload "root.name.ds"`<br>
Upload a json file
<br><br>
`php spark.php json-upload "root.name.ds" theme=DEFAULT`<br>
Upload a json file with presentation flags
<br><br>
`php spark.php json-upload "root.name.ds" -hb theme=DEFAULT`<br>
Upload a json file with presentation flags
<br><br>
`php spark.php json-upload "root.name.ds" -auto -hb theme=DEFAULT`<br>
Upload a json file with presentation flags
<br><br>