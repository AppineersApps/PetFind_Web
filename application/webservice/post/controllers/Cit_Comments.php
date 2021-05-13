<?php

   
/**
 * Description of Comment A post Extended Controller
 * 
 * @module Extended Comment A post
 * 
 * @class Cit_Comments.php
 * 
 * @path application\webservice\post\controllers\Cit_Comments.php
 * 
 * @author CIT Dev Team
 * 
 * @date 17.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Comments extends Comments {
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

public function formatOrderOfData(&$input='')
{
    $comments                   =  array();
    $comments                   =  array_reverse($input['get_all_comments']);
    $input['get_all_comments']  =  $comments;
}

}
