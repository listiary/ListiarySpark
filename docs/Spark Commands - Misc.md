## Spark Commands - Misc

	In this article we will look at the spark framework help and man commands, flags for altering the presentation of the output, and default behavior.

	about: Spark framework v 0.3
	Spark is an administrative CLI interface for administering the Listiary database layer.
	Spark is very powerful and unforgiving - be careful which script you are invoking and read the documentation.
	Spark is licensed under ...
	For more information visit documentation.listiary.org/...


## The - Help, Man, No, Fault

	command: `php spark.php`
	When ran without a command, spark displays a simple message, telling us in few lines what spark is,
	and encouraging us to run the help command, for more info. 
	When ran with a wrong command, we see an error message, letting us know the command is wrong, and the standard help message.
	
	command: `php spark.php -h`
	The help command gives us a help message of moderate depth and verbosity.
	The more in-depth and pagenated command will be 'man', which will give the user a manual, but it is not implemented yet.
	
	```
	command: php spark.php help [ <PRESENTATION_FLAGS> ] | -h [ <PRESENTATION_FLAGS> ]
	flags: 'help', 'h', '-h', '-help', '--help'
	Display help / usage message
	```


## The presentation flags

	The presentation flags are a number of flags and values that changes the way Spark CLI outputs data.
	They are used for user convenience and for fine-tuning the tool - for example for execution manually vs in automated scripts.
	
	flag: `auto`
	Removes the colors, for use in scripting.
	In PHP CLI, the console colors are special command characters that we output to change the color.
	This flags stops this behavior, so that no color command characters are outputted.
	syntax (all are valid): `auto`, `a`, `-a`, `-auto`, `--auto`
	
	flag: `hide-banner`
	Removes the ASCII art banner, for use in scripting.
	syntax (all are valid): `hide-banner`, `hb`, `-hidebanner`, `-hb`, `--hide-banner`
	
	flag: `theme=`
	Sets the color theme of the CLI text output.
	syntax: `theme=<THEME>`
	THEME: `GREEN`, `DBLUE`, `LBLUE`, `PASTEL`, `EARTH`, `CONTRAST`, `DEFAULT`, `VIOLET`, `CYAN`
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	