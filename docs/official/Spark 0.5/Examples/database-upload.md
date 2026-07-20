## Spark 0.5 - database-upload

Upload a copy of the database.
<br><br>
This function is used to upload a database backup to an active SQL database.
<br><br>
This is useful for backup and such.
<br><br>



## Command Syntax
`database-upload FILE_PATH [PRESENTATION_FLAGS]`<br>
`FILE_PATH` - path to the upload SQL file in the `_uploads` directory
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php database-upload "uploads-1\database.sql"`<br>
Upload copy, overriding the database.
<br><br>
`php spark.php database-upload "uploads-1\database.sql" theme=DEFAULT`<br>
Upload copy, overriding the database, with presentation flags.
<br><br>
`php spark.php database-upload "uploads-1\database.sql" -hb theme=DEFAULT`<br>
Upload copy, overriding the database, with presentation flags.
<br><br>
`php spark.php database-upload "uploads-1\database.sql" -auto -hb theme=DEFAULT`<br>
Upload copy, overriding the database, with presentation flags.
<br><br>