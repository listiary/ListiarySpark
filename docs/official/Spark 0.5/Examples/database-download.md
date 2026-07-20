## Spark 0.5 - database-download

Download a copy of the database.
<br><br>
This command is used to download a full copy of the database to the `_downloads` folder.
<br><br>



## Command Syntax

`database-download [PRESENTATION_FLAGS]`<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php database-download`<br>
Download a copy the database in an SQL file.
<br><br>
`php spark.php database-download theme=DEFAULT`<br>
Download a copy the database in an SQL file, with presentation flags
<br><br>
`php spark.php database-download -hb theme=DEFAULT`<br>
Download a copy the database in an SQL file, with presentation flags
<br><br>
`php spark.php database-download -auto -hb theme=DEFAULT`<br>
Download a copy the database in an SQL file, with presentation flags
<br><br>