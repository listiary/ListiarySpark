## Spark 0.5 - Metadata
Listiary database contains and keeps metadata for the contents of different files.
This is needed for proper prefetching of data and is needed for the correct work of the wiki.
<br><br>
FF metadata stands for File to Files. It describes what other files each file is related to.<br>
FI metadata stands for File to item Ids. It describes what files contain each public item Id.<br>
IF metadata stands for item Id to Files. It describes what files contain each public item Id.<br>
<br><br>



## Functions
`metadata-view` - Output the contents of a single metadata entry.

```
php spark.php metadata-view ff "FILE_NAME"
php spark.php metadata-view fi "FILE_NAME"
php spark.php metadata-view if "ITEM_ID"
```