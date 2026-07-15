Spark v 0.3 integration testing commands - Misc

## NO COMMAND
This section aims at testing the themes and various presentation flags.
Also, are we able to distinguish between a command and a presentation flag.

	```
	php spark.php
	php spark.php auto
	php spark.php hide-banner
	php spark.php theme=GREEN
	php spark.php theme=DBLUE
	php spark.php theme=LBLUE
	php spark.php theme=PASTEL
	php spark.php theme=EARTH
	php spark.php theme=CONTRAST
	php spark.php theme=DEFAULT
	php spark.php theme=VIOLET
	php spark.php theme=CYAN
	php spark.php theme="CYAN"
	php spark.php -a
	php spark.php --auto
	```

## WRONG / UNKNOWN COMMAND
Simply test the wrong command handling with some gibberish text

	```
	php spark.php jknsdln
	```

## help
Test the help command with and without presentation flags.

_**In the future, `help` can support a command parameter - `php spark.php help config-make-offline`**_

	```
	php spark.php -h
	php spark.php --help theme=DEFAULT
	php spark.php help -a -hb
	```