## Listiary Spark v0.5

Spark 0.5 is a rewrite of Spark 0.4 in a Cli tool and Library format.
Now, we have one tool that is the Cli runner - SparkCli, and the library that can be reused across other projects - SparkLib.
<br><br>

Spark functionality, and thus SparkCli commands, can be divided in categories, based on what functionality
in Listiary they are designed to manage or bring.
<br><br>

[Spark 0.5 - Misc and Presentation flags] - The help command and presentation flags SparkCli functions take.<br>
[Spark 0.5 - Configs] - How SparkCli deals with Listiary configs.<br>
[Spark 0.5 - REPL] - REPL and no-repl functions in SparkCli 0.5.<br>
<br><br>
[Spark 0.5 - Files and Jsons] - Raw Describe file operations on the database.<br>
[Spark 0.5 - Metadata] - Metadata operations on the database.<br>
[Spark 0.5 - Database and Action] - Functions that deal with the database as a whole and some more complex actions.
<br><br>


## List of commands

	`NONE` - This is running the SparkCli without any arguments. Will output the help message.
	`UNKNOWN` - This is running the SparkCli with some invalid first argument. Will output the help message.
	`help` - Output the help message.
	<br><br>

	`config-make` - Make a config file, by asking the user to input values on the CMD.<br>
	`config-make-online` - Make a config file, by asking the user to input values on the CMD. Test connection and credentials.<br>
	`config-make-offline` - Make a config file, by asking the user to input values on the CMD. Don't test connection and credentials.<br>
	`configs-list` - List all the configs in the `_configs` folder.<br>
	`config-show` - Show the constants inside the selected config.<br>
	`config-test` - Test the selected config against the backend server.<br>
	`config-test-compiler` - Test the compiler service for the current config.<br>
	<br><br>

	`action-create-stub` - Create a new article stub.
	`action-delete-article` - Delete an article and all its files.
	<br><br>

	`database-test` - Test if database is working and empty for Listiary installation.
	`database-make-tables` - Make the tables in an empty database, installing a wiki instance.
	`database-wipe` - Nuke a database, deleting everything from it.
	`database-download` - Download a copy of the database.
	`database-upload` - Upload a copy of the database.
	`database-recompile` - Recompile all the files in the database.
	`database-curate` - Generate the full metadata for all the files.
	`database-curate-ids` - Generate the metadata for all the files, populating 1 of the 2 tables.
	`database-curate-filenames` - Generate the metadata for all the files, populating 1 of the 2 tables.
	`database-find-orphans` - Find orphaned articles in the database.
	<br><br>

	`file-view` - Output the contents of a single file.
	`file-download` - Download a Describe file.
	`file-upload` - Upload a Describe file to the database.
	`file-compile` - Compile a single file in the database to JSON.
	`file-delete` - Delete a single file.
	<br><br>

	`files-list` - List the files in the database.
	`files-count` - Count the files in the database.
	`files-download` - Download Describe files from the database.
	`files-upload` - Upload Describe files to the database.
	`files-compile` - Compile all Describe files in the database, thus populating the JSONs table in the database.
	`files-delete` - Delete all the Describe files from the database.
	<br><br>

	`json-view` - Output the contents of a single json file.
	`json-download` - Download a JSON file.
	`json-upload` - Upload given data representing a JSON file to the database.
	`json-delete` - Delete a single JSON file.
	<br><br>

	`jsons-list` - List the JSON files in the database.
	`jsons-count` - Count the JSON files in the database.
	`jsons-download` - Download JSON files from the database.
	`jsons-delete` - Delete all the JSON files from the database.
	<br><br>

	`metadata-view` - Output the contents of a single metadata entry.
