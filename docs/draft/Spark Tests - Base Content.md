Spark v 0.3 integration testing commands - Basic Files

1. Single Files
This section aims at testing basic functionality for the Single File commands.

```
php spark.php file-view "FILE_NAME"							php spark.php file-view "radiowatch.anw"
php spark.php file-download "FILE_NAME"						php spark.php file-download "radiowatch.anw"
php spark.php file-upload "LOCAL_FILE_PATH"					php spark.php file-upload "file-1\radiowatch.anw.ds"
php spark.php file-compile "FILE_NAME"						php spark.php file-compile "radiowatch.anw"
php spark.php file-delete "FILE_NAME"						php spark.php file-delete "radiowatch.anw"

php spark.php json-view "FILE_NAME"							php spark.php json-view "radiowatch.anw"
php spark.php json-download "FILE_NAME"						php spark.php json-download "radiowatch.anw"
php spark.php json-upload "LOCAL_FILE_PATH"					php spark.php json-upload "json-1\radiowatch.anw.json"
php spark.php json-delete "FILE_NAME"						php spark.php json-delete "radiowatch.anw"
```


2. Bulk Files
This section aims at testing basic functionality for the Bulk Files commands.
_**We will be able to do a deep upload/download - where**_
_**file like foods.vegies.carrot-dishes.ds is resolved to `\foods\vegies\carrot-dishes.ds`**_
_**and uploaded back like `foods.vegies.carrot-dishes.ds`**_
_**Can also add max to `files-upload`**_


```
php spark.php files-list
php spark.php files-list max=12
php spark.php files-count
php spark.php files-download
php spark.php files-download max=12
php spark.php files-compile
php spark.php files-delete
php spark.php files-upload "uploads-1"

php spark.php jsons-list
php spark.php jsons-count
php spark.php jsons-download
php spark.php jsons-download max=12
php spark.php jsons-delete
```