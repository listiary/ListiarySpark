<?php
namespace SparkLib\Commands;
use Exception, Throwable, mysqli;

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


class Action
{
    //Delete an article from the database
    //parameters['fileName']
    public static function Action_DeleteArticle(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "fileName");
            Validator::validateParameterIsCanonicalDsName($parameters, "fileName");

			//get input
            $fileName = $parameters["fileName"];
            
            //get file contents
			$nameEscaped = mysqli_real_escape_string($link, $fileName);
			$sql = "DELETE FROM `describe_documents` WHERE `filename` = '$nameEscaped';";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error deleting file: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			//report
			$deletedRows = mysqli_affected_rows($link);
			if ($deletedRows === 0)
			{
				$log .= "No file named '$nameEscaped' was found in the database." . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

            //Delete JSON
			$sql = "DELETE FROM `compiled_documents` WHERE `filename` = '$nameEscaped';";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error deleting file: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			$deletedRows = mysqli_affected_rows($link);
			$log .= "Ok - deleted $deletedRows rows." . NEW_LINE;
			
			//report
			$deletedRows = mysqli_affected_rows($link);
			if ($deletedRows === 0)
			{
				$log .= "No file named '$nameEscaped' was found in the database." . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			else
			{
				$log .= "Ok - deleted $deletedRows rows." . NEW_LINE;
				$log .= "SCRIPT SUCCEEDED";
				return ["success" => false, "log" => $log, "result" => $deletedRows];
			}            
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Create a stub file and upload and compile it
    //parameters['item', 'namespace', 'isrnode', 'author']
    public static function Action_CreateStub(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParametersExist($parameters, "item", "namespace");
            Validator::validateParametersNotEmpty($parameters, "item", "namespace");
            Validator::validateParameterIsCanonicalDsName($parameters, "namespace");

            //get params
            $item = $parameters['item'];
            $namespace = $parameters['namespace'];
            $fileName = $namespace;
			$author = "ADD_AUTHOR";
			$isrnode = false;
			if(Validator::parameterExists($parameters, 'author'))
				if(!Validator::isStringNullOrEmpty($parameters['author']))
					$author = $parameters['author'];
			if(Validator::parameterExists($parameters, 'isrnode'))
				$isrnode = $parameters['isrnode'];

            //escape params
            $itemEscaped = mysqli_real_escape_string($link, $item);
            $nameEscaped = mysqli_real_escape_string($link, $namespace);
            
            //generate .DS source file
			$lastDotPos = strrpos($nameEscaped, '.');
			if($isrnode || $lastDotPos === false)
			{
				$contents = "directives ->\n\n";
				$contents .= "  language-version <1.0>,\n";
				$contents .= "  namespace <{$nameEscaped}>,\n";
				$contents .= "  filename <{$nameEscaped}.ds>,\n";
				$contents .= "  authors <{$author}>;\n\n\n";
				$contents .= $itemEscaped . " <.rnode> ->\n\n";
				$contents .= "  ITEM1,\n";
				$contents .= "  ITEM2;";
			}
			else
			{
				$beforeDot = substr($nameEscaped, 0, $lastDotPos);
				$afterDot = substr($nameEscaped, $lastDotPos + 1);
				$contents = "directives ->\n\n";
				$contents .= "  language-version <1.0>,\n";
				$contents .= "  namespace <{$beforeDot}>,\n";
				$contents .= "  filename <{$nameEscaped}.ds>,\n";
				$contents .= "  authors <{$author}>;\n\n\n";
				$contents .= $itemEscaped . " <.{$afterDot}> ->\n\n";
				$contents .= "  ITEM1,\n";
				$contents .= "  ITEM2;";
			}

            //put file contents
			$contentEscaped = mysqli_real_escape_string($link, $contents);
			$sql = "INSERT INTO `describe_documents` (`filename`, `content`) VALUES ('$nameEscaped', '$contentEscaped')";

            //check result
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				if (mysqli_errno($link) == 1062)
				{
					$log .= "Error: A file named '{$fileName}' already exists in the database." . NEW_LINE;
				}
				else
				{
					$log .= "Error uploading file: " . mysqli_error($link) . NEW_LINE;
				}
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

            //compile the file
			$result = Compiler::doPostRequest(COMPILER_URL, $contents, $fileName);
            //Compiler::logPostResponse($result);

            //check response
			if ($result == null)
			{
				$log .= "Parser response is NULL" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			// Decode response JSON
    		$jArr = json_decode($result, true);

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

    		// Compare result, safely using double quotes around array key
    		if (strtolower($jArr["Result"]) !== "success")
			{
				$log .= "ERROR: Result is " . $jArr["Result"] . NEW_LINE;
				$log .= "--- RAW RESPONSE ---" . NEW_LINE;
				$log .= $result . NEW_LINE;
				$log .= "--------------------" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

			// Decode the compiled output
			$compiled64 = $jArr["Output"];
			$compiledJson = base64_decode($compiled64);
			//echo $compiledJson;
			
			// Upsert document
			$content_safe = mysqli_real_escape_string($link, $compiledJson);
			$sql = "INSERT INTO `compiled_documents` (filename, content, submitted_at)
				VALUES ('$nameEscaped', '$content_safe', NOW())
				ON DUPLICATE KEY UPDATE
				content = VALUES(content),
				submitted_at = NOW();";
				
			//check result
			if (!mysqli_query($link, $sql))
			{
				$log .= "Database error: " . mysqli_error($conn) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			
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

	//Create a source file and upload and compile it. Ask user for the contents
	//parameters['item', 'namespace', 'text', 'isrnode', 'author']
	//public static function Action_CreateArticle(mysqli $link, array $parameters): array

    //action_show_article_namespace
    //action_rename_namespace
    //action_rename_article
    //action_list_articles_in_namespace
    //action_find_similar_articles
    //action_edit_article
    //action_download_articles_in_namespace
}