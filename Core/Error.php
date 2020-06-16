<?php

namespace Core;

/**
 * Handler de Errores y Excepciones
 *
 * PHP version 7.3.6
 */
class Error
{

    /**
     * Handler de Errores. Convierte los errores a excepciones lanzando ErrorException
     *
     * @param int $level  Nivel del Error
     * @param string $message  Mensaje de Error
     * @param string $file  Archivo donde el error salió
     * @param int $line  Número de linea de código
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handler de Excepciones
     *
     * @param Exception $exception  La Excepción
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);


        if (\App\Config::SHOW_ERRORS) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            //date_default_timezone_set('GMT');
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);
            //echo "<h1>An error occurred</h1>";
            /*
            if ($code == 404) {
                echo "<h1>Page not found</h1>";
            } else {
                echo "<h1>An error occurred</h1>";
            }
            */
            View::renderTemplate("$code.html");
        }
    }
}
