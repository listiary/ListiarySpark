## Spark 0.5 - metadata-view

Output the contents of a single metadata entry.
<br><br>
Listiary database contains and keeps metadata for the contents of different files.
This is needed for proper prefetching of data and is needed for the correct work of the wiki.
<br><br>
FF metadata stands for File to Files. It describes what other files each file is related to.
FI metadata stands for File to item Ids. It describes what files contain each public item Id.
IF metadata stands for item Id to Files. It describes what files contain each public item Id.
<br><br>



## Command Syntax
`metadata-view FLAG ID [PRESENTATION_FLAGS]`<br>
`FLAG` - `ff`, `fi`, `if` - Sets which metadata to fetch.
`ID` - Filename or item Id to fetch metadata for.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php metadata-view ff "root.filename"`<br>
View 'File to Files' metadata for a specific file<br>
Tells you which files need to be fetched together.<br>
<br><br>
`php spark.php metadata-view fi "root.filename"`<br>
View 'File to Items' metadata for a specific file.<br>
Tells you what items a file has.<br>
<br><br>
`php spark.php metadata-view if "root.filename.rnode"`<br>
View 'Item to Files' metadata for a specific public item.<br>
Tells you which files have that item.<br>
<br><br>
`php spark.php metadata-view ff "root.filename" -auto -hb theme=DEFAULT`<br>
View 'File to Files' metadata for a specific file.<br>
Tells you which files need to be fetched together.<br>
With presentation flags.<br>
<br><br>
`php spark.php metadata-view fi "root.filename" -auto -hb theme=DEFAULT`<br>
View 'File to Items' metadata for a specific file.<br>
Tells you what items a file has.<br>
With presentation flags.<br>
<br><br>
`php spark.php metadata-view if "root.filename.rnode" -auto -hb theme=DEFAULT`<br>
View 'Item to Files' metadata for a specific public item.<br>
Tells you which files have that item.<br>
With presentation flags.<br>
<br><br>