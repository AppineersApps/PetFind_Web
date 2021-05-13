<?php
/**
 * Description of Tagged_people Extended Controller
 * 
 * @module Extended Tagged_people
 * 
 * @class Cit_Tagged_people.php
 * 
 * @path application\webservice\tagged_people\controllers\Cit_Tagged_people.php
 * 
 * @author CIT Dev Team 
 * 
 * @date 18.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Tagged_people extends Tagged_people 
{
  public function __construct()
  {
      parent::__construct();
  }
  public function checkTaggedPeopleExist($input_params=array())
  {
      $return_arr['message']='';
     	$return_arr['status']='1';
     	 if(false == empty($input_params['missing_pet_id']))
     	 {
            $this->db->from("tag_people AS t");
            $this->db->select("t.iMissingPetId  AS missing_pet_id");
            $this->db->select("t.iTagId  AS tag_id");
            $this->db->where_in("iMissingPetId ", $input_params['missing_pet_id']);
            $this->db->where_in("iTagTo ", $input_params['untag_user_id']);
            $this->db->where_in("iTagFrom ", $input_params['user_id']);
            $review_data=$this->db->get()->result_array();
          if(true == empty($review_data)){
             $return_arr['checkTaggedPeopleExist']['0']['message']="No tag user available";
             $return_arr['checkTaggedPeopleExist']['0']['status'] = "0";
             return  $return_arr;
          }else{
          	$return_arr['missing_pet_id']=$review_data;
          }
      }
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
      return $return_arr;
    
  }

  
}