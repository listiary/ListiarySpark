<?php
namespace SparkCli;

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	require_once __DIR__ . '/SparkCli/CliCore.php';
	require_once __DIR__ . '/SparkLib/Common/Basics.php';
	use function SparkLib\Common\{catchEx, connectDb};
	set_exception_handler('SparkLib\Common\catchEx');


	Execute($argv);
