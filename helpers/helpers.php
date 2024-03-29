<?php
if (! function_exists('throw_exception')) {
    /**
     * Throw exception in valid format.
     * Expecially check for status code to be correct
     *
     * @param  Exception $e
     */
    function throw_exception(Exception $e)
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 0);

        if ($statusCode < 100 || $statusCode > 599) {
            $statusCode = 400;
        }

        abort($statusCode, $e->getMessage());
    }
}

if (!function_exists('db_table')) {
    /**
     * Get table name from resource
     *
     * @param  string $resource
     * @return string
     */
    function db_table(string $resource): string
    {
        return config(sprintf('constants.database_tables.%s', $resource));
    }
}
