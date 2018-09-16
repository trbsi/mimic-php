<?php

namespace Tests\Assert;

abstract class AssertAbstract
{
    /**
     * @return array
     */
    public function getAssertJsonStructureOnError(): array
    {
        return [
            'error' => [
                'message',
                'status_code'
            ]
        ];
    }

    /**
     * @param string $message
     * @return array
     */
    public function getAssertJsonOnError(string $message): array
    {
        return [
            'error' => [
                'message' => $message
            ]
        ];
    }

    /**
     * @param  array
     * @return array
     */
    public function getAssertJsonStructureOnUnprocessableEntityError(array $errors): array
    {
        return [
            'error' => [
                'message',
                'errors' => $errors,
                'status_code',
            ],
        ];
    }

    /**
     * @param  array
     * @return array
     */
    public function getAssertJsonOnUnprocessableEntityError(array $errors): array
    {
        return [
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => $errors,
                'status_code' => 422,
            ],
        ];
    }

    /**
     * Get json file and decode it
     *
     * @param  string $file Path to a json file
     * @return array
     */
    public static function getDecodedJsonDataFromFile(string $filePath): array
    {
        $data = file_get_contents($filePath);
        return json_decode($data, true);
    }

    /**
     * Get json file, alter it and decode it
     *
     * @param  string $file Path to a json file
     * @param  array $from Used for str_replace
     * @param  array $to Used for str_replace
     * @return array
     */
    public static function getDecodedJsonDataFromFileByAlteringFile(string $filePath, array $from, array $to): array
    {
        $json = file_get_contents($filePath);
        //find strings in json and  decode it
        $json = str_replace($from, $to, $json);
        return json_decode($json, true);
    }
}
