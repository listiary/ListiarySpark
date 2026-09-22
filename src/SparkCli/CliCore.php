<?php
namespace SparkCli;
use Exception, FilesystemIterator, RegexIterator;

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	require_once __DIR__ . '/../SparkLib/Common/Basics.php';
	require_once __DIR__ . '/../SparkLib/Common/Validator.php';
	require_once __DIR__ . '/../SparkLib/Common/Compiler.php';
	require_once __DIR__ . '/../SparkLib/Commands/Config.php';
	require_once __DIR__ . '/../SparkLib/Commands/File.php';
	require_once __DIR__ . '/../SparkLib/Commands/Json.php';
	require_once __DIR__ . '/../SparkLib/Commands/Metadata.php';
	require_once __DIR__ . '/../SparkLib/Commands/Database.php';
	require_once __DIR__ . '/../SparkLib/Commands/Action.php';
	require_once __DIR__ . "/ConsoleWriter.php";
	require_once __DIR__ . "/CommandHandler.php";
	use function SparkLib\Common\{catchEx, connectDb};
	use SparkLib\Common\Validator;
	use SparkLib\Common\Compiler;
	use SparkLib\Commands\Config;
	use SparkLib\Commands\File;
	use SparkLib\Commands\Json;
	use SparkLib\Commands\Metadata;
	use SparkLib\Commands\Database;
	use SparkLib\Commands\Action;
	set_exception_handler('\SparkLib\Common\catchEx');


// The Entry Point
function Execute(array $argv) {

	// get command
	echo "we here";
	$command = CommandHandler::GetCommand($argv);
	$arr = CommandHandler::ReadCommandArgs($argv, $command);
	$output = null;


	//preset
	//if(!ConsoleWriter::$isThemeSet) ConsoleWriter::setDefaultTheme();
	ConsoleWriter::printBanner();
	ConsoleWriter::printCmdLine($argv);


	// figure out and execute command
	if ($command == CliCommand::None) Execute_None();
	else if($command == CliCommand::Unknown) Execute_Unknown();
	else if($command == CliCommand::Help) Execute_Help($arr);
	else if(!$arr['success']) Execute_FaultyCommand($command);

	//configs
	if($command == CliCommand::ConfigMake) Execute_ConfigMake($arr, true);
	else if($command == CliCommand::ConfigMakeOnline) Execute_ConfigMake($arr, true);
	else if($command == CliCommand::ConfigMakeOffline) Execute_ConfigMake($arr, false);
	else if($command == CliCommand::ConfigShow) Execute_ConfigShow($arr);
	else if($command == CliCommand::ConfigTest) Execute_ConfigTest($arr);
	else if($command == CliCommand::ConfigTestCompiler) Execute_ConfigTestCompiler($arr);
	else if($command == CliCommand::ConfigsList) Execute_ConfigsList($arr);

	//file
	else if($command == CliCommand::FileView) Execute_FileView($arr);
	else if($command == CliCommand::FileDownload) Execute_FileDownload($arr);
	else if($command == CliCommand::FileUpload) Execute_FileUpload($arr);
	else if($command == CliCommand::FileCompile) Execute_FileCompile($arr);
	else if($command == CliCommand::FileDelete) Execute_FileDelete($arr);

	//json
	else if($command == CliCommand::JsonView) Execute_JsonView($arr);
	else if($command == CliCommand::JsonDownload) Execute_JsonDownload($arr);
	else if($command == CliCommand::JsonUpload) Execute_JsonUpload($arr);
	else if($command == CliCommand::JsonDelete) Execute_JsonDelete($arr);

	//files
    else if($command == CliCommand::FilesList) Execute_FilesList($arr);
	else if($command == CliCommand::FilesCount) Execute_FilesCount($arr);
	else if($command == CliCommand::FilesDownload) Execute_FilesDownload($arr);
	else if($command == CliCommand::FilesUpload) Execute_FilesUpload($arr);
	else if($command == CliCommand::FilesCompile) Execute_FilesCompile($arr);
	else if($command == CliCommand::FilesDelete) Execute_FilesDelete($arr);

	//jsons
	else if($command == CliCommand::JsonsList) Execute_JsonsList($arr);
	else if($command == CliCommand::JsonsCount) Execute_JsonsCount($arr);
	else if($command == CliCommand::JsonsDownload) Execute_JsonsDownload($arr);
	else if($command == CliCommand::JsonsDelete) Execute_JsonsDelete($arr);

	//metadata
	else if($command == CliCommand::MetadataView) Execute_MetadataView($arr);

	//database
	else if($command == CliCommand::DatabaseTest) Execute_DatabaseTest($arr);
	else if($command == CliCommand::DatabaseWipe) Execute_DatabaseWipe($arr);
	else if($command == CliCommand::DatabaseDownload) Execute_DatabaseDownload($arr);
	else if($command == CliCommand::DatabaseUpload) Execute_DatabaseUpload($arr);
	else if($command == CliCommand::DatabaseMakeTables) Execute_DatabaseMakeTables($arr);
	else if($command == CliCommand::DatabaseRecompile) Execute_DatabaseRecompile($arr);
	else if($command == CliCommand::DatabaseCurate) Execute_DatabaseCurate($arr);
	else if($command == CliCommand::DatabaseCurateIds) Execute_DatabaseCurateIds($arr);
	else if($command == CliCommand::DatabaseCurateFilenames) Execute_DatabaseCurateFilenames($arr);
	else if($command == CliCommand::DatabaseFindOrphans) Execute_DatabaseFindOrphans($arr);
	
	//action
	else if($command == CliCommand::ActionDeleteArticle) Execute_ActionDeleteArticle($arr);
	else if($command == CliCommand::ActionCreateStub) Execute_ActionCreateStub($arr);
}


function Execute_None(): void {

	ConsoleWriter::printSparkDescription();
	ConsoleWriter::resetColors();
	ConsoleWriter::consoleLogNewLine();
	exit(0);
}
function Execute_Unknown(): void {

	ConsoleWriter::printHelpMessage("Invalid syntax for command!");
	ConsoleWriter::resetColors();
	ConsoleWriter::consoleLogNewLine();
	exit(0);
}
function Execute_Help(array $arr): void {

	if($arr['success']) ConsoleWriter::printHelpMessage();
	else ConsoleWriter::printHelpMessage("Invalid syntax for 'help'!");
	ConsoleWriter::resetColors();
	ConsoleWriter::consoleLogNewLine();
	exit(0);
}
function Execute_FaultyCommand(CliCommand $command): void {

	$commandText = $command->value;
	ConsoleWriter::printHelpMessage("Invalid syntax around '{$commandText}'!");
	ConsoleWriter::resetColors();
	ConsoleWriter::consoleLogNewLine();
	exit(0);
}


function Execute_ConfigMake(array $arr, bool $online = true): void {

	if(Validator::isStringNullOrEmpty($arr['configName'])) $arr['configName'] = 'config.php';
	if(!Validator::validateConfigName($arr['configName'])) 
	{
		throw new Exception("'{$arr['configName']}' - is not a valid name for a Listiary config.");
	}

	// Ask user for values
	if(!array_key_exists('compiler', $arr)) $arr['compiler'] = readline("Compiler url: ");
	if(!array_key_exists('server', $arr)) $arr['server'] = readline("Server name: ");
	if(!array_key_exists('database', $arr)) $arr['database'] = readline("Database name: ");
	if(!array_key_exists('user', $arr)) $arr['user'] = readline("Username: ");
	if(!array_key_exists('password', $arr)) $arr['password'] = readline("Password: ");

	// Execute
	$parameters =
	[
		'compilerUrl' => $arr['compiler'],
		'serverName' => $arr['server'],
		'dbName' => $arr['database'],
		'dbUser' => $arr['user'],
		'dbPass' => $arr['password']
	];
	if($online) $output = Config::Config_Make_Online($parameters);
	else $output = Config::Config_Make_Offline($parameters);

	// Get new config filepath
	$newName = $arr['configName'];
	if($arr['addDate'])
	{
		$now = date("Ymd-His");
		$newName = $now . "-" . $arr['configName'];
	}
	$newPath = realpath(__DIR__ . "\..\_configs\\") . "\\" . $newName;

	// Save new config file
	$res = file_put_contents($newPath, $output['result']);

	//output the log narrative
	printExecutionLog($output["log"]);
	if($res > 0) ConsoleWriter::consoleLog("Done. Config saved to the configs folder: '{$newName}'");
	else ConsoleWriter::consoleLogError("Fail. Something went wrong.");
	exitProper($output["success"]);
}
function Execute_ConfigShow(array $arr): void {

	if(Validator::isStringNullOrEmpty($arr['configName'])) $arr['configName'] = 'config.php';
	if(!Validator::validateConfigName($arr['configName'])) 
	{
		throw new Exception("'{$arr['configName']}' - is not a valid name for a Listiary config.");
	}

	//include the config
	if (!@include_once __DIR__ . "/../_configs/" . $arr['configName']) 
	{
		throw new Exception("Missing main config file - '{" . $arr['configName'] . "}' ");
	}

	// Execute
	$log = "";
	$log .= "Compiler URL: " . COMPILER_URL . NEW_LINE;
	$log .= "Selected Server: " . DB_SERVER_PUBLIC . NEW_LINE;
	$log .= "Selected Database: " . DB_NAME_PUBLIC . NEW_LINE;
	$log .= "Selected User: " . DB_USERNAME_PUBLIC . NEW_LINE;
	$log .= "Password: ***********";
	$output = ["success" => true, "log" => $log, "result" => true];

	//output the log narrative
	printExecutionLog($output["log"]);
	exitProper($output["success"]);
}
function Execute_ConfigTest(array $arr): void {

	if(Validator::isStringNullOrEmpty($arr['configName'])) $arr['configName'] = 'config.php';
	else if(!Validator::validateConfigName($arr['configName'])) 
	{
		throw new Exception("'{$arr['configName']}' - is not a valid name for a Listiary config.");
	}

	//include the config
	if (!@include_once __DIR__ . "/../_configs/" . $arr['configName']) 
	{
		throw new Exception("Missing main config file - '{" . $arr['configName'] . "}' ");
	}

	// Execute
	$parameters =
	[
		'compilerUrl' => COMPILER_URL,
		'serverName' => DB_SERVER_PUBLIC,
		'dbName' => DB_NAME_PUBLIC,
		'dbUser' => DB_USERNAME_PUBLIC,
		'dbPass' => DB_PASSWORD_PUBLIC
	];
	$output = Config::Config_Test($parameters);

	//output the log narrative
	printExecutionLog($output["log"]);
	exitProper($output["success"]);
}
function Execute_ConfigTestCompiler(array $arr): void {

	if(Validator::isStringNullOrEmpty($arr['configName'])) $arr['configName'] = 'config.php';
	else if(!Validator::validateConfigName($arr['configName'])) 
	{
		throw new Exception("'{$arr['configName']}' - is not a valid name for a Listiary config.");
	}

	//include the config
	if (!@include_once __DIR__ . "/../_configs/" . $arr['configName']) 
	{
		throw new Exception("Missing main config file - '{" . $arr['configName'] . "}' ");
	}

	// Execute
	$parameters =
	[
		'compilerUrl' => COMPILER_URL
	];
	$output = Config::Config_Test_Compiler($parameters);

	//output the log narrative
	printExecutionLog($output["log"]);
	exitProper($output["success"]);
}
function Execute_ConfigsList(array $arr): void {

	$dir = __DIR__ . "/../_configs/*";

	// Execute
	$log = NEW_LINE;
	$files = glob($dir);
	foreach ($files as $file)
	{
		$log .= basename($file) . NEW_LINE;
	}
	$output = ["success" => true, "log" => $log, "result" => true];

	//output the log narrative
	printExecutionLog($output["log"]);
	exitProper($output["success"]);
}


function Execute_FilesList(array $arr): void {

	if(!Validator::isStringValidInt($arr['max'], -1, 9999999)) 
	{
		throw new Exception("'{$arr['max']}' - is not a valid maximum value.");
	}

	// Execute
	$parameters =
	[
		'max' => $arr['max']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::Files_List($link, $parameters);
	ConsoleWriter::consoleLog("Got " . count($output["result"]) . " files.");
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FilesCount(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::Files_Count($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FilesDownload(array $arr): void {

	if(!Validator::isStringValidInt($arr['max'], -1, 9999999)) 
	{
		throw new Exception("'{$arr['max']}' - is not a valid maximum value.");
	}

	// Execute
	$parameters =
	[
		'max' => $arr['max']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::Files_Download($link, $parameters);
	ConsoleWriter::consoleLog("Downloaded " . count($output["result"]) . " files.");
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);

	//save files
	$taskName = date("Ymd-His") . "-files-download";
	$downloadDir = __DIR__ . '\..\_downloads';
	$downloadDir = realpath($downloadDir);
	$downloadDir = $downloadDir . "\\" . $taskName;
	if (!is_dir($downloadDir)) mkdir($downloadDir, 0755, true);
	ConsoleWriter::consoleLog("Download to local dir: '" . $taskName . "'");
	foreach ($output["result"] as $fileData)
	{
		$filename = $fileData['filename'];
    	$content  = $fileData['content'];
		if (!str_ends_with(strtolower($filename), '.ds')) $filename .= ".ds";
		$filePath = $downloadDir . '\\' . $filename;
		$bytesWritten = file_put_contents($filePath, $content);
		if ($bytesWritten === false) ConsoleWriter::consoleLogError($filename . " - Failed");
		else ConsoleWriter::consoleLog($filename . " - Ok ({$bytesWritten} B)");
	}

	//exit
	ConsoleWriter::consoleLogNewLine();
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FilesUpload(array $arr): void {

	//verify folder
	$taskName = $arr['task-path'];
	$uploadDir = __DIR__ . '\..\_uploads';
	$uploadDir = realpath($uploadDir);
	$uploadDir = $uploadDir . "\\" . $taskName;
	if(!Validator::validateLocalFolderName($uploadDir)) 
	{
		throw new Exception("'{$taskName}' - is not a valid local folder inside '_uploads'.");
	}

	//get ds files
	$parameters = ['files' => []];
	$directory = new FilesystemIterator($uploadDir, FilesystemIterator::KEY_AS_FILENAME);
	$iterator = new RegexIterator($directory, '/\.ds$/i', RegexIterator::MATCH);
	foreach ($iterator as $fileName => $fileInfo)
	{
		// check the filename
		if(!Validator::validateCanonicalDsFileName($fileName)) 
		{
			throw new Exception("'{$fileName}' - is not a valid canonical ds filename.");
		}

		//remove extension
		if (str_ends_with($fileName, '.ds') || str_ends_with($fileName, '.DS'))
		{
			$fileName = substr($fileName, 0, -3);
		}

		// Read the full text of the file
		$fileContent = file_get_contents($fileInfo->getPathname());
		
		// Pack it into the associative array using the filename as the key
		if ($fileContent !== false)
		{
			$parameters['files'][$fileName] = $fileContent;
		}

		//$charCount = mb_strlen($fileContent, 'UTF-8');
		$byteCount = strlen($fileContent);
		ConsoleWriter::consoleLog("Red file '{$fileName}' - ok ({$byteCount} B).");
	}

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::Files_Upload($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);

	//exit
	ConsoleWriter::consoleLogNewLine();
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FilesCompile(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::Files_Compile($link, ConsoleWriter::consoleLog_NoNL(...));
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FilesDelete(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::Files_Delete($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}


function Execute_JsonsList(array $arr): void {

	if(!Validator::isStringValidInt($arr['max'], -1, 9999999)) 
	{
		throw new Exception("'{$arr['max']}' - is not a valid maximum value.");
	}

	// Execute
	$parameters =
	[
		'max' => $arr['max']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Jsons_List($link, $parameters);
	ConsoleWriter::consoleLog("Got " . count($output["result"]) . " files.");
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_JsonsCount(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Jsons_Count($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_JsonsDownload(array $arr): void {

	if(!Validator::isStringValidInt($arr['max'], -1, 9999999)) 
	{
		throw new Exception("'{$arr['max']}' - is not a valid maximum value.");
	}

	// Execute
	$parameters =
	[
		'max' => $arr['max']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Jsons_Download($link, $parameters);
	ConsoleWriter::consoleLog("Downloaded " . count($output["result"]) . " JSON files.");
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);

	//save files
	$taskName = date("Ymd-His") . "-jsons-download";
	$downloadDir = __DIR__ . '\..\_downloads';
	$downloadDir = realpath($downloadDir);
	$downloadDir = $downloadDir . "\\" . $taskName;
	if (!is_dir($downloadDir)) mkdir($downloadDir, 0755, true);
	ConsoleWriter::consoleLog("Download to local dir: '" . $taskName . "'");
	foreach ($output["result"] as $fileData)
	{
		$filename = $fileData['filename'];
    	$content  = $fileData['content'];
		if (!str_ends_with(strtolower($filename), '.json')) $filename .= ".json";
		$filePath = $downloadDir . '\\' . $filename;
		$bytesWritten = file_put_contents($filePath, $content);
		if ($bytesWritten === false) ConsoleWriter::consoleLogError($filename . " - Failed");
		else ConsoleWriter::consoleLog($filename . " - Ok ({$bytesWritten} B)");
	}

	//exit
	ConsoleWriter::consoleLogNewLine();
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_JsonsDelete(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Jsons_Delete($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}


function Execute_FileView(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['fileName'])) 
	{
		throw new Exception("'{$arr['fileName']}' - is not a valid canonical '.ds' file name.");
	}

	// Execute
	$parameters =
	[
		'fileName' => $arr['fileName']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::File_Fetch($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	if (!empty($output["result"])) 
	{
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLog("--- DATA OUTPUT - 'filename' ---");
		ConsoleWriter::consoleLogInfo($output["result"]["filename"]);
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLog("--- DATA OUTPUT - 'content' ---");
		ConsoleWriter::consoleLogMoreInfo($output["result"]["content"]); 
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLogNewLine();
	}
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FileDownload(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['fileName'])) 
	{
		throw new Exception("'{$arr['fileName']}' - is not a valid canonical '.ds' file name.");
	}

	// Execute
	$parameters =
	[
		'fileName' => $arr['fileName']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::File_Fetch($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	if (!empty($output["result"])) 
	{
		//save files
		$taskName = date("Ymd-His") . "-file-download";
		$downloadDir = __DIR__ . '\..\_downloads';
		$downloadDir = realpath($downloadDir);
		$downloadDir = $downloadDir . "\\" . $taskName;
		if (!is_dir($downloadDir)) mkdir($downloadDir, 0755, true);
		$filename = $output["result"]["filename"];
		$content  = $output["result"]["content"]; 
		if (!str_ends_with(strtolower($filename), '.ds')) $filename .= ".ds";
		$filePath = $downloadDir . '\\' . $filename;
		$bytesWritten = file_put_contents($filePath, $content);
		if ($bytesWritten === false) ConsoleWriter::consoleLogError($filename . " - Failed");
		else ConsoleWriter::consoleLog($taskName . "\\" . $filename . " - Ok ({$bytesWritten} B)");
	}
	
	//exit
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FileDelete(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['fileName'])) 
	{
		throw new Exception("'{$arr['fileName']}' - is not a valid canonical '.ds' file name.");
	}

	// Execute
	$parameters =
	[
		'fileName' => $arr['fileName']
	];

	//if(norepl == false) then do the confirmation
	// Confirm the dangerous operation we are about to perform
    ConsoleWriter::consoleLog("!!! WARNING !!!");
    ConsoleWriter::consoleLog("This will permanently delete file '{$arr['fileName']}' from the database.");
    ConsoleWriter::consoleLog("Do you want to proceed (y/n)?");
    $confirmation = trim(readline());
    if (strtolower($confirmation) !== "y")
	{
        ConsoleWriter::consoleLog("Aborted. Nothing was deleted.");
        exitProper(true);
    }

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::File_Delete($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FileUpload(array $arr): void {
	
	//verify path
	$filePath = $arr['filePath'];
	$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_uploads';
	$uploadDir = realpath($uploadDir);
	$uploadDir = $uploadDir . DIRECTORY_SEPARATOR . $filePath;
	if(!Validator::validateLocalFileReadable($uploadDir)) 
	{
		throw new Exception("'{$uploadDir}' - is not a valid relative file path inside '_uploads'.");
	}

	//verify filename
	$fileNameOnly = basename($uploadDir);
	if(!Validator::validateCanonicalDsFileName($fileNameOnly)) 
	{
		throw new Exception("'{$fileNameOnly}' - is not a valid canonical ds filename.");
	}

	//verify contents
	$fileContent = file_get_contents($uploadDir);
	$min = 3; $max = 500000;
	if(!Validator::validateStringByteLength($fileContent, $min, $max)) 
	{
		throw new Exception("'{$fileNameOnly}' file size ($size B) is outside the allowed range ({$min} - {$max}).");
	}
	if(!Validator::validateStringEncodingUtf8($fileContent)) 
	{
		throw new Exception("'{$fileNameOnly}' - is not valid UTF-8 text.");
	}

	//remove extension
	if (str_ends_with($fileNameOnly, '.ds') || str_ends_with($fileNameOnly, '.DS'))
	{
		$fileNameOnly = substr($fileNameOnly, 0, -3);
	}

	//read file
	$parameters = [];
	$parameters['fileName'] = $fileNameOnly;
	$parameters['fileContent'] = $fileContent;
	//$charCount = mb_strlen($fileContent, 'UTF-8');
	$byteCount = strlen($fileContent);
	ConsoleWriter::consoleLog("Red file '{$fileNameOnly}' - ok ({$byteCount} B).");

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::File_Put($link, $parameters);
	ConsoleWriter::consoleLog("Script Executed");
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);

	//exit
	ConsoleWriter::consoleLogNewLine();
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_FileCompile(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['fileName'])) 
	{
		throw new Exception("'{$arr['fileName']}' - is not a valid canonical '.ds' file name.");
	}

	// Execute
	$parameters =
	[
		'fileName' => $arr['fileName']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = File::File_Compile($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}


function Execute_JsonView(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['jsonName'])) 
	{
		throw new Exception("'{$arr['jsonName']}' - is not a valid canonical '.json' file name.");
	}

	// Execute
	$parameters =
	[
		'jsonName' => $arr['jsonName']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Json_Fetch($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	if (!empty($output["result"])) 
	{
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLog("--- DATA OUTPUT - 'filename' ---");
		ConsoleWriter::consoleLogInfo($output["result"]["filename"]);
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLog("--- DATA OUTPUT - 'content' ---");
		ConsoleWriter::consoleLogMoreInfo($output["result"]["content"]); 
		ConsoleWriter::consoleLogNewLine();
		ConsoleWriter::consoleLogNewLine();
	}
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_JsonDownload(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['jsonName'])) 
	{
		throw new Exception("'{$arr['jsonName']}' - is not a valid canonical '.json' file name.");
	}

	// Execute
	$parameters =
	[
		'jsonName' => $arr['jsonName']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Json_Fetch($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	if (!empty($output["result"])) 
	{
		//save files
		$taskName = date("Ymd-His") . "-json-download";
		$downloadDir = __DIR__ . '\..\_downloads';
		$downloadDir = realpath($downloadDir);
		$downloadDir = $downloadDir . "\\" . $taskName;
		if (!is_dir($downloadDir)) mkdir($downloadDir, 0755, true);
		$filename = $output["result"]["filename"];
		$content  = $output["result"]["content"]; 
		if (!str_ends_with(strtolower($filename), '.json')) $filename .= ".json";
		$filePath = $downloadDir . '\\' . $filename;
		$bytesWritten = file_put_contents($filePath, $content);
		if ($bytesWritten === false) ConsoleWriter::consoleLogError($filename . " - Failed");
		else ConsoleWriter::consoleLog($taskName . "\\" . $filename . " - Ok ({$bytesWritten} B)");
	}
	
	//exit
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_JsonUpload(array $arr): void {
	
	//verify path
	$jsonPath = $arr['jsonPath'];
	$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_uploads';
	$uploadDir = realpath($uploadDir);
	$uploadDir = $uploadDir . DIRECTORY_SEPARATOR . $jsonPath;
	if(!Validator::validateLocalFileReadable($uploadDir)) 
	{
		throw new Exception("'{$taskName}' - is not a valid relative file path inside '_uploads'.");
	}

	//verify filename
	$fileNameOnly = basename($uploadDir);
	if(!Validator::validateCanonicalDsFileName($fileNameOnly)) 
	{
		throw new Exception("'{$fileNameOnly}' - is not a valid canonical json filename.");
	}

	//verify contents
	$fileContent = file_get_contents($uploadDir);
	$min = 3; $max = 500000;
	if(!Validator::validateStringByteLength($fileContent, $min, $max)) 
	{
		throw new Exception("'{$fileNameOnly}' file size ($size B) is outside the allowed range ({$min} - {$max}).");
	}
	if(!Validator::validateStringEncodingUtf8($fileContent)) 
	{
		throw new Exception("'{$fileNameOnly}' - is not valid UTF-8 text.");
	}

	//remove extension
	if (str_ends_with($fileNameOnly, '.json') || str_ends_with($fileNameOnly, '.JSON'))
	{
		$fileNameOnly = substr($fileNameOnly, 0, -5);
	}

	//read file
	$parameters = [];
	$parameters['jsonName'] = $fileNameOnly;
	$parameters['jsonContent'] = $fileContent;
	//$charCount = mb_strlen($fileContent, 'UTF-8');
	$byteCount = strlen($fileContent);
	ConsoleWriter::consoleLog("Red file '{$fileNameOnly}' - ok ({$byteCount} B).");

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Json_Put($link, $parameters);
	ConsoleWriter::consoleLog("Script Executed");
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);

	//exit
	ConsoleWriter::consoleLogNewLine();
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_JsonDelete(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['jsonName'])) 
	{
		throw new Exception("'{$arr['jsonName']}' - is not a valid canonical '.json' file name.");
	}

	// Execute
	$parameters =
	[
		'jsonName' => $arr['jsonName']
	];

	//if(norepl == false) then do the confirmation
	// Confirm the dangerous operation we are about to perform
    ConsoleWriter::consoleLog("!!! WARNING !!!");
    ConsoleWriter::consoleLog("This will permanently delete file '{$arr['jsonName']}' from the database.");
    ConsoleWriter::consoleLog("Do you want to proceed (y/n)?");
    $confirmation = trim(readline());
    if (strtolower($confirmation) !== "y")
	{
        ConsoleWriter::consoleLog("Aborted. Nothing was deleted.");
        exitProper(true);
    }

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Json::Json_Delete($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}


function Execute_MetadataView(array $arr): void {

	// $result = [ 'success' => true, 'viewFlag' => null, 'item' => null ];
	if(!Validator::validateCanonicalDsFileName($arr['item'])) 
	{
		throw new Exception("'{$arr['item']}' - is not a valid canonical file name or id.");
	}

	// Execute
	$parameters = [];
	if($arr['viewFlag'] == 'ff' || $arr['viewFlag'] == 'fi') $parameters = ['fileName' => $arr['item']];
	else if($arr['viewFlag'] == 'if') $parameters = ['itemId' => $arr['item']];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = null;
	if($arr['viewFlag'] == 'ff') $output = Metadata::Metadata_Fetch_FF($link, $parameters);
	else if($arr['viewFlag'] == 'fi') $output = Metadata::Metadata_Fetch_FI($link, $parameters);
	else if($arr['viewFlag'] == 'if') $output = Metadata::Metadata_Fetch_IF($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}


function Execute_DatabaseTest(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Test($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");	

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseWipe(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Wipe($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");	

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseDownload(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Download($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	if (!empty($output["result"])) 
	{
		//save file
		$taskName = date("Ymd-His") . "-database-download";
		$downloadDir = __DIR__ . '\..\_downloads';
		$downloadDir = realpath($downloadDir);
		$downloadDir = $downloadDir . "\\" . $taskName;
		if (!is_dir($downloadDir)) mkdir($downloadDir, 0755, true);
		$filename = DB_NAME_PUBLIC . ".sql";
		$content  = $output["result"]; 
		$filePath = $downloadDir . '\\' . $filename;
		$bytesWritten = file_put_contents($filePath, $content);
		if ($bytesWritten === false) ConsoleWriter::consoleLogError($filename . " - Failed");
		else ConsoleWriter::consoleLog($taskName . "\\" . $filename . " - Ok ({$bytesWritten} B)");
	}
	
	//exit
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseUpload(array $arr): void {

	//verify path
	$filePath = $arr['filePath'];
	$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_uploads';
	$uploadDir = realpath($uploadDir);
	$uploadDir = $uploadDir . DIRECTORY_SEPARATOR . $filePath;
	if(!Validator::validateLocalFileReadable($uploadDir)) 
	{
		throw new Exception("'{$uploadDir}' - is not a valid relative file path inside '_uploads'.");
	}

	//verify contents
	$fileNameOnly = basename($uploadDir);
	$fileContent = file_get_contents($uploadDir);
	$min = 3; $max = 5000000000;
	if(!Validator::validateStringByteLength($fileContent, $min, $max)) 
	{
		throw new Exception("'{$fileNameOnly}' file size ($size B) is outside the allowed range ({$min} - {$max}).");
	}
	if(!Validator::validateStringEncodingUtf8($fileContent)) 
	{
		throw new Exception("'{$fileNameOnly}' - is not valid UTF-8 text.");
	}

	//read file
	$parameters = [];
	$parameters['sql'] = $fileContent;
	//$charCount = mb_strlen($fileContent, 'UTF-8');
	$byteCount = strlen($fileContent);
	ConsoleWriter::consoleLog("Red file '{$fileNameOnly}' - ok ({$byteCount} B).");

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Test($link);
	if($output['result'] == true)
	{
		//output the log narrative
		printExecutionLog($output["log"]);
		$output = Database::Database_Upload($link, $parameters);
		ConsoleWriter::consoleLog("Script Executed");
	}

	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);

	//exit
	ConsoleWriter::consoleLogNewLine();
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseMakeTables(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_MakeTables($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");	

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseRecompile(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Recompile($link, ConsoleWriter::consoleLog_NoNL(...));
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseCurate(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Curate($link, ConsoleWriter::consoleLog_NoNL(...));
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseCurateIds(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Curate_Ids($link, ConsoleWriter::consoleLog_NoNL(...));
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseCurateFilenames(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Curate_Filenames($link, ConsoleWriter::consoleLog_NoNL(...));
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_DatabaseFindOrphans(array $arr): void {

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Database::Database_Find_Orphans_Advanced($link);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");	

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}



function Execute_ActionDeleteArticle(array $arr): void {

	if(!Validator::validateCanonicalDsFileName($arr['fileName'])) 
	{
		throw new Exception("'{$arr['fileName']}' - is not a valid canonical '.ds' file name.");
	}

	// Execute
	$parameters =
	[
		'fileName' => $arr['fileName']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Action::Action_DeleteArticle($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}
function Execute_ActionCreateStub(array $arr): void {

	if(Validator::isStringNullOrEmpty($arr['item'])) 
	{
		throw new Exception("'{$arr['item']}' - is not a valid entry text.");
	}
	if(!Validator::validateCanonicalDsFileName($arr['namespace'])) 
	{
		throw new Exception("'{$arr['namespace']}' - is not a valid canonical namespace name.");
	}

	// Execute
	$parameters =
	[
		'item' => $arr['item'],
		'namespace' => $arr['namespace']
	];

	//Make connection
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '_configs/config.php' ");
	}
	$link = connectDb();
	ConsoleWriter::consoleLog("Connection ok");
	$output = Action::Action_CreateStub($link, $parameters);
	mysqli_close($link);
	ConsoleWriter::consoleLog("Connection closed");

	//output the log narrative
	printExecutionLog($output["log"]);
	ConsoleWriter::consoleBlock();
	exitProper($output["success"]);
}



//Misc
function createConnection(): mysqli|false {

	//include the config
	if (!@include_once __DIR__ . "/../_configs/config.php") 
	{
		throw new Exception("Missing main config file - '{" . $arr['configName'] . "}' ");
	}

	//create connection
	$link = @mysqli_connect(DB_SERVER_PUBLIC, DB_USERNAME_PUBLIC, DB_PASSWORD_PUBLIC, DB_NAME_PUBLIC);
	return $link;
}
function printExecutionLog(string $log): void {

	ConsoleWriter::consoleLogDelimiter();
	ConsoleWriter::consoleLog($log);
	ConsoleWriter::consoleLogDelimiter();
}
function exitProper(bool $success): void {

	// Exit with a proper status code
	ConsoleWriter::resetColors();
	ConsoleWriter::consoleLogNewLine();
	if ($success) exit(0); // All good
	else exit(1); // Notify the system that the script failed
}