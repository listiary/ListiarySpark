<?php
namespace SparkLib\Common;
use Exception, mysqli, mysqli_sql_exception, RuntimeException, Throwable;


	define("NEW_LINE", "\n");


	/**
	 * Open a connection to the DB
	 * @return mysqli The connection object
	 */
	function connectDb(): mysqli {

		static $connection = null;

		if ($connection === null) 
		{
			try
			{
				$connection = new mysqli(
					DB_SERVER_PUBLIC,
					DB_USERNAME_PUBLIC,
					DB_PASSWORD_PUBLIC,
					DB_NAME_PUBLIC
				);
			} 
			catch (mysqli_sql_exception $e) 
			{
				// Show generic message
				throw new RuntimeException('Database connection failed.');
			}
		}

		//return
		return $connection;
	}

	/**
	 * Default Exception handler
	 * @param Throwable $ex The exception handle
	 * @return void
	 */
	function catchEx(Throwable $ex): void {

		if (PHP_SAPI === 'cli') logExToConsole($ex);
		else logExToSite($ex);
		exit(1); // Exit with error code
	}
	function logExToConsole(Throwable $ex): void {
		
		if (PHP_SAPI === 'cli') 
		{
			// CLI Error Output
			fwrite(STDERR, "FATAL ERROR: " . $ex->getMessage() . PHP_EOL);
			fwrite(STDERR, $ex->getTraceAsString() . PHP_EOL);
		}
	}
	function logExToSite(Throwable $ex): void {
		
		echo "FATAL ERROR: " . $ex->getMessage();
		echo "<br>";
		echo $ex->getTraceAsString();
	}
	function logExToText(Throwable $ex): void {
		
		echo "FATAL ERROR: " . $ex->getMessage();
		echo "\n";
		echo $ex->getTraceAsString();
	}
	function logExToVar(Throwable $ex): string {
		
		$msg = "FATAL ERROR: " . $ex->getMessage();
		$msg .= "\n";
		$msg .= $ex->getTraceAsString();
	}