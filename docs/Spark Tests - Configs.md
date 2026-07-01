Spark v 0.3 integration testing commands - Misc

## config-make (REPL?)
This section aims at testing the command with and without config name, and date prefix.
Also, REPL style - no values provided, user is asked, vs no-REPL - values for compiler, server etc. 
are provided on the command line.

	```
	php spark.php config-make
	php spark.php config-make
	php spark.php config-make -d
	php spark.php config-make ConfigA.php
	php spark.php config-make CONFIG_B
	php spark.php config-make ConfigA.php -d
	php spark.php config-make compiler="[VALUE]" server="[VALUE]" database="[VALUE]" user="[VALUE]" password="[VALUE]"
	php spark.php config-make ABCD.php compiler="[VALUE]" server="[VALUE]" database="[VALUE]" user="[VALUE]" password="[VALUE]"
	```


## config-make-online (REPL?)
This section aims at testing the command with and without config name, and date prefix.
Also, REPL style - no values provided, user is asked, vs no-REPL - values for compiler, server etc. 
are provided on the command line.

	```
	php spark.php config-make-online
	php spark.php config-make-online
	php spark.php config-make-online -d
	php spark.php config-make-online ConfigA.php
	php spark.php config-make-online CONFIG_B
	php spark.php config-make-online ConfigA.php -d
	php spark.php config-make-online compiler="[VALUE]" server="[VALUE]" database="[VALUE]" user="[VALUE]" password="[VALUE]"
	php spark.php config-make-online ABCD.php compiler="[VALUE]" server="[VALUE]" database="[VALUE]" user="[VALUE]" password="[VALUE]"
	```


## config-make-offline (REPL?)
This section aims at testing the command with and without config name, and date prefix.
Also, REPL style - no values provided, user is asked, vs no-REPL - values for compiler, server etc. 
are provided on the command line.

	```
	php spark.php config-make-offline
	php spark.php config-make-offline
	php spark.php config-make-offline -d
	php spark.php config-make-offline ConfigA.php
	php spark.php config-make-offline CONFIG_B
	php spark.php config-make-offline ConfigA.php -d
	php spark.php config-make-offline compiler="[VALUE]" server="[VALUE]" database="[VALUE]" user="[VALUE]" password="[VALUE]"
	php spark.php config-make-offline ABCD.php compiler="[VALUE]" server="[VALUE]" database="[VALUE]" user="[VALUE]" password="[VALUE]"
	```


## config-show
Few simple tests, with and without default config name.
_**can add the concept of selected config, instead of showing "config.php" when none is selected**_
_**so 'php spark.php config-show' - shows the selected config**_
_**also, we can run 'php spark.php configs-list' and then something like 'php spark.php config-show 2' - to show config No 2.**_
_**much like diskpart util in Windows**_

	```
	php spark.php config-show
	php spark.php config-show theme=DEFAULT
	php spark.php config-show "ABACAZAM.php"
	php spark.php config-show ABACAZAM.php theme=DEFAULT
	```


## config-test
Few simple tests, with and without default config name.
_**we should also test the URL of the compiler, in the SparkCore function**_

	```
	php spark.php config-test
	php spark.php config-test "my-test-config.php"
	php spark.php config-test -auto
	php spark.php config-test -auto -hb theme=DEFAULT
	php spark.php config-test -hb theme=DEFAULT
	php spark.php config-test theme=DEFAULT
	
	php spark.php config-test-compiler
	spark.php config-test-compiler "my-test-config.php"
	```
	
	
## configs-list
Few simple tests, with and without default config name.
_**might add a pattern matching here - 'php spark.php configs-list [PATTERN]'**_

	```
	php spark.php configs-list
	php spark.php configs-list theme=DEFAULT
	```
