<?php
/**
 * Description of Block_user Extended Controller
 * 
 * @module Extended Block_user
 * 
 * @class Cit_Block_user.php
 * 
 * @path application\webservice\block_user\controllers\Cit_Block_user.php
 * 
 * @author CIT Dev Team 
 * 
 * @date 18.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Block_user extends Block_user 
{
  public function __construct()
  {
      parent::__construct();
  }
  public function check_block_user_exist($input_params=array())
  {
      $return_arr['message']='';
     	$return_arr['status']='1';
     	 if(false == empty($input_params['user_id']) && false == empty($input_params['block_user_id']))
     	 {
            $this->db->from("blocked_user AS blk");
            $this->db->select("blk.iBlockedId  AS block_id");
            $this->db->where("blk.iBlockedFrom ", $input_params['user_id']);
            $this->db->where("blk.iBlockedTo ", $input_params['block_user_id']);
            $review_data=$this->db->get()->result_array();

            // echo $this->db->last_query();exit;

          if(true == empty($review_data))
          {
             $return_arr['check_block_user_exist']['0']['message']="No block user available";
             $return_arr['check_block_user_exist']['0']['status'] = "0";
             return  $return_arr;
          }
          else
          {
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