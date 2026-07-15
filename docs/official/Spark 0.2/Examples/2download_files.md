## Spark 0.2 - 2download_files

Download all the files from the database (from the `_config.php`) to a local directory.

```
usage (bash) - output to console                            php 2download_files.php "/path/to/directory"
usage (bash) - output to file                               php 2download_files.php "/path/to/directory" > "output.txt"
usage (bash) - append to file                               php 2download_files.php "/path/to/directory" >> "output.txt"
usage (bash) - capture errors to file                       php 2download_files.php "/path/to/directory" 2> "errors.txt"
usage (bash) - append errors to file                        php 2download_files.php "/path/to/directory" 2>> "errors.txt"
usage (bash) - capture output and errors to file            php 2download_files.php "/path/to/directory" > "all_output.txt" 2>&1
usage (bash 4+) - capture output and errors to file         php 2download_files.php "/path/to/directory" &> "all_output.txt"
```