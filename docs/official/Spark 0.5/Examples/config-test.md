## Spark 0.5 - config-test

Test the selected config against the backend server.
<br><br>



## Command Syntax

`config-test [CONFIG_NAME] [PRESENTATION_FLAGS]`<br>
`CONFIG_NAME` - The name of the config in the `_configs` folder, to be tested.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php config-test`<br>
Test the default config.
<br><br>
`php spark.php config-test theme=DEFAULT`<br>
Test the default config with presentation flags
<br><br>
`php spark.php config-test -hb theme=DEFAULT`<br>
Test the default config with presentation flags
<br><br>
`php spark.php config-test -auto -hb theme=DEFAULT`<br>
Test the default config with presentation flags
<br><br>
`php spark.php config-test "my-test-config.php"`<br>
Test the selected config.
<br><br>
`php spark.php config-test "my-test-config.php" theme=DEFAULT`<br>
Test the selected config with presentation flags
<br><br>
`php spark.php config-test "my-test-config.php" -hb theme=DEFAULT`<br>
Test the selected config with presentation flags
<br><br>
`php spark.php config-test "my-test-config.php" -auto -hb theme=DEFAULT`<br>
Test the selected config with presentation flags
<br><br>