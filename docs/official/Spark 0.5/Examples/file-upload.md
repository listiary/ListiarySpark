## Spark 0.5 - file-upload

Upload a Describe file to the database.

The user copies a file to upload in the `_uploads` directory,
might put it inside a folder or not, gives that relative path to the function,
and the file gets uploaded to the database.
<br><br>
For example, user that runs : `php spark.php file-upload "file-1\root.name.ds"`<br>
will get a file in the database : `root.name`
<br><br>


## Command Syntax
`file-upload LOCAL_FILE_PATH [PRESENTATION_FLAGS]`<br>
`LOCAL_FILE_PATH` - The local path of the raw source file in the `_uploads` folder to be uploaded<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php file-upload "root.name.ds"`<br>
Upload a file
<br><br>
`php spark.php file-upload "root.name.ds" theme=DEFAULT`<br>
Upload a file with presentation flags
<br><br>
`php spark.php file-upload "root.name.ds" -hb theme=DEFAULT`<br>
Upload a file with presentation flags
<br><br>
`php spark.php file-upload "root.name.ds" -auto -hb theme=DEFAULT`<br>
Upload a file with presentation flags
<br><br>