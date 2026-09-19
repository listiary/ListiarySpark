### Directories<br>

`/.git`<br>
The git folder.<br>
<br>

`/.github`<br>
Documents related to GitHub workflows, policies, etc.<br>
Importantly, `FUNDING.yml`, that sets the URLs for the '♡Sponsor' button.<br>
<br><br>

`/.vscode`<br>
A Visual Studio Code special folder.
Importantly, containing the JSON config for debugging PHP in VS Code.
<br><br>

`/docs`<br>
The main documentation lives on the documentation website. 
This directory is used as a workspace for developing new documentation.<br>
<br>

```
/release
/release/Spark v0.4.7z
/release/Spark v0.5.7z
```
Different version release archives.<br>
<br>

```
/src
/src/_configs
/src/_downloads
/src/_uploads
/src/SparkCli
/src/SparkLib
/src/spark.php
```
The project's current, up to date, official source code.<br>
The underscored folders are file containers - for files going to be uploaded to the database, 
or downloaded from the database, or database configs.<br>
`SparkLib` - most of the logic lives here.<br>
`SparkCli` - the CLI app - calls the library, and also manages session.<br>
`spark.php` - is a deliberately simple executable wrapper. 
It exists mainly as the conventional entry point for running Spark.<br>
<br>

```
/work
/work/Current
/work/Spark v0.1
/work/Spark v0.2
/work/Spark v0.3
/work/Spark v0.4
/work/Spark v0.5
```
Files related to ongoing development.<br>
In this repository, we currently have different versions of the source code in this folder.
<br><br>


### Documents<br>

```
CHANGELOG.md
CODE_OF_CONDUCT.md
CONTRIBUTING.md
LICENSE
README.md
REPOSITORY_LAYOUT.md
```
<br><br>