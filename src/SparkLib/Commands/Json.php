<?php
namespace SparkLib\Commands;
use Exception, Throwable, mysqli;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . '/../Common/Basics.php';
    require_once __DIR__ . "/../Common/Validator.php";
    use function SparkLib\Common\{catchEx, connectDb};
    use SparkLib\Common\Validator;
	set_exception_handler('\SparkLib\Common\catchEx');


class Json
{
    //Output the contents of a single JSON file
    //parameters['jsonName']
    public static function Json_Fetch(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "jsonName");
            Validator::validateParameterIsCanonicalDsName($parameters, "jsonName");

            //get input
            $jsonName = $parameters["jsonName"];

            $nameEscaped = mysqli_real_escape_string($link, $jsonName);
			$sql = "SELECT `filename`, `content` FROM `compiled_documents` WHERE `filename` = '$nameEscaped';";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching file: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

            //report
			if ($row = mysqli_fetch_assoc($result))
			{
				//fetch
				$filename = $row['filename'];
				$content  = $row['content'];
				
				//output
				$data["filename"] = $filename;
				$data["content"] = $content;

				//return
				$log .= "SCRIPT SUCCEEDED";
				return ["success" => false, "log" => $log, "result" => $data];
			}
			else
			{
				$log .= "File '$nameEscaped' not found in database." . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Delete a single JSON file
    //parameters['jsonName']
    public static function Json_Delete(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "jsonName");
            Validator::validateParameterIsCanonicalDsName($parameters, "jsonName");

            //get input
            $jsonName = $parameters["jsonName"];

            $nameEscaped = mysqli_real_escape_string($link, $jsonName);
            $sql = "DELETE FROM `compiled_documents` WHERE `filename` = '$nameEscaped';";
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

    //Upload given data representing a JSON file to the database
    //parameters['jsonName', 'jsonContent']
    public static function Json_Put(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "jsonName", "jsonContent");
            Validator::validateParameterIsCanonicalDsName($parameters, "jsonName");
            Validator::validateParameterIsStringInRange($parameters, "jsonContent", 3, 500000);
            Validator::validateParameterIsUtf8String($parameters, "jsonContent");

            //get input
            $jsonName = $parameters["jsonName"];
            $jsonContent = $parameters["jsonContent"];

            //put file contents
            $nameEscaped = mysqli_real_escape_string($link, $jsonName);
			$contentEscaped = mysqli_real_escape_string($link, $jsonContent);
			$sql = "INSERT INTO `compiled_documents` (`filename`, `content`) VALUES ('$nameEscaped', '$contentEscaped')";

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
			else
			{
				//return
				$log .= "SCRIPT SUCCEEDED";
				return ["success" => true, "log" => $log, "result" => true];
			}
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }



    //List the JSON files in the database
	//Set maxfiles to > 0 if you need to limit the number of files fetched
    //parameters['max']
    public static function Jsons_List(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "max");
            Validator::validateParameterIsIntInRange($parameters, "max", -1, 9999999);

            //get input
            $maxFiles = (int)$parameters["max"];

            // Fetch files from database
			$sql = "SELECT `filename` FROM `compiled_documents`";
			if ($maxFiles > 0)
			{
				$sql .= " LIMIT $maxFiles";
			}
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching filenames: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			 // Show results
			$log .=  "File listing:" . NEW_LINE;
			$log .= NEW_LINE . NEW_LINE;
			while ($row = mysqli_fetch_assoc($result)) 
			{
				$filename = $row['filename'];
				$data[] = $filename;
				$log .= $filename . NEW_LINE;
			}
			$log .= NEW_LINE . NEW_LINE;
			
			// return
			$log .= "SCRIPT SUCCEEDED";
            return ["success" => true, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Count the JSON files in the database
    public static function Jsons_Count(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            // Count files in database
			$sql = "SELECT COUNT(*) AS total_files FROM `compiled_documents`";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching file count: " . mysqli_error($link) . NEW_LINE;
				return ["success" => false, "log" => $log, "result" => null];
			}

			// Fetch the single row containing our count
			$row = mysqli_fetch_assoc($result);
			$count = (int)$row['total_files'];
			$log .= "Total files counted: " . $count . NEW_LINE;

			// return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => $count];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Download JSON files from the database
    //Set maxfiles to > 0 if you need to limit the number of files fetched
    //parameters['max']
    public static function Jsons_Download(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "max");
            Validator::validateParameterIsIntInRange($parameters, "max", -1, 9999999);
            
            //get input
            $maxFiles = (int)$parameters["max"];

            // Fetch files from database
			$sql = "SELECT `filename`, `content` FROM `compiled_documents`";
			if ($maxFiles > 0)
			{
				$sql .= " LIMIT $maxFiles";
			}
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching files: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			//get results
			$count = 0;
			while ($row = mysqli_fetch_assoc($result)) 
			{
				$filename = $row['filename'];
				$content = $row['content'];
				$data[] = ["filename" => $filename, "content"  => $content ];
				$count++;
			}
			$log .=  "Ok - fetched {$count} files." . NEW_LINE;
	
			// return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Delete all the JSON files from the database
    public static function Jsons_Delete(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            // Delete files in database
			$sql = "DELETE FROM `compiled_documents`";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error deleting files: " . mysqli_error($link) . NEW_LINE;
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			// Get the number of rows that were successfully deleted
			$deleted_count = mysqli_affected_rows($link);
			$log .= "Successfully deleted $deleted_count files." . NEW_LINE;

			// return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => $deleted_count];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }
}