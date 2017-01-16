<?php

// Bootup the Composer autoloader
include __DIR__ . '/vendor/autoload.php';

use Mautic\Auth\ApiAuth;

session_start();

$publicKey = '';
$secretKey = '';
$callback  = '';
$accessTokenData = '';

// ApiAuth::initiate will accept an array of OAuth settings
$settings = array(
    'baseUrl'          => 'https://your-mautic.com',       // Base URL of the Mautic instance
    'version'          => 'OAuth2', // Version of the OAuth can be OAuth2 or OAuth1a. OAuth2 is the default value.
    'clientKey'        => 'YOUR_CLIENT_KEY',       // Client/Consumer key from Mautic
    'clientSecret'     => 'YOUR_CLIENT_SECRET',       // Client/Consumer secret key from Mautic
    'callback'         => 'https://youcallbackurl.com/'        // Redirect URI/Callback URI for this script
);



//
$auth1 = unserialize(file_get_contents('f2.txt')); //get the stored oauth token obtained

// Initiate the auth object


// If you already have the access token, et al, pass them in as well to prevent the need for reauthorization
$settings['accessToken']        = $auth1["access_token"];
//$settings['accessTokenSecret']  = $auth1["access_token_secret"]; //for OAuth1.0a
$settings['accessTokenExpires'] = $auth1["expires"]; //UNIX timestamp
$settings['refreshToken']       = $auth1["refresh_token"];



$initAuth = new ApiAuth();
$auth = $initAuth->newAuth($settings);


clearstatcache();
if(!filesize('filename.txt') || $auth->validateAccessToken()) {



    file_put_contents('f2.txt', serialize($auth->getAccessTokenData())); //store the oauth token for further access
    file_put_contents('filename.txt', serialize($auth)); //dump the auth object

}


// Initiate process for obtaining an access token; this will redirect the user to the $authorizationUrl and/or
// set the access_tokens when the user is redirected back after granting authorization

// If the access token is expired, and a refresh token is set above, then a new access token will be requested

use Mautic\MauticApi;


$apiUrl = "https://your-mautic.com";
//
$api = new MauticApi();
$contactApi = $api->newApi('contacts', unserialize(file_get_contents('filename.txt')), $apiUrl);
$row=1;
if (($handle = fopen("your_csv.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
// the data[$params] are based on the csv exported ..Needs to be customized as per your requirements
        $condata = array(
            'firstname' => $data[1],
            'address' => $data[3],
            'email'     => $data[4],
            'phone' => $data[5],
            'position' => $data[6],
            'city' => $data[7],
            'salary' => $data[8],
            'dob'=>$data[9],
            'activity' => $data[12]
        );

        $contact = $contactApi->create($condata);
        echo("Currently on row"+(string)$row);
        echo("\n");

        $row++;

        echo($data[4]);
        echo("\n");

    }
    fclose($handle);
}


?>