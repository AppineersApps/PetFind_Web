<?php
            
/**
 * Description of Delete_image Extended Controller
 * 
 * @module Extended Delete_image
 * 
 * @class Cit_Delete_image.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Delete_image.php
 * 
 * @author CIT Dev Team
 * 
 * @date 30.05.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Delete_image extends Delete_image {
        public function __construct()
{
    parent::__construct();
}

    public function checkImageStatus($input_params=array()){
      $return_arr['message']='';
        $return_arr['status']='1';
         if(false == empty($input_params['image_id']))
         {
            $this->db->from("missing_pet_images AS m");
            $this->db->select("m.iImageId AS image_id");
            $this->db->select("m.iMissingPetId AS missing_pet_id");
            $this->db->select("m.vImage AS image");
            $this->db->where_in("iImageId", $input_params['image_id']);
            $image_data=$this->db->get()->result_array();

          if(true == empty($image_data)){ 
             $return_arr['message']="Image is not available";
             $return_arr['status'] = "0";
             return  $return_arr;
          }else{
            $return_arr['image_id']=$image_data;
            $return_arr['status']='1';

          }
      }
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
      return $return_arr;
    
  }

}
