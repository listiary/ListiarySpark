## Listiary Spark v0.2

Spark 0.2 started to form more like a proper collection of command line tools.
It was also developed on my Linux Mint machine, to be used from the terminal.

N.B. Spark 0.2 was created for an older version of the database, and running it against
a modern Listiary database will not be safe - it is provided as a curiosity.


## Scripts

	`_config.php` - The config to be used.
	`_config_template.php` - The template used in creating the config, to be used - `_config.php`.
	<br><br>
	
	`1list_files.php` - List all files in a selected directory on the local file system.
	`1set_config.php` - Create a config file for the Listiary database.
	`1create_tables.php` - Create the needed tables for the wiki in the database provided in the "_config.php" file.
	`1wipe_database.php` - Delete all the contents from a database - as means to uninstall the wiki.
	<br><br>
	
	`2upload_files.php` - Upload all the files from a given directory to the database specified in the "_config.php".
	`2upload_file.php` - Upload a single file to the database specified in the "_config.php".
	`2download_files.php` - Download all the files from the database (from the "_config.php") to a local directory.
	`2download_file.php` - Download a selected file from the database (from the "_config.php") to a local directory.
	`2delete_files.php` - Delete all the files from the database (One that we connect to in our "_config.php").
	`2delete_file.php` - Delete a file from the database (One that we connect to in our "_config.php").
	`2list_files.php` - List all the files in the database (One that we connect to in our "_config.php").
	<br><br>
	
	`3compile_database.php` - No original Documentation. Compile the whole database.
	`3compile_entry.php` - No original Documentation. Compile an entry.
	<br><br>
	
	`4curate_database_filenames.php` - No original Documentation. Create the filename metadata.
	`4curate_database_ids.php` - No original Documentation. Create the id metadata.


## Script Examples

	[1list_files.php]()