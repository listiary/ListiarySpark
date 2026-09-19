<?php
namespace SparkCli;
use Exception;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	require_once __DIR__ . '/../SparkLib/Common/Basics.php';
    use function SparkLib\Common\{catchEx, connectDb};
	set_exception_handler('\SparkLib\Common\catchEx');


// class SparkCliCommandInfo
// {
//     public array $Usage;
//     public array $Description;
//     public array $ArgumentDescription;
// }
class CommandInformer
{
    public static array $Commands = [
        [ //help
            'Usage' => [
                "help [<PRESENTATION_FLAGS>] |",
                "-h [<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "Display this help message."
            ],
            'ArgumentDescription' => []
        ],
        [ //config-make
            'Usage' => [
                "config-make [CONFIG_NAME][-d]",
                "[compiler=\"<VALUE>\"][server=\"<VALUE>\"][database=\"<VALUE>\"][user=\"<VALUE>\"][password=\"<VALUE>\"]",
                "[<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "Create a new config file for a Listiary database connection,",
                "While running few tests against the actual database server,",
                "And save it in the configs folder."
            ],
            'ArgumentDescription' => [
                "'CONFIG_NAME' - set a custom name for the config",
                "'-d' flag - a config name including a timestamp in the filename.",
                "compiler - provides the compiler url for the config, instead of asking the user for the input.",
                "server - provides the SQL server name for the config, instead of asking the user for the input.",
                "database - provides the SQL database for the config, instead of asking the user for the input.",
                "user - provides the SQL database username for the config, instead of asking the user for the input.",
                "password - provides the SQL user's password for the config, instead of asking the user for the input.",
            ]
        ],
        [ //config-make-online
            'Usage' => [
                "config-make-online [CONFIG_NAME][-d]",
                "[compiler=\"<VALUE>\"][server=\"<VALUE>\"][database=\"<VALUE>\"][user=\"<VALUE>\"] [password=\"<VALUE>\"]",
                "[<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "Create a new config file for a Listiary database connection,",
                "While running few tests against the actual database server,",
                "And save it in the configs folder."
            ],
            'ArgumentDescription' => [
                "'CONFIG_NAME' - set a custom name for the config",
                "'-d' flag - a config name including a timestamp in the filename.",
                "compiler - provides the compiler url for the config, instead of asking the user for the input.",
                "server - provides the SQL server name for the config, instead of asking the user for the input.",
                "database - provides the SQL database for the config, instead of asking the user for the input.",
                "user - provides the SQL database username for the config, instead of asking the user for the input.",
                "password - provides the SQL user's password for the config, instead of asking the user for the input.",
            ]
        ],
        [ //config-make-offline
            'Usage' => [
                "config-make-offline [CONFIG_NAME][-d]",
                "[compiler=\"<VALUE>\"][server=\"<VALUE>\"][database=\"<VALUE>\"][user=\"<VALUE>\"][password=\"<VALUE>\"]",
                "[<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "Create a new config file for a Listiary database connection,",
                "And save it in the configs folder."
            ],
            'ArgumentDescription' => [
                "'CONFIG_NAME' - set a custom name for the config",
                "'-d' flag - a config name including a timestamp in the filename.",
                "compiler - provides the compiler url for the config, instead of asking the user for the input.",
                "server - provides the SQL server name for the config, instead of asking the user for the input.",
                "database - provides the SQL database for the config, instead of asking the user for the input.",
                "user - provides the SQL database username for the config, instead of asking the user for the input.",
                "password - provides the SQL user's password for the config, instead of asking the user for the input.",
            ]
        ],
        [ //configs-list
            'Usage' => [
                "configs-list [<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "List all the configs in the configs folder.",
            ],
            'ArgumentDescription' => []
        ],
        [ //config-show
            'Usage' => [
                "config-show [CONFIG_NAME][<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "Show the constants inside the selected config.",
                "If none is selected, it will try to show the contents of a default config named 'config.php'."
            ],
            'ArgumentDescription' => [
                "'CONFIG_NAME' - set a custom name for the config"
            ]
        ],
        [ //config-test
            'Usage' => [
                "config-test [CONFIG_NAME][<PRESENTATION_FLAGS>]"
            ],
            'Description' => [
                "Test the selected config against the backend servers.",
                "If none is selected, it will try to test the a default config named 'config.php'."
            ],
            'ArgumentDescription' => [
                "'CONFIG_NAME' - set a custom name for the config"
            ]
        ],
    ];
}