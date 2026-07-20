## Spark 0.5 - jsons-download

Fetch Describe files from the database.
Save those files to the `_downloads` folder of the SparkCli,
creating a new folder with a timestamp for the result of the execution.
<br><br>
For example, user that runs : `php spark.php jsons-download`<br>
will get a folder with a file : `_downloads\20260706-221700-jsons-download`, 
full of `.json` files
<br><br>



## Command Syntax
`jsons-download [max=INT][PRESENTATION_FLAGS]`<br>
`max` - The number of the maximum JSON files to download.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php jsons-download`<br>
Download all the compiled JSON files from the database.
<br><br>
`php spark.php jsons-download max=10`<br>
Download the first 10 compiled JSON files in the database.
<br><br>
`php spark.php jsons-download theme=DEFAULT`<br>
Download all the compiled JSON files from the database, with presentation flags.
<br><br>
`php spark.php jsons-download -hb theme=DEFAULT`<br>
Download all the compiled JSON files from the database, with presentation flags.
<br><br>
`php spark.php jsons-download -auto -hb theme=DEFAULT`<br>
Download all the compiled JSON files from the database, with presentation flags.
<br><br>