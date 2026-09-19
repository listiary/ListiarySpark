<?php
namespace SparkLib\Commands;
use Exception, Throwable, mysqli;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . '/../Common/Basics.php';
    require_once __DIR__ . "/../Common/Validator.php";
    require_once __DIR__ . "/File.php";
    use function SparkLib\Common\{catchEx, connectDb};
    use SparkLib\Common\Validator;
    use SparkLib\Commands\File;
	set_exception_handler('\SparkLib\Common\catchEx');


class Database
{
    //Test if database is working and empty for Listiary installation
    public static function Database_Test(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            //read test
			$link->query("SELECT 1");
			$log .= "Read access OK" . NEW_LINE;

            //write test
			$link->query("CREATE TEMPORARY TABLE __perm_test (id INT)");
			$link->query("DROP TEMPORARY TABLE __perm_test");
			$log .= "Write access OK" . NEW_LINE;
			
			//db empty test
			$isEmpty = false;
			$result = $link->query("SHOW TABLES");
			if ($result->num_rows === 0) 
			{
				$log .= "Database is EMPTY - OK" . NEW_LINE;
				$isEmpty = true;
			} 
			else 
			{
				$log .= "Database has " . $result->num_rows . " tables." . NEW_LINE;
				$log .= "Fail - you need an empty database to proceed" . NEW_LINE;
				$isEmpty = false;
			}

            //return
			if($isEmpty) 
			{
				$log .= "SCRIPT SUCCEEDED";
				return ["success" => true, "log" => $log, "result" => true];
			}
			else 
			{
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

    //Make the tables in an empty database, installing a wiki instance
    public static function Database_MakeTables(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            //do
            $sqlRelativePath = "/installer_sqls";
			$files = 
			[
				__DIR__ . $sqlRelativePath . "/init-accounts.sql",
				__DIR__ . $sqlRelativePath . "/init-history.sql",
				__DIR__ . $sqlRelativePath . "/init-housekeeping.sql",
				__DIR__ . $sqlRelativePath . "/init-main.sql",
				__DIR__ . $sqlRelativePath . "/init-permissions.sql",
			];
			foreach ($files as $file)
			{
				$log .= self::runSqlFile($link, $file);
			}

			// Return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => true];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;

			// PANIC: Drop everything from the database
			// DDL statements (like CREATE TABLE) cannot be rolled back via transactions.
			// This blunt tool manually fetches and drops every table and view to reset the DB.
			if (isset($link) && $link instanceof mysqli)
			{
				try 
				{
					$log .= "Initiating panic cleanup..." . NEW_LINE;
					
					// Disable foreign key checks so tables drop without relationship errors
					$link->query("SET FOREIGN_KEY_CHECKS = 0");
					
					// Drop all tables
					$result = $link->query("SHOW TABLES");
					if ($result)
					{
						while ($row = $result->fetch_array()) 
						{
							$tableName = $row[0];
							$link->query("DROP TABLE IF EXISTS `$tableName`");
						}
					}

					// Drop all views (in case your SQLs create any)
					$result = $link->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
					if ($result)
					{
						while ($row = $result->fetch_array()) 
						{
							$viewName = $row[0];
							$link->query("DROP VIEW IF EXISTS `$viewName`");
						}
					}

					// Re-enable foreign key checks
					$link->query("SET FOREIGN_KEY_CHECKS = 1");
					$log .= "Cleanup complete: Database completely wiped." . NEW_LINE;
				}
				catch (Throwable $cleanupEx)
				{
					$log .= "CRITICAL: Cleanup failed! DB might be in a partial state. " . $cleanupEx->getMessage() . NEW_LINE;
				}
			}
			
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
		}
    }

    //Nuke a database, deleting everything from it.
    public static function Database_Wipe(mysqli $link): array {

        $log = "";
		$data = [];
        try
        {
            // Disable foreign key checks so tables drop without relationship errors
            $link->query("SET FOREIGN_KEY_CHECKS = 0");
					
			// Drop all tables
			$result = $link->query("SHOW TABLES");
			if ($result)
			{
				while ($row = $result->fetch_array()) 
				{
					$tableName = $row[0];
					$link->query("DROP TABLE IF EXISTS `$tableName`");
					$log .=  "DROP TABLE IF EXISTS executed on " . $tableName . NEW_LINE;
				}
			}

			// Drop all views (in case your SQLs create any)
			$result = $link->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
			if ($result)
			{
				while ($row = $result->fetch_array()) 
				{
					$viewName = $row[0];
					$link->query("DROP VIEW IF EXISTS `$viewName`");
					$log .=  "DROP VIEW IF EXISTS executed on " . $viewName  . NEW_LINE;
				}
			}

			// Re-enable foreign key checks
			$link->query("SET FOREIGN_KEY_CHECKS = 1");
			$log .=  "Nuking complete - Database completely wiped." . NEW_LINE;

			// Return
			$log .=  "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => true];   
        }
        catch (Throwable $ex) 
		{
			$log .=  "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .=  "Cleanup failed - Database might be in a partial state." . NEW_LINE;
			$log .=  "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
        }
    }



    //Compile all Describe files in the database, populating the JSONs table
    //'$logger' is a function that can log output without added new lines
    public static function Database_Recompile(mysqli $link, ?callable $logger = null): array {

        return File::Files_Compile($link, $logger);
    }

    //Generate the metadata for all the files, populating the 2 tables
    //'$logger' is a function that can log output without added new lines
    public static function Database_Curate(mysqli $link, ?callable $logger = null): array {

        $log = "";
		$data = [];
        try
        {
			// Get entries
			$sql = "SELECT `content`, `filename` FROM `compiled_documents`;";
    		$filesFetchResult = mysqli_query($link, $sql);

			// Check result
			if (!$filesFetchResult) 
			{
				$log .= "Error fetching files: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}
			if (mysqli_num_rows($filesFetchResult) <= 0)
			{
				$log .= "No entries fetched." . NEW_LINE;
				return ["success" => false, "log" => $log, "result" => null];
			}

			//empty table
			$sql = "TRUNCATE TABLE housekeeping_itemid_filename";
			if (!mysqli_query($link, $sql))
			{
				$log .= "Error: " . mysqli_error($link) . NEW_LINE;
				if($logger != null) $logger("XX (failed)" . NEW_LINE);
			}
			if($logger != null) $logger("Empty table - Ok." . NEW_LINE);
			$log .= "Empty table - Ok." . NEW_LINE;

			// Iterate through rows
			if($logger != null) $logger("--- IMMEDIATE OUTPUT ---" . NEW_LINE);
			while ($row = mysqli_fetch_assoc($filesFetchResult))
			{
				$filename = $row['filename'];
        		$content = $row['content'];
				if($logger != null) $logger($filename . "... ");

				// Extract IDs
				$jArr = json_decode($content, true);
				$ids = [];
				self::extractIds($jArr, $ids);
				$ids = array_keys($ids);

				// Log count
				$length = count($ids);
				$log .= "'{$filename}' has {$length} public ids." . NEW_LINE;
				//print_r($ids); var_dump($ids); break;

				// Upload the data
				$filename_safe = mysqli_real_escape_string($link, $filename);
				$values = [];
				foreach ($ids as $id) 
				{
					$item_id_safe = mysqli_real_escape_string($link, $id);
					$values[] = "('$filename_safe', '$item_id_safe')";
				}
				if (!empty($values))
				{
					$valueString = implode(", ", $values);
					$data[] = $valueString;
					$query = "REPLACE INTO housekeeping_itemid_filename (filename, item_id) VALUES " . $valueString;
					//$query = "INSERT INTO housekeeping_itemid_filename (filename, item_id) VALUES " 
					//. $valueString . " ON DUPLICATE KEY UPDATE filename = VALUES(filename)";

					if (!mysqli_query($link, $query))
					{
						$log .= "Error: " . mysqli_error($link) . NEW_LINE;
						if($logger != null) $logger("XX (failed)" . NEW_LINE);
					}
					else
					{
						$log .= "Inserted " . mysqli_affected_rows($link) . " rows." . NEW_LINE;
						if($logger != null) $logger("Ok (" . mysqli_affected_rows($link) . " rows)" . NEW_LINE);
					}
				}
				else
				{
					if($logger != null) $logger("Ok (0 rows)" . NEW_LINE);
				}
			}

			//Curate filenames
			// Get entries
			$sql = "SELECT DISTINCT `filename` FROM `housekeeping_itemid_filename`;";
    		$filesFetchResult = mysqli_query($link, $sql);

			// Check result
			if (!$filesFetchResult) 
			{
				$log .= "Error fetching files: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}
			if (mysqli_num_rows($filesFetchResult) <= 0)
			{
				$log .= "No entries fetched." . NEW_LINE;
				return ["success" => false, "log" => $log, "result" => null];
			}

			//empty table
			$sql = "TRUNCATE TABLE housekeeping_filename_related";
			if (!mysqli_query($link, $sql))
			{
				$log .= "Error: " . mysqli_error($link) . NEW_LINE;
				if($logger != null) $logger("XX (failed)" . NEW_LINE);
			}
			if($logger != null) $logger("Empty table - Ok." . NEW_LINE);
			$log .= "Empty table - Ok." . NEW_LINE;

			// Iterate through rows
			if($logger != null) $logger("--- IMMEDIATE OUTPUT ---" . NEW_LINE);
			while ($row = mysqli_fetch_assoc($filesFetchResult))
			{
				$filename = $row['filename'];
        		$filename_escaped = mysqli_real_escape_string($link, $filename);
				$log .= "Working on '{$filename}'... ";
				if($logger != null) $logger("Working on '{$filename}'... ");

				// Get the ids in this file
				$ids = [];
				$sql = "SELECT `item_id` FROM `housekeeping_itemid_filename` WHERE `filename` = '$filename_escaped';";
				$result2 = mysqli_query($link, $sql);
				while ($row2 = mysqli_fetch_assoc($result2))
				{
					$itemId = $row2['item_id'];
					$ids[] = $itemId;
				}

				// Check there are Ids
				if (empty($ids))
				{
					if($logger != null) $logger("No item IDs" . NEW_LINE);
					$log .= "No item IDs" . NEW_LINE;
					continue;
				}

				// Get the files for those ids
        		$relatedFilenames = [];
        		$ids_quoted = array_map(fn($id) => "'$id'", $ids);
        		$sql = "SELECT DISTINCT `filename` FROM `housekeeping_itemid_filename` WHERE `item_id` IN (" . implode(',', $ids_quoted) . ");";
        		$result3 = mysqli_query($link, $sql);
				if ($result3)
				{
					while ($row3 = mysqli_fetch_assoc($result3))
					{
						$relFile = $row3['filename'];
						$relatedFilenames[$relFile] = true;
					}
				}
        		$relatedFilenames = array_keys($relatedFilenames);

				// Upload the data
				$values = [];
				foreach ($relatedFilenames as $fn)
				{
					$fn_escaped = mysqli_real_escape_string($link, $fn);
					$values[] = "('$filename_escaped', '$fn_escaped')";
				}
				if (!empty($values))
				{
					$query = "REPLACE INTO housekeeping_filename_related (filename, related_filename) VALUES " 
						. implode(", ", $values);
					if (!mysqli_query($link, $query))
					{
						if($logger != null) $logger("Error: " . mysqli_error($link) . NEW_LINE);
						$log .= "Error: " . mysqli_error($link) . NEW_LINE;
					}
					else
					{
						if($logger != null) $logger("Inserted " . mysqli_affected_rows($link) . " rows." . NEW_LINE);
						$log .= "Inserted " . mysqli_affected_rows($link) . " rows." . NEW_LINE;
					}
				}
			}

            // Return
			$log .= "SCRIPT SUCCEEDED";
			if($logger != null) $logger("------------------------" . NEW_LINE);
			return ["success" => true, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
        }
    }

	//Generate the metadata for all the files, populating 1 of the 2 tables
    //'$logger' is a function that can log output without added new lines
	public static function Database_Curate_Ids(mysqli $link, ?callable $logger = null): array {

		$log = "";
		$data = [];
        try
        {
			// Get entries
			$sql = "SELECT `content`, `filename` FROM `compiled_documents`;";
    		$filesFetchResult = mysqli_query($link, $sql);

			// Check result
			if (!$filesFetchResult) 
			{
				$log .= "Error fetching files: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}
			if (mysqli_num_rows($filesFetchResult) <= 0)
			{
				$log .= "No entries fetched." . NEW_LINE;
				return ["success" => false, "log" => $log, "result" => null];
			}

			//empty table
			$sql = "TRUNCATE TABLE housekeeping_itemid_filename";
			if (!mysqli_query($link, $sql))
			{
				$log .= "Error: " . mysqli_error($link) . NEW_LINE;
				if($logger != null) $logger("XX (failed)" . NEW_LINE);
			}
			if($logger != null) $logger("Empty table - Ok." . NEW_LINE);
			$log .= "Empty table - Ok." . NEW_LINE;

			// Iterate through rows
			if($logger != null) $logger("--- IMMEDIATE OUTPUT ---" . NEW_LINE);
			while ($row = mysqli_fetch_assoc($filesFetchResult))
			{
				$filename = $row['filename'];
        		$content = $row['content'];
				if($logger != null) $logger($filename . "... ");

				// Extract IDs
				$jArr = json_decode($content, true);
				$ids = [];
				self::extractIds($jArr, $ids);
				$ids = array_keys($ids);

				// Log count
				$length = count($ids);
				$log .= "'{$filename}' has {$length} public ids." . NEW_LINE;
				//print_r($ids); var_dump($ids); break;

				// Upload the data
				$filename_safe = mysqli_real_escape_string($link, $filename);
				$values = [];
				foreach ($ids as $id) 
				{
					$item_id_safe = mysqli_real_escape_string($link, $id);
					$values[] = "('$filename_safe', '$item_id_safe')";
				}
				if (!empty($values))
				{
					$valueString = implode(", ", $values);
					$data[] = $valueString;
					$query = "REPLACE INTO housekeeping_itemid_filename (filename, item_id) VALUES " . $valueString;
					//$query = "INSERT INTO housekeeping_itemid_filename (filename, item_id) VALUES " 
					//. $valueString . " ON DUPLICATE KEY UPDATE filename = VALUES(filename)";

					if (!mysqli_query($link, $query))
					{
						$log .= "Error: " . mysqli_error($link) . NEW_LINE;
						if($logger != null) $logger("XX (failed)" . NEW_LINE);
					}
					else
					{
						$log .= "Inserted " . mysqli_affected_rows($link) . " rows." . NEW_LINE;
						if($logger != null) $logger("Ok (" . mysqli_affected_rows($link) . " rows)" . NEW_LINE);
					}
				}
				else
				{
					if($logger != null) $logger("Ok (0 rows)" . NEW_LINE);
				}
			}

            // Return
			$log .= "SCRIPT SUCCEEDED";
			if($logger != null) $logger("------------------------" . NEW_LINE);
			return ["success" => true, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
        }
	}

	//Generate the metadata for all the files, populating 1 of the 2 tables
    //'$logger' is a function that can log output without added new lines
	public static function Database_Curate_Filenames(mysqli $link, ?callable $logger = null): array {

		$log = "";
		$data = [];
        try
        {
			// Get entries
			$sql = "SELECT DISTINCT `filename` FROM `housekeeping_itemid_filename`;";
    		$filesFetchResult = mysqli_query($link, $sql);

			// Check result
			if (!$filesFetchResult) 
			{
				$log .= "Error fetching files: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}
			if (mysqli_num_rows($filesFetchResult) <= 0)
			{
				$log .= "No entries fetched." . NEW_LINE;
				return ["success" => false, "log" => $log, "result" => null];
			}

			//empty table
			$sql = "TRUNCATE TABLE housekeeping_filename_related";
			if (!mysqli_query($link, $sql))
			{
				$log .= "Error: " . mysqli_error($link) . NEW_LINE;
				if($logger != null) $logger("XX (failed)" . NEW_LINE);
			}
			if($logger != null) $logger("Empty table - Ok." . NEW_LINE);
			$log .= "Empty table - Ok." . NEW_LINE;

			// Iterate through rows
			if($logger != null) $logger("--- IMMEDIATE OUTPUT ---" . NEW_LINE);
			while ($row = mysqli_fetch_assoc($filesFetchResult))
			{
				$filename = $row['filename'];
        		$filename_escaped = mysqli_real_escape_string($link, $filename);
				$log .= "Working on '{$filename}'... ";
				if($logger != null) $logger("Working on '{$filename}'... ");

				// Get the ids in this file
				$ids = [];
				$sql = "SELECT `item_id` FROM `housekeeping_itemid_filename` WHERE `filename` = '$filename_escaped';";
				$result2 = mysqli_query($link, $sql);
				while ($row2 = mysqli_fetch_assoc($result2))
				{
					$itemId = $row2['item_id'];
					$ids[] = $itemId;
				}

				// Check there are Ids
				if (empty($ids))
				{
					if($logger != null) $logger("No item IDs" . NEW_LINE);
					$log .= "No item IDs" . NEW_LINE;
					continue;
				}

				// Get the files for those ids
        		$relatedFilenames = [];
        		$ids_quoted = array_map(fn($id) => "'$id'", $ids);
        		$sql = "SELECT DISTINCT `filename` FROM `housekeeping_itemid_filename` WHERE `item_id` IN (" . implode(',', $ids_quoted) . ");";
        		$result3 = mysqli_query($link, $sql);
				if ($result3)
				{
					while ($row3 = mysqli_fetch_assoc($result3))
					{
						$relFile = $row3['filename'];
						$relatedFilenames[$relFile] = true;
					}
				}
        		$relatedFilenames = array_keys($relatedFilenames);

				// Upload the data
				$values = [];
				foreach ($relatedFilenames as $fn)
				{
					$fn_escaped = mysqli_real_escape_string($link, $fn);
					$values[] = "('$filename_escaped', '$fn_escaped')";
				}
				if (!empty($values))
				{
					$query = "REPLACE INTO housekeeping_filename_related (filename, related_filename) VALUES " 
						. implode(", ", $values);
					if (!mysqli_query($link, $query))
					{
						if($logger != null) $logger("Error: " . mysqli_error($link) . NEW_LINE);
						$log .= "Error: " . mysqli_error($link) . NEW_LINE;
					}
					else
					{
						if($logger != null) $logger("Inserted " . mysqli_affected_rows($link) . " rows." . NEW_LINE);
						$log .= "Inserted " . mysqli_affected_rows($link) . " rows." . NEW_LINE;
					}
				}
			}

            // Return
			$log .= "SCRIPT SUCCEEDED";
			if($logger != null) $logger("------------------------" . NEW_LINE);
			return ["success" => true, "log" => $log, "result" => $data];
        }
        catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
        }
	}



	//Find how many times each file is referenced
	public static function Database_Find_Orphans(mysqli $link): array {

		$log = "";
		$data = [];
        try
        {
			// Fetch files from database
			$sql = "SELECT `filename`, `related_filename` FROM `housekeeping_filename_related`;";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching filenames: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}

			//loop through files
			$fileCounts = [];
            while ($row = mysqli_fetch_assoc($result)) 
            {
                $relatedFile = $row['related_filename'];
                if (isset($fileCounts[$relatedFile])) $fileCounts[$relatedFile]++;
				else $fileCounts[$relatedFile] = 1;
            }

			 // Show results
			foreach ($fileCounts as $file => $count) 
            {
                $log .= "'{$file}' - {$count}" . NEW_LINE;
            }
			
			// return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => $fileCounts];
		}
		catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
        }
	}

	//Find how many times each file is referenced
	public static function Database_Find_Orphans_Advanced(mysqli $link): array {

		$log = "";
		$data = [];
        try
        {
			// Fetch files from database
			$sql = "SELECT `filename`, `related_filename` FROM `housekeeping_filename_related`;";
			$result = mysqli_query($link, $sql);
			if (!$result) 
			{
				$log .= "Error fetching filenames: " . mysqli_error($link);
				return ["success" => false, "log" => $log, "result" => null];
			}

			//1. Build a DIRECTED graph (A points to B, but not vice versa automatically)
            $graph = [];
            $allFiles = [];

            // Track all files we know about in the DB so we have a master list
			$startingFile = "";
            while ($row = mysqli_fetch_assoc($result)) 
            {
                $file = $row['filename'];
                $related = $row['related_filename'];
				if($startingFile == "") $startingFile = $file;

                // Directed connection: $file references $related
                $graph[$file][] = $related;

                // Add both to our master list of unique files
                $allFiles[$file] = true;
                $allFiles[$related] = true;
            }

            // 2. Traverse the chain starting ONLY from the first file
            $visited = [];
            $queue = [$startingFile];
            $visited[$startingFile] = true;

            while (count($queue) > 0) 
            {
                $current = array_shift($queue);

                // If this file references other files, follow those links
                if (isset($graph[$current])) 
                {
                    foreach ($graph[$current] as $neighbor) 
                    {
                        if (!isset($visited[$neighbor])) 
                        {
                            $visited[$neighbor] = true;
                            $queue[] = $neighbor; // Add to queue to explore its references later
                        }
                    }
                }
            }

            // 3. Compare visited files against the master list
            $unnavigableFiles = [];
            foreach (array_keys($allFiles) as $file) 
            {
                if (!isset($visited[$file])) 
                {
                    $unnavigableFiles[] = $file;
                }
            }

            // 4. Output the results
            $totalFiles = count($allFiles);
            $reachableCount = count($visited);
            
            $log .= "--- Navigation Analysis ---" . NEW_LINE;
            $log .= "Starting File: $startingFile" . NEW_LINE;
            $log .= "Total files in system: $totalFiles" . NEW_LINE;
            $log .= "Files reachable from start: $reachableCount" . NEW_LINE . NEW_LINE;

            if (count($unnavigableFiles) === 0) 
            {
                $log .= "SUCCESS: The chain of references eventually includes ALL files! Everything is navigable.\n";
            } 
            else 
            {
                $log .= "There are " . count($unnavigableFiles) . 
					" unnavigable file(s) that cannot be reached from the first file:" . NEW_LINE;
                foreach ($unnavigableFiles as $isolatedFile) 
                {
                    $log .= "  - $isolatedFile"  . NEW_LINE;
                }
            }
			
			// return
			$log .= "SCRIPT SUCCEEDED";
			return ["success" => true, "log" => $log, "result" => $unnavigableFiles];
		}
		catch (Throwable $ex) 
		{
			$log .= "EXCEPTION: " . $ex->getMessage() . " (line " . $ex->getLine() . ")" . NEW_LINE;
			$log .= "SCRIPT FAILED";
			return ["success" => false, "log" => $log, "result" => null];
        }
	}



    //helpers
    private static function runSqlFile(mysqli $link, string $filePath): string {

		$log = "";
		$label = basename($filePath);
		$log .= "Running: '" . $label . "' ...";

		try 
		{
			$sql = file_get_contents($filePath);
			if ($sql === false) 
			{
				throw new Exception("Fail - Cannot read file");
			}

			// Execute multi-statement SQL
			if (!$link->multi_query($sql)) 
			{
				throw new Exception("Fail - SQL execution failed");
			}
			
			// Cycle through all results to ensure completion and catch subsequent errors in the batch
			do 
			{
				if ($result = $link->store_result())
				{
					$result->free();
				}
				if ($link->error) 
				{
					throw new Exception("Fail - SQL error");
				}
			}
			while ($link->more_results() && $link->next_result());

			// Report
			$log .= " OK" . NEW_LINE;
		} 
		catch (Throwable $e) 
		{
			// Stop installer immediately, let the main catch block handle the panic
			$log .= " Fail" . NEW_LINE;
			throw $e;
		}
		
		return $log;
	}
    private static function extractIds(array $node, array &$ids) {

		if (isset($node['id']) && is_string($node['id'])) {
			if (strpos($node['id'], '@') !== 0) {
				$ids[$node['id']] = true; // Use value as key to ensure uniqueness
			}
		}

		if (isset($node['items']) && is_array($node['items'])) {
			foreach ($node['items'] as $child) {
				if (is_array($child)) {
					self::extractIds($child, $ids);
				}
			}
		}
	}
}