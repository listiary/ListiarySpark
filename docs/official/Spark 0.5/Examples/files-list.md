## Spark 0.5 - files-list

List the files in the database.



## Command Syntax
`files-list [max=INT] [PRESENTATION_FLAGS]`<br>
`max` - The number of the maximum files to list.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php files-list`<br>
List all the files in the database.
<br><br>
`php spark.php files-list max=10`<br>
List the first 10 files in the database.
<br><br>
`php spark.php files-list theme=DEFAULT`<br>
List all the files in the database, with presentation flags.
<br><br>
`php spark.php files-list -hb theme=DEFAULT`<br>
List all the files in the database, with presentation flags.
<br><br>
`php spark.php files-list -auto -hb theme=DEFAULT`<br>
List all the files in the database, with presentation flags.
<br><br>