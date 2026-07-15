Listiary Spark

Spark is an administrative layer for managing the Listiary database.
Spark is very powerful and unforgiving - be careful which script you are invoking and read the documentation.
For the user documentation of the current version check - (SparkCli v0.3 Manual)[]


## Basic Architecture
The Listiary Spark framework, or Spark for short, consists of a library containing the core functionality,
and few toold utilizing it.
SparkLib - the library containing the core functionality.
SparkCli - the CLI interface for the framework, used to administer instances of Listiary on the command line.
SparkWeb - will be an HTTP API based interface for spark that can do the same thing SparkCli can do, but remotely.
Listiary Admin Module - the admin interface in Listiary - has a lot of the same functionality, but is limited to the current wiki.


## Versioning
In Listiary, subsystems are versioned internally - like separate pieces of software that come together - which they kind of are, in all fairness.
Currently, SparkLib and SparkCli are in version 0.3.
There is one quirk to this - before version 0.3, we just had one collection of scripts. At 0.3, now we have crystallized this from a folder of
loose scripts to a library - SparkLib and a CLI runner front end - SparkCli.

	Spark 0.1
	Spark 0.2
	Spark 0.3

	Spark v 0.1
	Spark v 0.2
	Spark v 0.3 (Spark Cli v 0.3; Spark Lib v 0.3)