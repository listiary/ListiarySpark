## Spark 0.2 - 2list_files

List all the files in the database (One that we connect to in our `_config.php`).

```
usage (bash) - output to console                            php 2list_files.php
usage (bash) - output to file                               php 2list_files.php > "output.txt"
usage (bash) - append to file                               php 2list_files.php >> "output.txt"
usage (bash) - capture errors to file                       php 2list_files.php 2> "errors.txt"
usage (bash) - append errors to file                        php 2list_files.php 2>> "errors.txt"
usage (bash) - capture output and errors to file            php 2list_files.php > "all_output.txt" 2>&1
usage (bash 4+) - capture output and errors to file         php 2list_files.php &> "all_output.txt"
```