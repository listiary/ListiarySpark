<?php
namespace SparkLib\Common;
use Exception;

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	require_once __DIR__ . "/Basics.php";
	set_exception_handler(__NAMESPACE__ . '\catchEx');


class Compiler
{
    public static function doPostRequest($url, $code, $filename): string {

        // Base64 encode the source code
        $code_base64 = base64_encode($code);

        //https://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php
        $requestPayload = [
            'command' => 'parse-file',
            'verbosity' => 'low',
            'translator' => 'JSON',
            'filename' => $filename,
            'code' => $code_base64
        ];

        // Convert JSON to string
        $jsonString = json_encode($requestPayload);

        // Base64-encode the JSON string (this is what Lambda expects in request.Body)
        $encodedRequest = base64_encode($jsonString);

        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: text/plain\r\n",
                'method' => 'POST',
                'content' => $encodedRequest,
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    public static function logPostResponse($response): string {

        $log = "";
        if ($response == null)
        {
            $log .= "ERROR : response is NULL\n";
            return $log;
        }
        $log .= "\n\nRaw Response:\n$response\n\n";

        $jArr = json_decode($response, true);
        //var_dump($jArr);

        //json.Result
        if (!isset($jArr["Result"])) $log .= "Result : NULL\n";
        else $log .= "Result : " . $jArr["Result"] . "\n";


        //json.Command
        if (!isset($jArr["Command"])) $log .= "Command : NULL\n";
        else $log .= "Command : " . $jArr["Command"] . "\n";
        $log .= "\n\n";

        //json.Logs
        if (!isset($jArr["Logs"])) $log .= "Logs : NULL\n";
        else $log .= "Logs :\n" . $jArr["Logs"] . "\n\n";
        $log .= "\n";

        //json.Output
        if (!isset($jArr["Output"])) $log .= "Output : NULL\n";
        else $log .= "Output :\n" . $jArr["Output"] . "\n";
        $log .= "\n";

        return $log;
    }
    public static function logArticle($article): string {

        $log = "";
        $log .= "Code :\n" . htmlspecialchars($article);
        $log .= "\n\n\n\n";
        return $log;
    }
    public static function logCode($code): string {

        $log = "";
        $log .= $code;
        $log .= "\n\n\n\n";
        return $log;
    }
}