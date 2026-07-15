## Spark 0.2 - 2delete_file

Delete a file from the database (One that we connect to in our `_config.php`).

```
usage (bash) - output to console                            php 2delete_file.php "name.in.database"
usage (bash) - output to file                               php 2delete_file.php "name.in.database" > "output.txt"
usage (bash) - append to file                               php 2delete_file.php "name.in.database" >> "output.txt"
usage (bash) - capture errors to file                       php 2delete_file.php "name.in.database" 2> "errors.txt"
usage (bash) - append errors to file                        php 2delete_file.php "name.in.database" 2>> "errors.txt"
usage (bash) - capture output and errors to file            php 2delete_file.php "name.in.database" > "all_output.txt" 2>&1
usage (bash 4+) - capture output and errors to file         php 2delete_file.php "name.in.database" &> "all_output.txt"
```