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


class file
{
    //Output the contents of a single file
    //parameters['fileName']
    public static function File_Fetch(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "fileName");
            Validator::validateParameterIsCanonicalDsName($parameters, "fileName");

            //get input
            $fileName = $parameters["fileName"];

            $nameEscaped = mysqli_real_escape_string($link, $fileName);
			$sql = "SELECT `filename`, `content` FROM `describe_documents` WHERE `filename` = '$nameEscaped';";
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

    //Delete a single file
    //parameters['fileName']
    public static function File_Delete(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "fileName");
            Validator::validateParameterIsCanonicalDsName($parameters, "fileName");

            //get input
            $fileName = $parameters["fileName"];

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

    //Upload given data representing a Describe file to the database
    //parameters['fileName', 'fileContent']
    public static function File_Put(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParametersExist($parameters, "fileName", "fileContent");
            Validator::validateParameterIsCanonicalDsName($parameters, "fileName");
            Validator::validateParameterIsStringInRange($parameters, "fileContent", 3, 500000);
            Validator::validateParameterIsUtf8String($parameters, "fileContent");

            //get input
            $fileName = $parameters["fileName"];
            $fileContent = $parameters["fileContent"];

            //put file contents
            $nameEscaped = mysqli_real_escape_string($link, $fileName);
			$contentEscaped = mysqli_real_escape_string($link, $fileContent);
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

    //Compile a single file in the database to JSON
    //parameters['fileName']
    public static function File_Compile(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "fileName");
            Validator::validateParameterIsCanonicalDsName($parameters, "fileName");

            //get input
            $fileName = $parameters["fileName"];

            // fetch the file contents
    		$entry_safe = mysqli_real_escape_string($link, $fileName);
    		$sql = "SELECT `content` FROM `describe_documents` WHERE `filename` = '$entry_safe'";
    		$result = mysqli_query($link, $sql);

            // check result
			if (mysqli_num_rows($result) <= 0)
			{
				$log .= "No entries with filename '{$entry_safe}'" . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

            // get data
    		$row = mysqli_fetch_assoc($result);
    		$content = $row["content"];
    		$log .= "Fetching file - OK (" . strlen($content) . ") characters long." . NEW_LINE;

			// compile data
			$result = Compiler::doPostRequest(COMPILER_URL, $content, $fileName);
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
				VALUES ('$entry_safe', '$content_safe', NOW())
				ON DUPLICATE KEY UPDATE
				content = VALUES(content),
				submitted_at = NOW();";
			if (!mysqli_query($link, $sql))
			{
				$log .= "Database error: " . mysqli_error($conn) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			else
			{
				//output
				$data["filename"] = $entry_safe;
				$data["content"] = $content_safe;

				$log .= "Document saved - Ok." . NEW_LINE;
				$log .= "SCRIPT SUCCEEDED";
				return ["success" => true, "log" => $log, "result" => $data];
			}
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }



    //List the files in the database
	//Set maxfiles to > 0 if you need to limit the number of files fetched
    //parameters['max']
    public static function Files_List(mysqli $link, array $parameters): array {

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
			$sql = "SELECT `filename` FROM `describe_documents`";
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

    //Count the files in the database
    public static function Files_Count(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            // Count files in database
			$sql = "SELECT COUNT(*) AS total_files FROM `describe_documents`";
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

    //Download Describe files from the database
    //Set maxfiles to > 0 if you need to limit the number of files fetched
    //parameters['max']
    public static function Files_Download(mysqli $link, array $parameters): array {

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
			$sql = "SELECT `filename`, `content` FROM `describe_documents`";
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

    //Compile all Describe files in the database
    //thus populating the JSONs table in the database
    //'$logger' is a function that can log output without added new lines
    public static function Files_Compile(mysqli $link, ?callable $logger = null): array {

        $log = "";
		$data = [];
        try
        {
            // Get entries
			$sql = "SELECT `content`, `filename` FROM `describe_documents`;";
			$filesFetchResult = mysqli_query($link, $sql);

			// Check if the query failed
			if (!$filesFetchResult) 
			{
				$log .= "SQL Error: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

			// Check result
			if (mysqli_num_rows($filesFetchResult) <= 0)
			{
				$log .= "No entries fetched." . NEW_LINE;
				$log .= "SCRIPT SUCCEEDED";
				mysqli_close($link);
				return ["success" => true, "log" => $log, "result" => $data];
			}

			// Iterate through rows
            if($logger != null) $logger("--- IMMEDIATE OUTPUT ---" . NEW_LINE);
			while ($row = mysqli_fetch_assoc($filesFetchResult))
			{
				$filename = $row['filename'];
        		$content = $row['content'];

				//compile data
				$log .= "Compiling $filename... ";
                if($logger != null) $logger("Compiling $filename... ");
				$result = Compiler::doPostRequest(COMPILER_URL, $content, $filename);
				//Compiler::logPostResponse($result);

				//check response
				if ($result == null)
				{
                    if($logger != null) $logger("XX" . NEW_LINE);
					$log .= "ERROR: Response is NULL" . NEW_LINE;
					continue;
				}

				// Decode response JSON
    			$jArr = json_decode($result, true);

				// Check if decoding failed
				if (!is_array($jArr))
				{
					if($logger != null) $logger("XX" . NEW_LINE);
					$log .= "ERROR: Failed to parse JSON" . NEW_LINE;
					$log .= "--- RAW RESPONSE ---" . NEW_LINE;
					$log .= $result . NEW_LINE;
					$log .= "--------------------" . NEW_LINE;
					continue;
				}

				// Check if "Output" is missing
				if (!isset($jArr["Output"])) 
				{
					if($logger != null) $logger("XX" . NEW_LINE);
					$log .= "ERROR: 'Output' is missing from response" . NEW_LINE;
					$log .= "--- RAW RESPONSE ---" . NEW_LINE;
					$log .= $result . NEW_LINE;
					$log .= "--------------------" . NEW_LINE;
					continue;
				}

				// Check if "Result" is missing
				if (!isset($jArr["Result"])) 
				{
					if($logger != null) $logger("XX" . NEW_LINE);
					$log .= "ERROR: 'Result' is missing from response" . NEW_LINE;
					$log .= "--- RAW RESPONSE ---" . NEW_LINE;
					$log .= $result . NEW_LINE;
					$log .= "--------------------" . NEW_LINE;
					continue;
				}

				// Compare result, safely using double quotes around array key
				if (strtolower($jArr["Result"]) !== "success")
				{
					if($logger != null) $logger("XX" . NEW_LINE);
					$log .= "ERROR: Result is " . $jArr["Result"] . NEW_LINE;
					$log .= "--- RAW RESPONSE ---" . NEW_LINE;
					$log .= $result . NEW_LINE;
					$log .= "--------------------" . NEW_LINE;
					continue;
				}

				// Decode the compiled output
				$compiled64 = $jArr["Output"];
				$compiledJson = base64_decode($compiled64);
				//if($logger != null) $logger($compiledJson);

				// Upsert document
				$entry_safe = mysqli_real_escape_string($link, $filename);
				$content_safe = mysqli_real_escape_string($link, $compiledJson);
				$sql = "INSERT INTO `compiled_documents` (filename, content, submitted_at)
					VALUES ('$entry_safe', '$content_safe', NOW())
					ON DUPLICATE KEY UPDATE
					content = VALUES(content),
					submitted_at = NOW();";
				if (!mysqli_query($link, $sql))
				{
					if($logger != null) $logger("XX" . NEW_LINE);
					$log .= "Database error: " . mysqli_error($conn) . NEW_LINE;
				}
				else
				{
					//output
					$data[] =
					[
						"filename" => $entry_safe,
						"content"  => $content_safe
					];

                    if($logger != null) $logger("Ok" . NEW_LINE);
					$log .= "Ok." . NEW_LINE;
				}
			}

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

    //Delete all the Describe files from the database
    public static function Files_Delete(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            // Delete files in database
			$sql = "DELETE FROM `describe_documents`";
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

    //Upload given data representing Describe files to the database
    //parameters['files'][FILENAME][FILETEXT]
    public static function Files_Upload(mysqli $link, array $parameters): array {
        
        $log = "";
        $data = [];
        try 
        {
            //validate input
            Validator::validateParameterExists($parameters, "files");

            // check data
            $minLength = 3;
            $maxLength = 500000; // ~500KB or approx 150-200 pages of code
            $processedCount = 0;
            foreach ($parameters['files'] as $filename => $fileContents)
            {
                $isInvalid = false;
                if (!is_string($fileContents))
                {
                    $log .= "Invalid content format: Expected a string." . NEW_LINE;
                    $isInvalid = true;
                } 
                elseif (strlen($fileContents) < $minLength) 
                {
                    $log .= "File content is too short (Minimum {$minLength} characters)." . NEW_LINE;
                    $isInvalid = true;
                } 
                elseif (strlen($fileContents) > $maxLength) 
                {
                    $log .= "File content is too large (Maximum {$maxLength} characters)." . NEW_LINE;
                    $isInvalid = true;
                }
                if ($isInvalid) 
                {
                    $log .= "SCRIPT FAILED";
                    return ["success" => false, "log" => $log, "result" => null];
                }
            }

            // Start the transaction
            mysqli_begin_transaction($link);

            // Upload files
            $processedCount = 0;
            foreach ($parameters['files'] as $filename => $fileContents)
            {
                // Put file contents
                $nameEscaped = mysqli_real_escape_string($link, $filename);
                $contentEscaped = mysqli_real_escape_string($link, $fileContents);
                $sql = "INSERT INTO `describe_documents` (`filename`, `content`) VALUES ('$nameEscaped', '$contentEscaped')";

                // Check result
                $result = mysqli_query($link, $sql);
                if (!$result)
                {
                    if (mysqli_errno($link) == 1062) 
                    {
                        $log .= "Error: A file named '{$filename}' already exists in the database." . NEW_LINE;
                    } 
                    else 
                    {
                        $log .= "Error uploading file '{$filename}': " . mysqli_error($link) . NEW_LINE;
                    }
                    
                    // Rollback transaction on SQL failure
                    mysqli_rollback($link);
                    $log .= "Transaction reverted - 0 entries changed." . NEW_LINE;
                    $log .= "SCRIPT FAILED";
                    return ["success" => false, "log" => $log, "result" => null];
                }
                else
                {
                    $log .= "Success: File '{$filename}' queued for commit." . NEW_LINE;
                    $processedCount++;
                }
            }

            // If the loop finishes without returning, commit the transaction
            mysqli_commit($link);
            
            if($processedCount < 1) $log .= "{$processedCount} files uploaded." . NEW_LINE;
            else $log .= "All {$processedCount} file(s) uploaded successfully." . NEW_LINE;
            $log .= "SCRIPT SUCCEEDED." . NEW_LINE;
            return ["success" => true, "log" => $log, "result" => true];

        }
        catch (Throwable $ex)
        {
            // Attempt to rollback if a hard exception breaks the flow mid-transaction
            mysqli_rollback($link);
            
            $log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
            $log .= "Transaction reverted - 0 entries changed." . NEW_LINE;
            $log .= "SCRIPT FAILED";
            return ["success" => false, "log" => $log, "result" => null];
        }
    }
}