## Spark v0.5 - REPL or Not?

REPL CLI app is one that is interactive - that - asking user for input is REPL - Read-Eval-Print Loop.
It is very convenient for human users, but it breaks automated scripting, and takes away the expressive power of the shell
used to invoke the app.
<br>
Spark Cli currently does not allow a user to run many commands interactively, one after another,
however, some commands have a REPL variation. The way this works is basically, if the user provides the data
with flags, when invoking the script, everything works without interaction, but if the user omits
providing, data, he will be asked for the input, interactively.
<br>
We might have a `no-repl` also abbreviated `nr` flag, that will disallow REPL mode completely, and if something is missing, 
in terms of data, will throw an error. We might also have a repl variants of commands, but this is a bit of an overkill for now.
