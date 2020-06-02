<?php

   
/**
 * Description of Check Unique User Extended Controller
 * 
 * @module Extended Check Unique User
 * 
 * @class Cit_Check_unique_user.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Check_unique_user.php
 * 
 * @author CIT Dev Team
 * 
 * @date 13.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Check_unique_user extends Check_unique_user {
        public function __construct()
{
    parent::__construct();
}
public function checkUniqueUser($input_params=array()){
   
   $return_arr['message']='';
   $return_arr['status']='1';
   
  if(!empty($input_params['email'])){
      $this->db->select('vEmail');
      $this->db->from('users');
      $this->db->where('vEmail',$input_params['email']);
      $email_data=$this->db->get()->result_array();
      if($email_data[0]['vEmail']==$input_params['email']){
         $return_arr['message']="Account with this email already exists.";
         $return_arr['status'] = "0";
         return  $return_arr;
      }
  }
   return  $return_arr;
   
}
}
