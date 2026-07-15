## Listiary Spark v0.1

Spark 0.1 was something very rudimentary - a folder with 3 random administrative scripts, that could compile
the database and test the Compiler API. Those were HTTP invoke-able PHP scripts.

There was no documentation, and it was not called Spark, but we include it in the repo, as a historical curiosity.

At that point, the Listiary prototype was much simpler, and the database was getting compiled
to a single JSON file - that represented all the data.

N.B. Spark 0.1 was created for an older version of the database, and running it against
a modern Listiary database will not be safe - it is provided as a curiosity.



## Scripts

	`compiledb.php` - A script compiling the database.
	`compiledb2.php` - A script compiling the database, version of `compiledb.php`.
	`testCompilerAPI.php` - Invoke with an 'article' URL parameter to test the compiler AWS Lambda.