## Spark 0.5 - config-test-compiler

Test the compiler service for the current config.
<br><br>



## Command Syntax

`config-test-compiler [CONFIG_NAME] [PRESENTATION_FLAGS]`<br>
`CONFIG_NAME` - The name of the config in the `_configs` folder, to be tested.<br>
`PRESENTATION_FLAGS` - common presentation flags for the SparkCli.<br>
For more info on presentation flags, check the [manual].
<br><br>



## Examples
`php spark.php config-test-compiler`<br>
Test the default config.
<br><br>
`php spark.php config-test-compiler theme=DEFAULT`<br>
Test the default config with presentation flags
<br><br>
`php spark.php config-test-compiler -hb theme=DEFAULT`<br>
Test the default config with presentation flags
<br><br>
`php spark.php config-test-compiler -auto -hb theme=DEFAULT`<br>
Test the default config with presentation flags
<br><br>
`php spark.php config-test-compiler "my-test-config.php"`<br>
Test the selected config.
<br><br>
`php spark.php config-test-compiler "my-test-config.php" theme=DEFAULT`<br>
Test the selected config with presentation flags
<br><br>
`php spark.php config-test-compiler "my-test-config.php" -hb theme=DEFAULT`<br>
Test the selected config with presentation flags
<br><br>
`php spark.php config-test-compiler "my-test-config.php" -auto -hb theme=DEFAULT`<br>
Test the selected config with presentation flags
<br><br>