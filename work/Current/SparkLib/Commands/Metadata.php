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


class Metadata
{
    //Output the contents of a single metadata entry
    //FF metadata stands for File to Files - describes what other files each file is related to
    //parameters['fileName']
    public static function Metadata_Fetch_FF(mysqli $link, array $parameters): array {

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
			$sql = "SELECT `filename`, `related_filename` FROM `housekeeping_filename_related` WHERE `filename` = '$nameEscaped';";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching file: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}

            //report
			$count = 0;
			$log .= NEW_LINE . "----------------------------" . NEW_LINE;
			$log .= "{$nameEscaped} ->" . NEW_LINE . NEW_LINE;
			while ($row = mysqli_fetch_assoc($result)) 
			{
				$related_filename = $row['related_filename'];
				$data[] = $related_filename;
				if($count > 0) $log .= "," . NEW_LINE;
				$log .= "    " . $related_filename;
				$count++;
			}
			$log .= ";" . NEW_LINE . "----------------------------" . NEW_LINE . NEW_LINE;
			
			//return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => false, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Output the contents of a single metadata entry
	//FI metadata stands for File to item Ids - describes what files contain each public item Id
    //parameters['fileName']
    public static function Metadata_Fetch_FI(mysqli $link, array $parameters): array {

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
			$sql = "SELECT `filename`, `item_id` FROM `housekeeping_itemid_filename` WHERE `filename` = '$nameEscaped';";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching file name: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			//report
			$count = 0;
			$log .= NEW_LINE . "----------------------------" . NEW_LINE;
			$log .= "{$nameEscaped}(.ds) ->" . NEW_LINE . NEW_LINE;
			while ($row = mysqli_fetch_assoc($result)) 
			{
				$item_id = $row['item_id'];
				$data[] = $item_id;
				if($count > 0) $log .= "," . NEW_LINE;
				$log .= "    " . $item_id;
				$count++;
			}
			$log .= ";" . NEW_LINE . "----------------------------" . NEW_LINE . NEW_LINE;
			
			//return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => false, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Output the contents of a single metadata entry
    //IF metadata stands for item Id to Files - describes what files contain each public item Id
    //parameters['itemId']
    public static function Metadata_Fetch_IF(mysqli $link, array $parameters): array {

        $log = "";
		$data = [];
        try
        {
            //validate input
            Validator::validateParameterExists($parameters, "itemId");
            Validator::validateParameterIsCanonicalDsName($parameters, "itemId");

            //get input
            $itemId = $parameters["itemId"];

            //get file contents
			$idEscaped = mysqli_real_escape_string($link, $itemId);
			$sql = "SELECT `filename`, `item_id` FROM `housekeeping_itemid_filename` WHERE `item_id` = '$idEscaped';";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching item id: " . mysqli_error($link) . NEW_LINE;
				$log .= "SCRIPT FAILED";
				return ["success" => false, "log" => $log, "result" => null];
			}
			
			//report
			$count = 0;
			$log .= NEW_LINE . "----------------------------" . NEW_LINE;
			$log .= "{$idEscaped} ->" . NEW_LINE . NEW_LINE;
			while ($row = mysqli_fetch_assoc($result)) 
			{
				$filename = $row['filename'];
				$data[] = $filename;
				if($count > 0) $log .= "(.ds)," . NEW_LINE;
				$log .= "    " . $filename;
				$count++;
			}
			$log .= "(.ds);" . NEW_LINE . "----------------------------" . NEW_LINE . NEW_LINE;
			
			//return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => false, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }
}