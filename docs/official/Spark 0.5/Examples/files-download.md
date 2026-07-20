## Spark 0.5 - files-download

Fetch Describe files from the database.
Save those files to the `_downloads` folder of the SparkCli,
creating a new folder with a timestamp for the result of the execution.
<br><br>
For example, user that runs : `php spark.php files-download`<br>
will get a folder with a file : `_downloads\20260706-221700-file-download`, 
full of `.ds` files
<br><br>



## Command Syntax
`files-download [max=INT][PRESENTATION_FLAGS]`<br>
`max` - The number of the maximum files to download.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php files-download`<br>
Download all the raw files from the database.
<br><br>
`php spark.php files-download max=10`<br>
Download the first 10 files in the database.
<br><br>
`php spark.php files-download theme=DEFAULT`<br>
Download all the raw files from the database, with presentation flags.
<br><br>
`php spark.php files-download -hb theme=DEFAULT`<br>
Download all the raw files from the database, with presentation flags.
<br><br>
`php spark.php files-download -auto -hb theme=DEFAULT`<br>
Download all the raw files from the database, with presentation flags.
<br><br>