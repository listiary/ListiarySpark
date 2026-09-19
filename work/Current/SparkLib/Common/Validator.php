<?php
namespace SparkLib\Common;
use Exception;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	require_once __DIR__ . "/Basics.php";
    set_exception_handler(__NAMESPACE__ . '\catchEx');

class Validator
{
    public static function validateParameterExists(array $parameters, string $param) {

        if (!array_key_exists($param, $parameters)) 
        {
            throw new Exception("Missing '{$param}' parameter");
        }
    }
    public static function validateParametersExist(array $parameters, string ...$params) {

        foreach ($params as $param) 
        {
            self::validateParameterExists($parameters, $param);
        }
    }
    public static function validateParameterNotEmpty(array $parameters, string $param) {

        if (self::isStringNullOrEmpty($parameters[$param])) 
        {
            throw new Exception("Invalid '{$param}' parameter");
        }
    }
    public static function validateParametersNotEmpty(array $parameters, string ...$params) {

        foreach ($params as $param) 
        {
            self::validateParameterNotEmpty($parameters, $param);
        }
    }
    public static function validateParameterIsCanonicalDsName(array $parameters, string $param) {

        if (!self::validateCanonicalDsFileName($parameters[$param]))
        {
            throw new Exception("Invalid '{$param}' parameter");
        }
    }
    public static function validateParameterIsIntInRange(array $parameters, string $param, int $min, int $max) {

        if (!self::isStringValidInt($parameters[$param], $min, $max))
        {
            throw new Exception("Invalid '{$param}' parameter");
        }
    }
    public static function validateParameterIsStringInRange(array $parameters, string $param, int $min, int $max) {

        if (!self::validateStringByteLength($parameters[$param], 3, 500000))
        {
            throw new Exception("Invalid '{$param}' parameter length");
        }
    }
    public static function validateParameterIsUtf8String(array $parameters, string $param) {

        if (!self::validateStringEncodingUtf8($parameters[$param])) 
        {
            throw new Exception("Invalid '{$param}' encoding");
        }
    }


    public static function validateConfigName(?string $name): bool {

        if(self::isStringNullOrEmpty($name)) return false;
        return preg_match('/^(?=.*[a-zA-Z0-9])(?!-)[a-zA-Z0-9_.()-]*(?<![-.])$/', $name) === 1;
    }
    public static function isStringNullOrEmpty(?string $s): bool {

        if(is_null($s)) return true;
        if(trim($s) === '') return true;
        return false;
    }
    public static function isStringValidInt(?string $s, int $min, int $max): bool {

        $fo = ['options' => ['min_range' => $min, 'max_range' => $max]];
        if(filter_var($s, FILTER_VALIDATE_INT, $fo) === false) return false;
        return true;
    }
    public static function validateLocalFolderName(?string $s): bool {

        if ($s === null || trim($s) === '') {
            return false;
        }

        // 2. Check if the path exists AND is a directory
        if(!is_dir($s)) return false;

        // 3. Check if the directory is writeable
        return is_writable($s); 
    }


    public static function validateCanonicalDsFileName(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Matches letters, numbers, dots, and hyphens, and underscores
        return (bool) preg_match('/^[a-zA-Z0-9._-]+$/', $s);
    }
    public static function validateCanonicalDsFileNameExt(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Matches letters, numbers, dots, and hyphens, and underscores ending in .ds or .DS 
        return (bool) preg_match('/^[a-zA-Z0-9._-]+\.[dD][sS]$/', $s);
    }
    public static function validateCanonicalDescribeNamespaceName(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Matches letters, numbers, dots, and hyphens, and underscores
        return (bool) preg_match('/^[a-zA-Z0-9._-]+$/', $s);
    }
    public static function validateLocalFileName(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Check if the path exists AND is a file
        return is_file($s);
    }
    public static function validateLocalFileReadable(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Check if the path exists AND is a file
        if(!is_readable($s)) return false;

        // Check if the file is writeable
        return is_writable($s); 
    }
    public static function validateLocalFileWriteable(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Check if the path exists AND is a file
        if(!is_file($s)) return false;

        // Check if the file is writeable
        return is_writable($s); 
    }
    public static function validateStringByteLength(?string $s, int $min, int $max): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Check byte length
        $size = strlen($s);
        if ($size < $min || $size > $max) return false;

        return true;
    }
    public static function validateStringEncodingUtf8(?string $s): bool {

        // Check if string is not empty or null
        if ($s === null || trim($s) === '') return false;

        // Check encoding
        return mb_check_encoding($s, 'UTF-8');
    }



    public static function validateDescribeItemId($fileName) {
		
		if (preg_match('/^[\p{L}0-9._-]+$/u', $fileName)) return true;
		return false;
	}
	public static function validateDescribeFilename($fileName) {
		
		if (preg_match('/^[\p{L}0-9._-]+$/u', $fileName)) return true;
		return false;
	}
	public static function validateDescribeNamespace($namespace) {
		
		//same as filename, but first or last symbol cannot be dots
		if (preg_match('/^(?!\.)[\p{L}0-9._-]+(?<!\.)$/u', $namespace)) return true;
		return false;
	}
	public static function validateDescribeFilenameLatin($fileName) {
		
		if (preg_match('/^[a-zA-Z0-9._-]+$/', $fileName)) return true;
		return false;
	}
}