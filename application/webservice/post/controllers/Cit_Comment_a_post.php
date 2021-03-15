<?php

   
/**
 * Description of Comment A post Extended Controller
 * 
 * @module Extended Comment A post
 * 
 * @class Cit_Comment_a_post.php
 * 
 * @path application\webservice\post\controllers\Cit_Comment_a_post.php
 * 
 * @author CIT Dev Team
 * 
 * @date 17.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Comment_a_post extends Comment_a_post {
        public function __construct()
{
    parent::__construct();
}
public function prepareNotificationMessage($input='')
{
    #pr($input,1);die;
    //"<<user>> commented on your post."
    $return_array =  array();
    $commented_by =  $input['get_commented_user_details'][0]['commented_by_user'];
    $message      =  $commented_by." commented on your post.";
    $return_array[0]['message'] = $message;
    return $return_array;
}
}
