<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of importFromMongo
 *
 * @author Chronos
 */
class importFromMongo {

    //put your code her
    function __construct() {

        include_once "org_netbeans_saas_flickr/FlickrPhotoService.php";
        try {

            $result = FlickrPhotoService::testLogin();
            echo $result->getResponseBody();
        } catch (Exception $e) {
            echo "Exception occured: " . $e;
        }
    }

}
