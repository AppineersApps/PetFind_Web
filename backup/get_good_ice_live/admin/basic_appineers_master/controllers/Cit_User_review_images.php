<?php


/**
 * Description of Query Images Extended Controller
 * 
 * @module Extended Query Images
 * 
 * @class Cit_User_Review_images.php
 * 
 * @path application\admin\basic_appineers_master\controllers\Cit_User_Review_images.php
 * 
 * @author CIT Dev Team
 * 
 * @date 24.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_User_review_images extends User_review_images {
        public function __construct()
{
    parent::__construct();
}

public function getReviewImages($render_arr=''){
    $return_status = '2';
    /*if(empty($render_arr['
    	data'])){
        $return_status = '0';
    }*/
    return $return_status;
}
}
