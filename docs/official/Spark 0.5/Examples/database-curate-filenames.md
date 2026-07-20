## Spark 0.5 - database-curate-filenames

Generate the filename-related metadata for all the files.
<br><br>
This command is used to recreate the filename-related metadata in the database.
<br><br>



## Command Syntax

`database-curate-filenames [PRESENTATION_FLAGS]`<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php database-curate-filenames`<br>
Recreate the filename-specific metadata in the database.
<br><br>
`php spark.php database-curate-filenames theme=DEFAULT`<br>
Recreate the filename-specific metadata in the database, with presentation flags
<br><br>
`php spark.php database-curate-filenames -hb theme=DEFAULT`<br>
Recreate the filename-specific metadata in the database, with presentation flags
<br><br>
`php spark.php database-curate-filenames -auto -hb theme=DEFAULT`<br>
Recreate the filename-specific metadata in the database, with presentation flags
<br><br>