## Listiary Spark v0.4

Spark 0.4 improved upon 0.3, adding more scripts to the collection.
Spark 0.4 also added the idea of actions, and there are some not-implemented ones as well,
that only exist as empty files, as placeholders.



## Scripts

	`spark_ver.php` - Show the banner and the initial message for Spark.
	<br><br>
	
	`config_make.php` - Make a config file, by asking the user to input values on the CMD.
	`config_show.php` - Show the constants inside the selected config.
	`config_test.php` - Test the selected config against the backend server.
	<br><br>
	
	`action_create_stub.php` - Create a new article stub.
	`action_delete_article.php` - Delete an article and all its files.
	`.action_download_articles_in_namespace.php` - Suggested script.
	`.action_edit_article.php` - Suggested script.
	`.action_find_similar_articles.php` - Suggested script.
	`.action_list_articles_in_namespace.php` - Suggested script.
	`.action_rename_article.php` - Suggested script.
	`.action_rename_namespace.php` - Suggested script.
	`.action_show_article_namespace.php` - Suggested script.
	<br><br>
	
	`database_curate.php` - Generate the full metadata for all the files.
	`database_curate_filenames.php` - Generate the metadata for all the files, populating 1 of the 2 tables.
	`database_curate_ids.php` - Generate the metadata for all the files, populating 1 of the 2 tables.
	`database_find_orphans.php` - Find orphaned articles in the database.
	`database_make_tables.php` - Make the tables in an empty database, installing a wiki instance.
	`database_recompile.php` - Recompile all the files in the database.
	`database_test.php` - Test if database is working and empty for Listiary installation.
	`database_test_compiler.php` - Test the compiler service for the current config.
	`database_wipe.php` - Nuke a database, deleting everything from it.
	<br><br>

	`directory_list.php` - list files in a local directory.
	<br><br>
	
	`file_compile.php` - Compile a single file in the database to JSON.
	`file_delete.php` - Delete a single file.
	`file_delete_nowarn.php` - Delete a single file, don't ask for confirmation.
	`file_download.php` - Download a Describe file.
	`file_upload.php` - Upload a Describe file to the database.
	`file_view.php` - Output the contents of a single file.
	<br><br>

	`files_compile.php` - Compile all Describe files in the database, thus populating the JSONs table in the database.
	`files_count.php` - Count the files in the database.
	`files_delete.php` - Delete all the Describe files from the database.
	`files_delete_nowarn.php` - Delete all the Describe files from the database, don't ask for confirmation.
	`files_download.php` - Download Describe files from the database.
	`files_list.php` - List the files in the database.
	`files_upload.php` - Upload Describe files to the database.
	<br><br>

	`json_delete.php` - Delete a single JSON file.
	`json_delete_nowarn.php` - Delete a single JSON file, don't ask for confirmation.
	`json_download.php` - Download a JSON file.
	`json_upload.php` - Upload given data representing a JSON file to the database.
	`json_view.php` - Output the contents of a single json file.
	<br><br>

	`jsons_count.php` - Count the JSON files in the database.
	`jsons_delete.php` - Delete all the JSON files from the database.
	`jsons_delete_nowarn.php` - Delete all the JSON files from the database, don't ask for confirmation.
	`jsons_download.php` - Download JSON files from the database.
	`jsons_list.php` - List the JSON files in the database.
	`jsons_upload.php` - Upload JSON files to the database.
	<br><br>

	`metadataff_view.php` - Output the contents of a single metadata entry. FF metadata stands for File to Files.
	`metadatafi_view.php` - Output the contents of a single metadata entry. FI metadata stands for File to item Ids.
	`metadataif_view.php` - Output the contents of a single metadata entry. IF metadata stands for item Id to Files.
