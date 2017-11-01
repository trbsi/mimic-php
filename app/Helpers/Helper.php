<?php

namespace App\Helpers;

class Helper
{
    /**
     * format date: Y-m-d H:i:s
     * @param $date
     * @return bool|string
     */
    public static function formatDate($date)
    {
        //return date("M d, Y h:i:s a"); December 12, 1999 12:00:01 pm
        return date("h:i:s a", strtotime($date));
    }

    /**
     * Get data from a specific provider FB/Twitter and format it for me
     * @param  string $provider Name of a provider "facebook"/"twitter"
     * @param  object $provider_data Object containing provider data
     * @return array Formatted data
     */
    public static function getOauthProviderData($provider, $provider_data)
    {
        if ($provider == "facebook") {
            return
                [
                    'provider' => $provider,
                    'provider_id' => $provider_data["id"],
                    'email' => (!isset($provider_data["email"]) || empty($provider_data["email"])) ? NULL : $provider_data["email"],
                    'profile_picture' => $provider_data["picture"]["data"]["url"],
                ];
        }

        if ($provider == "twitter") {
            return
                [
                    'provider' => $provider,
                    'provider_id' => $provider_data["id"],
                    'email' => (!isset($provider_data["email"]) || empty($provider_data["email"])) ? NULL : $provider_data["email"],
                    'profile_picture' => $provider_data["profile_image_url"],
                ];
        }
    }
    
    /**
     * Format numbers, shorten them to K/M/B
     * @param  [type]  $n         [description]
     * @param  integer $precision [description]
     * @return [type]             [description]
     */
    public static function numberFormat($n, $precision = 0)
    {
        if ($n < 1000) {
            // Anything less than a million
            $n_format = number_format($n);
        } else if ($n < 1000000) {
            $n_format = number_format($n / 1000, $precision) . 'K';
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision) . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision) . 'B';
        }

        return $n_format;
    }
}
