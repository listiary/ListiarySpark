<?php
namespace SparkCli;
use Exception;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . "/CommandInformer.php";
    require_once __DIR__ . '/../SparkLib/Common/Basics.php';
    use function SparkLib\Common\{catchEx, connectDb};
	set_exception_handler('\SparkLib\Common\catchEx');

class ConsoleWriter 
{
	public static bool $manualMode = true;
    public static bool $printBanner = true;
    public static bool $isThemeSet = false;
	
	private static string $log = "";
	private const string SPARK_VERSION  = "0.3";
	private const string LINE_DELIMITER = "-----------------------------------------------------------------";
    private const bool ONE_BASED_ARG_INDEX = true;
	private static string $INFO_COLOR = "\033[33m";
    private static string $ERROR_COLOR = "\033[31m";
    private static string $MOREINFO_COLOR = "\033[36m";
    private static string $TEXT_COLOR = "\033[0m";

	// Ctor - Prevent the class from being instantiated
    private function __construct() {}



	//prints
	public static function printBanner(bool $block = false): void {

        if(self::$printBanner === true)
        {
            // https://www.asciiart.eu/text-to-ascii-art - Isometric2
            self::consoleLog(PHP_EOL);
            self::consoleLogInfo("      ___           ___         ___           ___           ___      ");
            self::consoleLogInfo("     /\__\         /\  \       /\  \         /\  \         /\  \     ");
            self::consoleLogInfo("    /:/ _/_       /::\  \     /::\  \       /::\  \        \:\  \    ");
            self::consoleLogInfo("   /:/ /\  \     /:/\:\__\   /:/\:\  \     /:/\:\__\        \:\__\   ");
            self::consoleLogInfo("  /:/ /::\  \   /:/ /:/  /  /:/ /::\  \   /:/ /:/__/_   __  /:/__/_  ");
            self::consoleLogInfo(" /:/_/:/\:\__\ /:/_/:/  /  /:/_/:/\:\__\ /:/_/:/\:\__\ /\ \/:/\:\__\ ");
            self::consoleLogInfo(" \:\/:/ /:/  / \:\/:/  /   \:\/:/  \/__/ \:\/:/  \/__/ \:\/:/  \/__/ ");
            self::consoleLogInfo("  \::/ /:/  /   \::/__/     \::/__/       \::/__/       \::/__/      ");
            self::consoleLogInfo("   \/_/:/  /     \:\  \      \:\  \        \:\  \        \:\  \      ");
            self::consoleLogInfo("     /:/  /       \:\__\      \:\__\        \:\__\        \:\__\     ");
            self::consoleLogInfo("     \/__/         \/__/       \/__/         \/__/         \/__/     ");
            self::consoleLogInfo(PHP_EOL);
        }
        else self::consoleLogNewLine();

		//block
		if ($block && self::$manualMode) self::consoleBlock();
	}	
	public static function printSparkDescription(bool $block = true): void {

		self::consoleLog("Running Spark framework v " . self::SPARK_VERSION);
		self::consoleLog("Spark is an administrative CLI interface for administering the Listiary database layer.");
		self::consoleLog("Spark is very powerful and unforgiving - be careful which script you are invoking and read the documentation.");
		self::consoleLog("Run '" . self::getScriptName() . " -h' for more information");

		//block
		if ($block && self::$manualMode) self::consoleBlock();
	}
	public static function printHelpMessage(?string $error = null, bool $block = true): void {

		if($error) self::consoleLogError($error);

		self::consoleLog("about: Spark framework v " . self::SPARK_VERSION);
		self::consoleLog("Spark is an administrative CLI interface for administering the Listiary database layer.");
		self::consoleLog("Spark is very powerful and unforgiving - be careful which script you are invoking and read the documentation.");
		//Spark is licensed under ...
		//For more information visit documentation.listiary.org/...

        $firstWordOfCommand = "";
        foreach (CommandInformer::$ShortCommands as $Command)
        {
            $fi = strtok($Command, '-');
            if($firstWordOfCommand !== $fi && $firstWordOfCommand . 's' !== $fi) 
            {
                self::consoleLogNewLine();
                $firstWordOfCommand = $fi;
            }
            self::consoleLogInfo(self::getScriptName() . " " . $Command);
        }
        return;

        foreach (CommandInformer::$Commands as $Command)
        {
            self::consoleLogNewLine(); self::consoleLogNewLine(); self::consoleLogNewLine();

            self::consoleLogInfo(self::getScriptName() . " " . $Command['Usage'][0]);
            $extraUsageLines = array_slice($Command['Usage'], 1);
            foreach ($extraUsageLines as $ExtraLine) self::consoleLogInfo($ExtraLine);
            self::consoleLogNewLine();
            
            $descriptionLines = array_slice($Command['Description'], 0);
            foreach ($descriptionLines as $descr) self::consoleLog($descr);
            self::consoleLogNewLine();

            $argumentLines = array_slice($Command['ArgumentDescription'], 0);
            foreach ($argumentLines as $argl) self::consoleLogMoreInfo($argl);
            self::consoleLog(self::LINE_DELIMITER);
        }

		self::consoleLogNewLine(); self::consoleLogNewLine(); self::consoleLogNewLine();
        self::consoleLogInfo("about: <PRESENTATION_FLAGS>");
        self::consoleLogMoreInfo("The 'auto' flag - remove the colors, for use in scripting");
        self::consoleLogMoreInfo("The 'hide-banner' flag - remove the ASCII art banner, for use in scripting.");
        self::consoleLogMoreInfo("The 'theme=THEME' flag - set a color theme for the output.");
        self::consoleLogNewLine();

		//block
		if ($block && self::$manualMode) self::consoleBlock();
	}
    public static function printCmdLine(array $args): void {

        $s = "> ";
        $count = count($args);

        if ($count > 0) 
        {
            //no args
            $s .= $args[0];
            if ($count === 1)
            {
                self::consoleLogInfo($s);
                return;
            }

            //single arg - the called function name - like 'help'
            $s .= ' ' . $args[1];
            if ($count === 2)
            {
                self::consoleLogInfo($s);
                return;
            }


            // Array of prefixes we want to wrap values in quotes for
            //$quotePrefixes = ['password=', 'input-password=', 'output-password=', 'log-password=', 'log-file=', 'logfile='];
            $quotePrefixes = ['server=', 'database=', 'user=', 'password='];

            for ($i = 2; $i < $count; $i++) 
            {
                $arg = $args[$i];
                $matched = false;

                foreach ($quotePrefixes as $prefix)
                {
                    if (str_starts_with($arg, $prefix))
                    {
                        // Extract the value and wrap it in quotes
                        $value = substr($arg, strlen($prefix));
                        $s .= ' ' . $prefix . '"' . $value . '"';
                        $matched = true;
                        break;
                    }
                }

                // If it didn't match any of our prefixes, just append the raw argument
                if (!$matched) $s .= ' ' . $arg;
            }
        }

        self::consoleLogInfo($s);
    }
    public static function printWarning(string $message, bool $block = true): void {

        $msg = "Warning: " . $message;
        self::consoleLogInfo($msg);

        //block
		if ($block && self::$manualMode) self::consoleBlock();
    }
    public static function printNoArgumentsError(bool $block = true): void {

        $msg = "No arguments or invalid argument count.";
        self::consoleLogError($msg);
        self::printHelpMessage(false);
        
        //block
		if ($block && self::$manualMode) self::consoleBlock();
    }
    public static function printArgumentError(string $arg, int $argIndex, bool $block = true, ?string $message = null): void {

        if (self::ONE_BASED_ARG_INDEX) $argIndex++;

        $msg = null;
        if($message !== null && trim($message) !== '')
        {
            $msg = "Invalid argument " . $argIndex . " - \"" . $arg . "\" - " . $message;
        }
        else
        {
            $msg = "Invalid argument " . $argIndex . " - \"" . $arg . "\"";
        }

        //log
        self::consoleLogError($msg);
        self::printHelpMessage(false);

        //block
		if ($block && self::$manualMode) self::consoleBlock();
    }
    public static function printFatalError(string $message, bool $block = true): void {

        $msg = "Fatal error: " . $message;
        self::consoleLogError($msg);

        //block
		if ($block && self::$manualMode) self::consoleBlock();
    }



	//themes
    public static function resetColors(): void {

        echo "\e[0m\e[K";
        //echo "\e[0m" . "Back to normal text!";
    }
	public static function setGreenTheme(): void {

        self::$INFO_COLOR     = "\033[92m"; // Green
        self::$TEXT_COLOR     = "\033[93m"; // Yellow
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[90m"; // Dark Gray
        self::$log .= self::$TEXT_COLOR;
        echo self::$TEXT_COLOR;
        $isThemeSet = true;
    }
    public static function setSunSeaTheme(): void {

        self::$INFO_COLOR       = "\033[33m"; // Yellow
        self::$ERROR_COLOR      = "\033[31m"; // Red
        self::$MOREINFO_COLOR   = "\033[36m"; // Cyan
        self::$TEXT_COLOR       = "\033[0m";  // White
        self::$log .= self::$TEXT_COLOR;
        echo self::$TEXT_COLOR;
        $isThemeSet = true;
    }
    public static function setPastelTheme(): void {

        self::$INFO_COLOR     = "\033[36m"; // Dark Cyan
        self::$TEXT_COLOR     = "\033[95m"; // Magenta
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[96m"; // Cyan
    }
    public static function setEarthTheme(): void {

        self::$INFO_COLOR     = "\033[33m"; // Dark Yellow
        self::$TEXT_COLOR     = "\033[32m"; // Dark Green
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[37m"; // Gray
    }
    public static function setHighContrastTheme(): void {

        self::$INFO_COLOR     = "\033[97m"; // White
        self::$TEXT_COLOR     = "\033[93m"; // Yellow
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[92m"; // Green
    }
	public static function setDefaultTheme(): void {

        self::$INFO_COLOR     = "\033[90m"; // Dark Gray
        self::$TEXT_COLOR     = "\033[97m"; // White
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[92m"; // Green
    }
    public static function setVioletTheme(): void {

        self::$INFO_COLOR     = "\033[35m"; // Dark Magenta
        self::$TEXT_COLOR     = "\033[97m"; // White
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[90m"; // Dark Gray
    }
    public static function setCyanTheme(): void {

        self::$INFO_COLOR     = "\033[35m"; // Dark Magenta
        self::$TEXT_COLOR     = "\033[96m"; // Cyan
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[36m"; // Dark Cyan
    }
    public static function setLightBlueTheme(): void {

        self::$INFO_COLOR     = "\033[97m"; // White
        self::$TEXT_COLOR     = "\033[94m"; // Blue
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[36m"; // Dark Cyan
    }
    public static function setDarkBlueTheme(): void {

        self::$INFO_COLOR     = "\033[90m"; // Dark Gray
        self::$TEXT_COLOR     = "\033[34m"; // Dark Blue
        self::$ERROR_COLOR    = "\033[31m"; // Red
        self::$MOREINFO_COLOR = "\033[36m"; // Dark Cyan
    }

	
	
	//getters
	public static function getLog(): string {
		
        return self::$log;
    }
	public static function getScriptName(): string {
		
        $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
		return $scriptName;
    }



	//logging
	public static function consoleLog(string $text): void {
    
		self::$log .= $text . PHP_EOL;
        echo $text . PHP_EOL;
    }
	public static function consoleLogInfo(string $text): void {

        self::$log .= $text . PHP_EOL;
        if (self::$manualMode) echo self::$INFO_COLOR;
        echo $text . PHP_EOL;
        if (self::$manualMode) echo self::$TEXT_COLOR;
    }
    public static function consoleLogError(string $text): void {
    
		self::$log .= $text . PHP_EOL;
        if (self::$manualMode) echo self::$ERROR_COLOR;
        echo $text . PHP_EOL;
        if (self::$manualMode) echo self::$TEXT_COLOR;
    }
    public static function consoleLogMoreInfo(string $text): void {

        self::$log .= $text . PHP_EOL;
        if (self::$manualMode) echo self::$MOREINFO_COLOR;
        echo $text . PHP_EOL;
        if (self::$manualMode) echo self::$TEXT_COLOR;
    }
    public static function consoleLog_NoNL(string $text): void {
    
		self::$log .= $text;
        echo $text;
    }
    public static function consoleLogNewLine(): void {
    
		self::$log .= PHP_EOL;
        echo PHP_EOL;
    }
    public static function consoleLogDelimiter(): void {
    
		self::$log .= self::LINE_DELIMITER . PHP_EOL;
        echo self::LINE_DELIMITER . PHP_EOL;
    }
	public static function consoleBlock(): void {

		self::$log .= "Press any key to exit." . PHP_EOL;
		if (self::$manualMode) echo self::$INFO_COLOR;
		readline("Press any key to exit...");
		if (self::$manualMode) echo self::$TEXT_COLOR;
    }
}