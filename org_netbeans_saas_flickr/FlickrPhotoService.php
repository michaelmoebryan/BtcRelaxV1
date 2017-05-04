<?php

include_once "org_netbeans_saas/RestConnection.php";
include_once "FlickrPhotoServiceAuthenticator.php";

class FlickrPhotoService {

    public function FlickrPhotoService() {
        
    }

    /*
      @return an instance of RestResponse */

    public static function testLogin() {
        $method = "flickr.test.login";
        FlickrPhotoServiceAuthenticator::login();
        $apiKey = FlickrPhotoServiceAuthenticator::getApiKey();
        $authToken = FlickrPhotoServiceAuthenticator::getAuthToken();

        $sign_params = array();
        $sign_params["api_key"] = $apiKey;
        $sign_params["method"] = $method;
        $sign_params["auth_token"] = $authToken;
        $apiSig = FlickrPhotoServiceAuthenticator::sign($sign_params);

        $pathParams = array();
        $queryParams = array();
        $queryParams["api_key"] = $apiKey;
        $queryParams["method"] = $method;
        $queryParams["auth_token"] = $authToken;
        $queryParams["api_sig"] = $apiSig;

        $conn = new RestConnection("http://api.flickr.com/services/rest", $pathParams, $queryParams);

        sleep(1);
        return $conn->get();
    }

}

?>
