<?php

session_start();

include_once "FlickrPhotoServiceAuthenticator.php";

FlickrPhotoServiceAuthenticator::saveSession("flickr_return_url", $_GET['rUrl']);

printf("<html>");
printf("<head>");
printf("<title>Flickr Authorization Page</title>");
printf("</head>");
printf("<body>");
printf("<p>Please click on the following link to log into your Flickr account and authorize this application to access your account:</p>");
$authToken = FlickrPhotoServiceAuthenticator::getSession("flickr_auth_token");

if ($authToken != null) {
    printf("<p>Already logged in.</b>");
} else {
    $apiKey = FlickrPhotoServiceAuthenticator::getApiKey();
    $perms = "delete";
    $sign_params = array();
    $sign_params["api_key"] = $apiKey;
    $sign_params["perms"] = $perms;
    $apiSig = FlickrPhotoServiceAuthenticator::sign($sign_params);
    printf("<a href=\"http://www.flickr.com/services/auth/?api_key=" . $apiKey . "&perms=" . $perms . "&api_sig=" . $apiSig . "\">Flickr Login</a>");
}
printf("</body>");
printf("</html>");
?>
