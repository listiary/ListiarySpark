<?php
namespace SparkCli;
use Exception;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . '/../SparkLib/Common/Basics.php';
    require_once __DIR__ . '/../SparkLib/Common/Validator.php';
    use function SparkLib\Common\{catchEx, connectDb};
    use SparkLib\Common\Validator;
	set_exception_handler('\SparkLib\Common\catchEx');


    enum CliCommand: string {

		case None = "NONE";					                             //no command
		case Unknown = "UNKNOWN";				                         //invalid command
		case Help = "help";					                             //'help', 'h', '-h', '-help', '--help'

		case ConfigMake = "config-make";		                         //'config-make'
        case ConfigMakeOnline = "config-make-online";         		     //'config-make-online'
        case ConfigMakeOffline = "config-make-offline";         		 //'config-make-offline'
        case ConfigShow = "config-show";                                 //'config-show'
        case ConfigTest = "config-test";                                 //'config-test'
        case ConfigTestCompiler = "config-test-compiler";                //'config-test-compiler'
        case ConfigsList = "configs-list";                               //'configs-list'
        
        case FilesList = "files-list";                                   //'files-list'
        case FilesCount = "files-count";                                 //'files-count'
        case FilesDownload = "files-download";                           //'files-download'
        case FilesUpload = "files-upload";                               //'files-upload'
        case FilesCompile = "files-compile";                             //'files-compile'
        case FilesDelete = "files-delete";                               //'files-delete'

        case JsonsList = "jsons-list";                                   //'jsons-list'
        case JsonsCount = "jsons-count";                                 //'jsons-count'
        case JsonsDownload = "jsons-download";                           //'jsons-download'
        case JsonsDelete = "jsons-delete";                               //'jsons-delete'

        case FileView = "file-view";                                     //'file-view'
        case FileDownload = "file-download";                             //'file-download'
        case FileUpload = "file-upload";                                 //'file-upload'
        case FileCompile = "file-compile";                               //'file-compile'
        case FileDelete = "file-delete";                                 //'file-delete'

        case JsonView = "json-view";                                     //'json-view'
        case JsonDownload = "json-download";                             //'json-download'
        case JsonUpload = "json-upload";                                 //'json-upload'
        case JsonDelete = "json-delete";                                 //'json-delete'

        case MetadataView = "metadata-view";                             //'metadata-view'

        case DatabaseTest = "database-test";                             //'database-test'
        case DatabaseMakeTables = "database-make-tables";                //'database-make-tables'
        case DatabaseWipe = "database-wipe";                             //'database-wipe'
        case DatabaseRecompile = "database-recompile";                   //'database-recompile'
        case DatabaseCurate = "database-curate";                         //'database-curate'
        case DatabaseCurateIds = "database-curate-ids";                  //'database-curate-ids'
        case DatabaseCurateFilenames = "database-curate-filenames";      //'database-curate-filenames'
        case DatabaseFindOrphans = "database-find-orphans";              //'database-find-orphans'

        case ActionDeleteArticle = "action-delete-article";              //'action-delete-article'
        case ActionCreateStub = "action-create-stub";                    //'action-create-stub'
	}
    class CommandHandler
    {
        //commands
        private const array COMMANDFLAGS_HELP = ['help', 'h', '-h', '-help', '--help'];
        private const array COMMANDFLAGS_CONFIG_MAKE = ['config-make'];
        private const array COMMANDFLAGS_CONFIG_MAKE_ONLINE = ['config-make-online'];
        private const array COMMANDFLAGS_CONFIG_MAKE_OFFLINE = ['config-make-offline'];
        private const array COMMANDFLAGS_CONFIG_SHOW = ['config-show'];
        private const array COMMANDFLAGS_CONFIG_TEST = ['config-test'];
        private const array COMMANDFLAGS_CONFIG_TEST_COMPILER = ['config-test-compiler'];
        private const array COMMANDFLAGS_CONFIGS_LIST = ['configs-list'];
        private const array COMMANDFLAGS_FILES_LIST = ['files-list'];
        private const array COMMANDFLAGS_FILES_COUNT = ['files-count'];
        private const array COMMANDFLAGS_FILES_DOWNLOAD = ['files-download'];
        private const array COMMANDFLAGS_FILES_UPLOAD = ['files-upload'];
        private const array COMMANDFLAGS_FILES_COMPILE = ['files-compile'];
        private const array COMMANDFLAGS_FILES_DELETE = ['files-delete'];
        private const array COMMANDFLAGS_JSONS_LIST = ['jsons-list'];
        private const array COMMANDFLAGS_JSONS_COUNT = ['jsons-count'];
        private const array COMMANDFLAGS_JSONS_DOWNLOAD = ['jsons-download'];
        private const array COMMANDFLAGS_JSONS_DELETE = ['jsons-delete'];
        private const array COMMANDFLAGS_FILE_VIEW = ['file-view'];
        private const array COMMANDFLAGS_FILE_DOWNLOAD = ['file-download'];
        private const array COMMANDFLAGS_FILE_UPLOAD = ['file-upload'];
        private const array COMMANDFLAGS_FILE_COMPILE = ['file-compile'];
        private const array COMMANDFLAGS_FILE_DELETE = ['file-delete'];
        private const array COMMANDFLAGS_JSON_VIEW = ['json-view'];
        private const array COMMANDFLAGS_JSON_DOWNLOAD = ['json-download'];
        private const array COMMANDFLAGS_JSON_UPLOAD = ['json-upload'];
        private const array COMMANDFLAGS_JSON_DELETE = ['json-delete'];
        private const array COMMANDFLAGS_METADATA_VIEW = ['metadata-view'];
        private const array DATABASE_TEST = ['database-test'];
        private const array DATABASE_MAKE_TABLES = ['database-make-tables'];
        private const array DATABASE_WIPE = ['database-wipe'];
        private const array DATABASE_RECOMPILE = ['database-recompile'];
        private const array DATABASE_CURATE = ['database-curate'];
        private const array DATABASE_CURATE_IDS = ['database-curate-ids'];
        private const array DATABASE_CURATE_FILENAMES = ['database-curate-filenames'];
        private const array DATABASE_FIND_ORPHANS = ['database-find-orphans'];
        private const array ACTION_DELETE_ARTICLE = ['action-delete-article'];
        private const array ACTION_CREATE_STUB = ['action-create-stub'];

        //argument flags - 'config-make', 'config-make-online', 'config-make-offline'
        private const array ARGUMENTFLAGS_CONFIG_MAKE_DATE = ['d', '-d'];
        private const string ARGUMENTFLAGS_CONFIG_MAKE_COMPILER = 'compiler';
        private const string ARGUMENTFLAGS_CONFIG_MAKE_SERVER = 'server';
        private const string ARGUMENTFLAGS_CONFIG_MAKE_DATABASE = 'database';
        private const string ARGUMENTFLAGS_CONFIG_MAKE_USER = 'user';
        private const string ARGUMENTFLAGS_CONFIG_MAKE_PASSWORD = 'password';

        //argument flags - 'files-list', 'files-download'
        private const string ARGUMENTFLAGS_FILES_LIST_MAX = 'max';
        private const string ARGUMENTFLAGS_FILES_DOWNLOAD_MAX = 'max';
        private const string ARGUMENTFLAGS_JSONS_LIST_MAX = 'max';
        private const string ARGUMENTFLAGS_JSONS_DOWNLOAD_MAX = 'max';

        //argument flags - 'metadata-view'
        private const string ARGUMENTFLAGS_METADATA_VIEW_FF = 'ff';
        private const string ARGUMENTFLAGS_METADATA_VIEW_FI = 'fi';
        private const string ARGUMENTFLAGS_METADATA_VIEW_IF = 'if';

        //presentation flags
        private const array ARGUMENTFLAGS_AUTO = ['auto', 'a', '-a', '-auto', '--auto'];
        private const array ARGUMENTFLAGS_HIDE_BANNER = ['hide-banner', 'hb', '-hidebanner', '-hb', '--hide-banner'];
        private const string ARGUMENTFLAGS_THEME = 'theme';
        


        // Get the main command
        public static function GetCommand(array $argv): CliCommand {

            //get string
            $command = isset($argv[1]) ? strtolower($argv[1]) : null;

            //match it with known command flags
            if($command == null) return CliCommand::None;
            else if(in_array($command, self::COMMANDFLAGS_HELP)) return CliCommand::Help;
            else if(in_array($command, self::COMMANDFLAGS_CONFIG_MAKE)) return CliCommand::ConfigMake;
            else if(in_array($command, self::COMMANDFLAGS_CONFIG_MAKE_ONLINE)) return CliCommand::ConfigMakeOnline;
            else if(in_array($command, self::COMMANDFLAGS_CONFIG_MAKE_OFFLINE)) return CliCommand::ConfigMakeOffline;
            else if(in_array($command, self::COMMANDFLAGS_CONFIG_SHOW)) return CliCommand::ConfigShow;
            else if(in_array($command, self::COMMANDFLAGS_CONFIG_TEST)) return CliCommand::ConfigTest;
            else if(in_array($command, self::COMMANDFLAGS_CONFIG_TEST_COMPILER)) return CliCommand::ConfigTestCompiler;
            else if(in_array($command, self::COMMANDFLAGS_CONFIGS_LIST)) return CliCommand::ConfigsList;
            else if(in_array($command, self::COMMANDFLAGS_FILES_LIST)) return CliCommand::FilesList;
            else if(in_array($command, self::COMMANDFLAGS_FILES_COUNT)) return CliCommand::FilesCount;
            else if(in_array($command, self::COMMANDFLAGS_FILES_DOWNLOAD)) return CliCommand::FilesDownload;
            else if(in_array($command, self::COMMANDFLAGS_FILES_UPLOAD)) return CliCommand::FilesUpload;
            else if(in_array($command, self::COMMANDFLAGS_FILES_COMPILE)) return CliCommand::FilesCompile;
            else if(in_array($command, self::COMMANDFLAGS_FILES_DELETE)) return CliCommand::FilesDelete;
            else if(in_array($command, self::COMMANDFLAGS_JSONS_LIST)) return CliCommand::JsonsList;
            else if(in_array($command, self::COMMANDFLAGS_JSONS_COUNT)) return CliCommand::JsonsCount;
            else if(in_array($command, self::COMMANDFLAGS_JSONS_DOWNLOAD)) return CliCommand::JsonsDownload;
            else if(in_array($command, self::COMMANDFLAGS_JSONS_DELETE)) return CliCommand::JsonsDelete;
            else if(in_array($command, self::COMMANDFLAGS_FILE_VIEW)) return CliCommand::FileView;
            else if(in_array($command, self::COMMANDFLAGS_FILE_DOWNLOAD)) return CliCommand::FileDownload;
            else if(in_array($command, self::COMMANDFLAGS_FILE_UPLOAD)) return CliCommand::FileUpload;
            else if(in_array($command, self::COMMANDFLAGS_FILE_COMPILE)) return CliCommand::FileCompile;
            else if(in_array($command, self::COMMANDFLAGS_FILE_DELETE)) return CliCommand::FileDelete;
            else if(in_array($command, self::COMMANDFLAGS_JSON_VIEW)) return CliCommand::JsonView;
            else if(in_array($command, self::COMMANDFLAGS_JSON_DOWNLOAD)) return CliCommand::JsonDownload;
            else if(in_array($command, self::COMMANDFLAGS_JSON_UPLOAD)) return CliCommand::JsonUpload;
            else if(in_array($command, self::COMMANDFLAGS_JSON_DELETE)) return CliCommand::JsonDelete;
            else if(in_array($command, self::COMMANDFLAGS_METADATA_VIEW)) return CliCommand::MetadataView;
            else if(in_array($command, self::DATABASE_TEST)) return CliCommand::DatabaseTest;
            else if(in_array($command, self::DATABASE_MAKE_TABLES)) return CliCommand::DatabaseMakeTables;
            else if(in_array($command, self::DATABASE_WIPE)) return CliCommand::DatabaseWipe;
            else if(in_array($command, self::DATABASE_RECOMPILE)) return CliCommand::DatabaseRecompile;
            else if(in_array($command, self::DATABASE_CURATE)) return CliCommand::DatabaseCurate;
            else if(in_array($command, self::DATABASE_CURATE_IDS)) return CliCommand::DatabaseCurateIds;
            else if(in_array($command, self::DATABASE_CURATE_FILENAMES)) return CliCommand::DatabaseCurateFilenames;
            else if(in_array($command, self::DATABASE_FIND_ORPHANS)) return CliCommand::DatabaseFindOrphans;
            else if(in_array($command, self::ACTION_DELETE_ARTICLE)) return CliCommand::ActionDeleteArticle;
            else if(in_array($command, self::ACTION_CREATE_STUB)) return CliCommand::ActionCreateStub;

            //if we have a presentation argument, that will mean we have no command
            //execution with presentation arguments
            else if(in_array($command, self::ARGUMENTFLAGS_AUTO)) return CliCommand::None;
            else if(in_array($command, self::ARGUMENTFLAGS_HIDE_BANNER)) return CliCommand::None;
            else if(str_starts_with(strtolower($command), self::ARGUMENTFLAGS_THEME)) return CliCommand::None;

            //return
            return CliCommand::Unknown;
        }


        // Read arguments for each command
        public static function ReadCommandArgs(array $argv, CliCommand $command): ?array {

            if($command == CliCommand::Help) return self::readCommandArgs_Help($argv);
            else if($command == CliCommand::ConfigMake) return self::readCommandArgs_ConfigMake($argv);
            else if($command == CliCommand::ConfigMakeOnline) return self::readCommandArgs_ConfigMake($argv);
            else if($command == CliCommand::ConfigMakeOffline) return self::readCommandArgs_ConfigMake($argv);
            else if($command == CliCommand::ConfigShow) return self::readCommandArgs_ConfigShow($argv);
            else if($command == CliCommand::ConfigTest) return self::readCommandArgs_ConfigTest($argv);
            else if($command == CliCommand::ConfigTestCompiler) return self::readCommandArgs_ConfigTest($argv);
            else if($command == CliCommand::ConfigsList) return self::readCommandArgs_ConfigsList($argv);
            else if($command == CliCommand::FilesList) return self::readCommandArgs_FilesList($argv);
            else if($command == CliCommand::FilesCount) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::FilesDownload) return self::readCommandArgs_FilesDownload($argv);
            else if($command == CliCommand::FilesUpload) return self::readCommandArgs_FilesUpload($argv);
            else if($command == CliCommand::FilesCompile) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::FilesDelete) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::JsonsList) return self::readCommandArgs_JsonsList($argv);
            else if($command == CliCommand::JsonsCount) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::JsonsDownload) return self::readCommandArgs_JsonsDownload($argv);
            else if($command == CliCommand::JsonsDelete) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::FileView) return self::readCommandArgs_FileView($argv);
            else if($command == CliCommand::FileDownload) return self::readCommandArgs_FileView($argv);
            else if($command == CliCommand::FileUpload) return self::readCommandArgs_FileUpload($argv);
            else if($command == CliCommand::FileCompile) return self::readCommandArgs_FileView($argv);
            else if($command == CliCommand::FileDelete) return self::readCommandArgs_FileView($argv);
            else if($command == CliCommand::JsonView) return self::readCommandArgs_JsonView($argv);
            else if($command == CliCommand::JsonDownload) return self::readCommandArgs_JsonView($argv);
            else if($command == CliCommand::JsonUpload) return self::readCommandArgs_JsonUpload($argv);
            else if($command == CliCommand::JsonDelete) return self::readCommandArgs_JsonView($argv);
            else if($command == CliCommand::MetadataView) return self::readCommandArgs_MetadataView($argv);
            else if($command == CliCommand::DatabaseTest) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseMakeTables) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseWipe) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseRecompile) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseCurate) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseCurateIds) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseCurateFilenames) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::DatabaseFindOrphans) return self::readCommandArgs_PresentationOnly($argv);
            else if($command == CliCommand::ActionDeleteArticle) return self::readCommandArgs_ActionDeleteArticle($argv);
            else if($command == CliCommand::ActionCreateStub) return self::readCommandArgs_ActionCreateStub($argv);

            else if($command == CliCommand::None) return self::readCommandArgs_None($argv);
            else return null;
        }
        private static function readCommandArgs_PresentationOnly($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'max' => -1 ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_None($argv): array {

            $arguments = array_slice($argv, 1);
            $argCount = count($arguments);
            $result = [ 'success' => true ];

            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_Help($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true ];

            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_ConfigMake($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'configName' => null, 'addDate' => false ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (!$inPresentationTail && in_array($argument, self::ARGUMENTFLAGS_CONFIG_MAKE_DATE))
                {
                    $result['addDate'] = true;
                } 
                else if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_CONFIG_MAKE_COMPILER))
                {
                    $compiler = self::readCompoundArgument($arguments[$i], $i);
                    if($compiler == null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['compiler'] = $compiler;
                }
                else if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_CONFIG_MAKE_SERVER))
                {
                    $server = self::readCompoundArgument($arguments[$i], $i);
                    if($server == null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['server'] = $server;
                }
                else if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_CONFIG_MAKE_DATABASE))
                {
                    $database = self::readCompoundArgument($arguments[$i], $i);
                    if($database == null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['database'] = $database;
                }
                else if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_CONFIG_MAKE_USER))
                {
                    $user = self::readCompoundArgument($arguments[$i], $i);
                    if($user == null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['user'] = $user;
                }
                else if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_CONFIG_MAKE_PASSWORD))
                {
                    $password = self::readCompoundArgument($arguments[$i], $i);
                    if($password == null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['password'] = $password;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && Validator::validateConfigName($arguments[$i]))
                {
                    $result['configName'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_ConfigShow($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'configName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && Validator::validateConfigName($arguments[$i]))
                {
                    $result['configName'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_ConfigTest($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'configName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && Validator::validateConfigName($arguments[$i]))
                {
                    $result['configName'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_ConfigsList($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'configName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_FilesList($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'max' => -1 ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_FILES_LIST_MAX))
                {
                    $max = self::readCompoundArgument($arguments[$i], $i);
                    $fo = ['options' => ['min_range' => -1, 'max_range' => 999998]];
                    if($max == null || Validator::isStringValidInt($max, -1, 999999) !== true)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['max'] = $max;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_FilesDownload($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'max' => -1 ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_FILES_DOWNLOAD_MAX))
                {
                    $max = self::readCompoundArgument($arguments[$i], $i);
                    $fo = ['options' => ['min_range' => -1, 'max_range' => 999998]];
                    if($max == null || Validator::isStringValidInt($max, -1, 999999) !== true)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['max'] = $max;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_FilesUpload($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'task-path' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]); $argument = trim($argument, "'\"");
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if(!$inPresentationTail && $i == 0)
                {
                    $result['task-path'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_JsonsList($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'max' => -1 ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_JSONS_LIST_MAX))
                {
                    $max = self::readCompoundArgument($arguments[$i], $i);
                    $fo = ['options' => ['min_range' => -1, 'max_range' => 999998]];
                    if($max == null || Validator::isStringValidInt($max, -1, 999999) !== true)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['max'] = $max;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_JsonsDownload($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'max' => -1 ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if(!$inPresentationTail && str_starts_with($argument, self::ARGUMENTFLAGS_JSONS_DOWNLOAD_MAX))
                {
                    $max = self::readCompoundArgument($arguments[$i], $i);
                    $fo = ['options' => ['min_range' => -1, 'max_range' => 999998]];
                    if($max == null || Validator::isStringValidInt($max, -1, 999999) !== true)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    else $result['max'] = $max;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_FileView($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'fileName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && Validator::validateCanonicalDsFileName($arguments[$i]))
                {
                    $result['fileName'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_FileUpload($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'filePath' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0)
                {
                    $result['filePath'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_JsonView($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'jsonName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && Validator::validateCanonicalDsFileName($arguments[$i]))
                {
                    $result['jsonName'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_JsonUpload($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'jsonPath' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0)
                {
                    $result['jsonPath'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_MetadataView($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'viewFlag' => null, 'item' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && $argument == "ff") $result['viewFlag'] = "ff";
                else if($i == 0 && $argument == "fi") $result['viewFlag'] = "fi";
                else if($i == 0 && $argument == "if") $result['viewFlag'] = "if";
                else if($i == 1)
                {
                    $result['item'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            if($result['viewFlag'] == null)
            {
                $result['error'] = "No view flag first argument.";
                $result['success'] = false;
            }
            return $result;
        }
        private static function readCommandArgs_ActionDeleteArticle($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'fileName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0 && Validator::validateCanonicalDsFileName($arguments[$i]))
                {
                    $result['fileName'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }
        private static function readCommandArgs_ActionCreateStub($argv): array {

            $arguments = array_slice($argv, 2);
            $argCount = count($arguments);
            $result = [ 'success' => true, 'fileName' => null ];

            $inPresentationTail = false;
            for ($i = 0; $i < $argCount; $i++) 
            {
                $argument = strtolower($arguments[$i]);
                if (in_array($argument, self::ARGUMENTFLAGS_AUTO))
                {
                    ConsoleWriter::$manualMode = false;
                    $result['manualMode'] = false;
                    $inPresentationTail = true;
                }
                else if (in_array($argument, self::ARGUMENTFLAGS_HIDE_BANNER))
                {
                    ConsoleWriter::$printBanner = false;
                    $result['printBanner'] = false;
                    $inPresentationTail = true;
                }
                else if(str_starts_with($argument, self::ARGUMENTFLAGS_THEME))
                {
                    $theme = self::readThemeArgument($argument, $i);
                    if($theme === null)
                    {
                        $result['error'] = "invalid argument No'{$i}'";
                        $result['success'] = false;
                        return $result;
                    }
                    $inPresentationTail = true;
                }
                else if($i == 0)
                {
                    $result['item'] = $arguments[$i];
                }
                else if($i == 1 && Validator::validateCanonicalDescribeNamespaceName($arguments[$i]))
                {
                    $result['namespace'] = $arguments[$i];
                }
                else
                {
                    $result['error'] = "invalid argument No'{$i}'";
                    $result['success'] = false;
                }
            }

            return $result;
        }

  

        //readers
        private static function readThemeArgument(string $arg, int $argindex): string {

            try
            {
                // Find the position of the "=" character
                $eqPosition = strpos($arg, '=');
                if ($eqPosition === false)
                {
                    ConsoleWriter::printArgumentError($arg, $argindex, "");
                    return false;
                }

                // Extract everything after the "="
                $val = substr($arg, $eqPosition + 1);

                // Check if empty
                if (trim($val) === '') 
                {
                    ConsoleWriter::printArgumentError($arg, $argindex, "");
                    return null;
                }

                // Check if it is a valid theme
                $val = strtoupper($val);
                if ($val == "DBLUE") { ConsoleWriter::SetDarkBlueTheme(); return "DBLUE"; }
                else if ($val == "LBLUE") { ConsoleWriter::SetLightBlueTheme(); return "LBLUE"; }
                else if ($val == "GREEN") { ConsoleWriter::SetGreenTheme(); return "GREEN"; }
                else if ($val == "PASTEL") { ConsoleWriter::SetPastelTheme(); return "PASTEL"; }
                else if ($val == "EARTH") { ConsoleWriter::SetEarthTheme(); return "EARTH"; }
                else if ($val == "CONTRAST") { ConsoleWriter::SetHighContrastTheme(); return "CONTRAST"; }
                else if ($val == "DEFAULT") { ConsoleWriter::SetDefaultTheme(); return "DEFAULT"; }
                else if ($val == "VIOLET") { ConsoleWriter::SetVioletTheme(); return "VIOLET"; }
                else if ($val == "CYAN") { ConsoleWriter::SetCyanTheme(); return "CYAN"; }
                else
                {
                    ConsoleWriter::printArgumentError($arg, $argindex, "invalid value \"" + $val + "\"");
                    return null;
                }

                return null;
            }
            catch (Throwable $ex)
            {
                ConsoleWriter::printArgumentError($arg, $argindex, $ex.Message);
                return null;
            }
        }
        private static function readCompoundArgument(string $arg, int $argindex): string {

            try
            {
                // Find the position of the "=" character
                $eqPosition = strpos($arg, '=');
                if ($eqPosition === false)
                {
                    ConsoleWriter::printArgumentError($arg, $argindex, "");
                    return false;
                }

                // Extract everything after the "="
                $val = substr($arg, $eqPosition + 1);

                // Check if empty
                if (trim($val) === '') 
                {
                    ConsoleWriter::printArgumentError($arg, $argindex, "");
                    return null;
                }

                //validate $val really
                if(true)
                {
                    return $val;
                }

                return null;
            }
            catch (Throwable $ex)
            {
                ConsoleWriter::printArgumentError($arg, $argindex, $ex.Message);
                return null;
            }
        }
    }
    