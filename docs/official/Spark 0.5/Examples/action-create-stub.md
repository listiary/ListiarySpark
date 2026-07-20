## Spark 0.5 - action-delete-article

Create a new article stub.
<br><br>



## Command Syntax

`action-delete-article "TEXT" "NAMESPACE" [PRESENTATION_FLAGS]`<br>
`TEXT` - text of the title of the article<br>
`NAMESPACE` - the namespace of the article<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php action-create-stub "lorem" "namespace.name"`<br>
Create an article stub.
<br><br>
`php spark.php action-create-stub "lorem" "namespace.name" theme=DEFAULT`<br>
Create an article stub, with presentation flags
<br><br>
`php spark.php action-create-stub "lorem" "namespace.name" -hb theme=DEFAULT`<br>
Create an article stub, with presentation flags
<br><br>
`php spark.php action-create-stub "lorem" "namespace.name" -auto -hb theme=DEFAULT`<br>
Create an article stub, with presentation flags
<br><br>