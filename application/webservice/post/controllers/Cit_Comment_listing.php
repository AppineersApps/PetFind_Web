<?php

   
/**
 * Description of Comment Listing Extended Controller
 * 
 * @module Extended Comment Listing
 * 
 * @class Cit_Comment_listing.php
 * 
 * @path application\webservice\post\controllers\Cit_Comment_listing.php
 * 
 * @author CIT Dev Team
 * 
 * @date 19.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Comment_listing extends Comment_listing {
        public function __construct()
{
    parent::__construct();
}
public function formatOrderOfData(&$input='')
{
    $comments                   =  array();
    $comments                   =  array_reverse($input['get_all_comments']);
    $input['get_all_comments']  =  $comments;
}
}
