## Spark 0.5 - jsons-list

List the JSON files in the database.



## Command Syntax
`jsons-list [max=INT] [PRESENTATION_FLAGS]`<br>
`max` - The number of the maximum files to list.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php jsons-list`<br>
List all the compiled JSON files in the database.
<br><br>
`php spark.php jsons-list max=10`<br>
List the first 10 compiled JSON files in the database.
<br><br>
`php spark.php jsons-list theme=DEFAULT`<br>
List all the compiled JSON files in the database, with presentation flags.
<br><br>
`php spark.php jsons-list -hb theme=DEFAULT`<br>
List all the compiled JSON files in the database, with presentation flags.
<br><br>
`php spark.php jsons-list -auto -hb theme=DEFAULT`<br>
List all the compiled JSON files in the database, with presentation flags.
<br><br>