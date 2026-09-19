<?php
namespace SparkLib\Commands;
use Exception, Throwable;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . '/../Common/Basics.php';
    require_once __DIR__ . "/../Common/Validator.php";
    require_once __DIR__ . "/../Common/Compiler.php";
    use function SparkLib\Common\{catchEx, connectDb};
    use SparkLib\Common\Validator;
    use SparkLib\Common\Compiler;
	set_exception_handler('\SparkLib\Common\catchEx');

class Config
{
    //Make a local config to be used to connect to a Listiary backend
    //Make a test connection to the server before proceeding.
    //parameters['serverName', 'dbName', 'dbUser', 'dbPass']
    public static function Config_Make_Online(array $parameters): array {

        $log = "";
        try
        {
            //check input
            Validator::validateParameterExists($parameters, "serverName", "dbName", "dbUser", "dbPass");
            Validator::validateParametersNotEmpty($parameters, "serverName", "dbName", "dbUser", "dbPass");

            // get values
            $servername = $parameters["serverName"];
            $dbname = $parameters["dbName"];
            $username = $parameters["dbUser"];
            $password = $parameters["dbPass"];

            // Create connection
            try
            {
                $link = @mysqli_connect($servername, $username, $password, $dbname);
            }
            catch(Throwable $x)
            {
                $log .= "Values are NOT correct - we were not able to establish a connection to the server with the values you provided!" . NEW_LINE;
                $log .= "Connection failed: " . mysqli_connect_error() . NEW_LINE;
                $log .= "SCRIPT FAILED";
                return ["success" => false, "log" => $log, "result" => null];
            }

            // Check connection
            if (!$link)
            {
                $log .= "Ok - values are NOT correct - we were not able to establish a connection to the server with the values you provided!" . NEW_LINE;
                $log .= "Connection failed: " . mysqli_connect_error() . NEW_LINE;
                $log .= "SCRIPT FAILED";
                return ["success" => false, "log" => $log, "result" => null];
            }
            else
            {
                // Close connection
                mysqli_close($link);
                $log .= "Ok - values are correct - we were able to establish a connection to the server with the values you provided!" . NEW_LINE;
            }

            // Get config template
            $filename = __DIR__ . "\installer_templates\_config_template.php";
            if (!file_exists($filename))
            {
                $log .= "Config template file not found: $filename" . NEW_LINE;
                $log .= "SCRIPT FAILED";
                return ["success" => false, "log" => $log, "result" => null];
            }

            // Replace values
            $contents = file_get_contents($filename);
            $updated = str_replace("*SERVER_VALUE*", $servername, $contents);
            $updated = str_replace("*USERNAME_VALUE*", $username, $updated);
            $updated = str_replace("*PASSWORD_VALUE*", $password, $updated);
            $updated = str_replace("*DATABASE_NAME_VALUE*", $dbname, $updated);

            // return
            $log .= "Done. Config created and handed back." . NEW_LINE;
            $log .= "SCRIPT SUCCEEDED";
            return ["success" => true, "log" => $log, "result" => $updated];
        }
        catch (Throwable $ex) 
        {
            $log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
            $log .= "SCRIPT FAILED";
            return ["success" => false, "log" => $log, "result" => null];
        }
    }

    //Make a local config to be used to connect to a Listiary backend
    //Don't make a test connection to the server - trust the provided credentials instead.
    //parameters['serverName', 'dbName', 'dbUser', 'dbPass']
    public static function Config_Make_Offline(array $parameters): array {

        $log = "";
        try
        {
            //check input
            Validator::validateParameterExists($parameters, "serverName", "dbName", "dbUser", "dbPass");
            Validator::validateParametersNotEmpty($parameters, "serverName", "dbName", "dbUser", "dbPass");

            // get values
            $servername = $parameters["serverName"];
            $dbname = $parameters["dbName"];
            $username = $parameters["dbUser"];
            $password = $parameters["dbPass"];

            // Create connection
            try
            {
                $link = @mysqli_connect($servername, $username, $password, $dbname);
            }
            catch(Throwable $x)
            {
                $log .= "Values are NOT correct - we were not able to establish a connection to the server with the values you provided!" . NEW_LINE;
                $log .= "Connection failed: " . mysqli_connect_error() . NEW_LINE;
                $log .= "SCRIPT FAILED";
                return ["success" => false, "log" => $log, "result" => null];
            }

            // Get config template
            $filename = __DIR__ . "\installer_templates\_config_template.php";
            if (!file_exists($filename))
            {
                $log .= "Config template file not found: $filename" . NEW_LINE;
                $log .= "SCRIPT FAILED";
                return ["success" => false, "log" => $log, "result" => null];
            }

            // Replace values
            $contents = file_get_contents($filename);
            $updated = str_replace("*SERVER_VALUE*", $servername, $contents);
            $updated = str_replace("*USERNAME_VALUE*", $username, $updated);
            $updated = str_replace("*PASSWORD_VALUE*", $password, $updated);
            $updated = str_replace("*DATABASE_NAME_VALUE*", $dbname, $updated);

            // return
            $log .= "Done. Config created and handed back." . NEW_LINE;
            $log .= "SCRIPT SUCCEEDED";
            return ["success" => true, "log" => $log, "result" => $updated];
        }
        catch (Throwable $ex) 
        {
            $log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
            $log .= "SCRIPT FAILED";
            return ["success" => false, "log" => $log, "result" => null];
        }
    }

    //Test config values by makeing a test connection to the server
    //parameters['compilerUrl', 'serverName', 'dbName', 'dbUser', 'dbPass']
    public static function Config_Test(array $parameters): array {

        $log = "";
		try 
		{
            //check input
            Validator::validateParametersExist($parameters, "compilerUrl", "serverName", "dbName", "dbUser", "dbPass");
            Validator::validateParametersNotEmpty($parameters, "compilerUrl", "serverName", "dbName", "dbUser", "dbPass");

            // get values
            $compilerurl = $parameters["compilerUrl"];
            $servername = $parameters["serverName"];
            $dbname = $parameters["dbName"];
            $username = $parameters["dbUser"];
            $password = $parameters["dbPass"];

            // config info
            $log .= "Compiler URL: " . $compilerurl . NEW_LINE;
            $log .= "Server Name: " . $servername . NEW_LINE;
			$log .= "Database Name: " . $dbname . NEW_LINE;
            $log .= "Database User: " . $username . NEW_LINE;
            $log .= "Database Pass: ***********" . NEW_LINE . NEW_LINE;

            // check connection
            try
            {
                $link = @mysqli_connect($servername, $username, $password, $dbname);
                $log .= "Connection OK" . NEW_LINE;
            }
            catch(Throwable $x)
            {
                $log .= "Values are NOT correct - we were not able to establish a connection to the server with the values you provided!" . NEW_LINE;
                $log .= "Connection failed: " . mysqli_connect_error() . NEW_LINE;
                $log .= "SCRIPT FAILED";
                return ["success" => false, "log" => $log, "result" => null];
            }

            //read test
			$link->query("SELECT 1");
			$log .= "Read access OK" . NEW_LINE;
			
			//write test
			$link->query("CREATE TEMPORARY TABLE __perm_test (id INT)");
			$link->query("DROP TEMPORARY TABLE __perm_test");
			$log .= "Write access OK" . NEW_LINE;
			
			mysqli_close($link);
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => true];
        }
        catch (Throwable $ex) 
        {
            $log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
            $log .= "SCRIPT FAILED";
            return ["success" => false, "log" => $log, "result" => null];
        }
    }

    //Test the compiler url
    //parameters['compilerUrl']
    public static function Config_Test_Compiler(array $parameters): array {

        $log = "";
		try 
		{
            //check input
            Validator::validateParameterExists($parameters, "compilerUrl");
            Validator::validateParameterNotEmpty($parameters, "compilerUrl");

            // get values
            $compilerUrl = $parameters["compilerUrl"];

            // config info
            $log .= "Compiler URL: " . $compilerUrl . NEW_LINE . NEW_LINE;

            //generate .DS source test file
			$contents = "directives ->\n\n";
			$contents .= "  language-version <1.0>,\n";
			$contents .= "  namespace <test.compiler>,\n";
			$contents .= "  filename <test.compiler.ds>,\n";
			$contents .= "  authors <ADD_AUTHOR>;\n\n\n";
			$contents .= "Test list (Hello Describe Compiler) <.rnode> ->\n\n";
			$contents .= "  ITEM1,\n";
			$contents .= "  ITEM2;";

			//test Compiler
			$url = $compilerUrl;
			$code = $contents;
			$filename = "test.compiler";

            //get result
            $result = Compiler::doPostRequest($url, $code, $filename);
            
            //check response
			if ($result == null)
			{
				$log .= "Parser response is NULL" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
            
            // Decode response JSON
    		$jArr = json_decode($result, true);
			
			//report
			$data["json_result"] = $result;
			$log .= "----- RAW RESULT -----" . NEW_LINE;
			$log .= json_encode($jArr) . NEW_LINE;
			$log .= "----------------------" . NEW_LINE . NEW_LINE;
            
            // Check if decoding failed
			if (!is_array($jArr))
			{
				$log .= "ERROR: Failed to parse JSON" . NEW_LINE;
				$log .= "--- RAW RESPONSE ---" . NEW_LINE;
				$log .= $result . NEW_LINE;
				$log .= "--------------------" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

            // Check if "Output" is missing
			if (!isset($jArr["Output"])) 
			{
				$log .= "ERROR: 'Output' is missing from response" . NEW_LINE;
				$log .= "--- RAW RESPONSE ---" . NEW_LINE;
				$log .= $result . NEW_LINE;
				$log .= "--------------------" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			$log .= "Ok - JSON has an 'Output' item." . NEW_LINE;

			// Check if "Result" is missing
			if (!isset($jArr["Result"])) 
			{
				$log .= "ERROR: 'Result' is missing from response" . NEW_LINE;
				$log .= "--- RAW RESPONSE ---" . NEW_LINE;
				$log .= $result . NEW_LINE;
				$log .= "--------------------" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			$log .= "Ok - JSON has an 'Result' item." . NEW_LINE;

    		// Compare result safely using double quotes around array key
    		if (strtolower($jArr["Result"]) !== "success")
			{
				$log .= "ERROR: Result is " . $jArr["Result"] . NEW_LINE;
				$log .= "--- RAW RESPONSE ---" . NEW_LINE;
				$log .= $result . NEW_LINE;
				$log .= "--------------------" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			$log .= "Ok - JSON 'Result' is 'success'." . NEW_LINE . NEW_LINE;
            
            // Decode the compiled output
			$compiled64 = $jArr["Output"];
			$compiledJson = base64_decode($compiled64);
			$log .= "----- COMPILED FILE DECODED -----" . NEW_LINE;
			$log .= json_encode($compiledJson) . NEW_LINE;
			$log .= "----------------------" . NEW_LINE . NEW_LINE;

			//return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => true];
        }
        catch (Throwable $ex) 
        {
            $log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
            $log .= "SCRIPT FAILED";
            return ["success" => false, "log" => $log, "result" => null];
        }
    }
}