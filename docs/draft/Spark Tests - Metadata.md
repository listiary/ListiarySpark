Spark v 0.3 integration testing commands - Metadata layer

Listiary database contains and keeps metadata for the contents of different files.
This is needed for proper prefetching of data and is needed for the correct work of the wiki.

FF metadata stands for File to Files. It describes what other files each file is related to.
FI metadata stands for File to item Ids. It describes what files contain each public item Id.
IF metadata stands for item Id to Files. It describes what files contain each public item Id.

```
php spark.php metadata-view ff "FILE_NAME"					php spark.php metadata-view ff "radiowatch.anw"
php spark.php metadata-view fi "FILE_NAME"					php spark.php metadata-view fi "radiowatch.anw"
php spark.php metadata-view if "ITEM_ID"					php spark.php metadata-view if "radiowatch.anw.rnode"
```