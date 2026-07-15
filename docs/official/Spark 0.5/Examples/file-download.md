## Spark 0.5 - file-download
Fetch a single file from the database.
Save that file to the `_downloads` folder of the SparkCli,
creating a new folder with a timestamp for the result of the execution.
<br><br>
For example, user that runs : `php spark.php file-download "root.name"`<br>
will get a folder with a file : `_downloads\20260706-221700-file-download\root.name.ds`
<br><br>


## Command Syntax
`file-download FILE_NAME [PRESENTATION_FLAGS]`<br>
`FILE_NAME` - The name of the raw source file in the database to be downloaded<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php file-download "root.name"`<br>
Download a file
<br><br>
`php spark.php file-download "root.name" theme=DEFAULT`<br>
Download a file with presentation flags
<br><br>
`php spark.php file-download "root.name" -hb theme=DEFAULT`<br>
Download a file with presentation flags
<br><br>
`php spark.php file-download "root.name" -auto -hb theme=DEFAULT`<br>
Download a file with presentation flags
<br><br>