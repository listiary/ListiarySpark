## Spark 0.5 - database-curate

Generate the full metadata for all the files.
<br><br>
This command is used to recreate the metadata in the database.
<br><br>



## Command Syntax

`database-curate [PRESENTATION_FLAGS]`<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php database-curate`<br>
Recreate the metadata in the database.
<br><br>
`php spark.php database-curate theme=DEFAULT`<br>
Recreate the metadata in the database, with presentation flags
<br><br>
`php spark.php database-curate -hb theme=DEFAULT`<br>
Recreate the metadata in the database, with presentation flags
<br><br>
`php spark.php database-curate -auto -hb theme=DEFAULT`<br>
Recreate the metadata in the database, with presentation flags
<br><br>