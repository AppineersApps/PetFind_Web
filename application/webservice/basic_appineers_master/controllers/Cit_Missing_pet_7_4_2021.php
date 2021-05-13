<?php
/**
 * Description of Resend Otp Extended Controller
 * 
 * @module Extended Resend Otp
 * 
 * @class Cit_Resend_otp.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Resend_otp.php
 * 
 * @author CIT Dev Team 
 * 
 * @date 18.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Missing_pet extends Missing_pet {
  public function __construct()
  {
      parent::__construct();
  }
  public function checkMissingPetExist($input_params=array()){
      $return_arr['message']='';
     	$return_arr['status']='1';
     	 if(false == empty($input_params['missing_pet_id']))
     	 {
            $this->db->from("missing_pets AS i");
            $this->db->select("i.iMissingPetId  AS missing_pet_id");
            $this->db->where_in("iMissingPetId ", $input_params['missing_pet_id']);
            $review_data=$this->db->get()->result_array();
          if(true == empty($review_data)){
             $return_arr['checkmissingpetexist']['0']['message']="No Missing Pet available";
             $return_arr['checkmissingpetexist']['0']['status'] = "0";
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

public function PrepareHelperMessage($input_params=array(),$notification_for){
  if($notification_for=='area')
  {
    $this->db->where('nt.vTemplateCode=','NearArea');
  }
  if($notification_for=='tag')
  {
        $this->db->where('nt.vTemplateCode=','NewPostCreate');
  }
    $this->db->from('notification_template as nt');
    $this->db->select('nt.tNotificationText');
    
    
    $notification_text=$this->db->get()->result_array();

    $notification_text=$notification_text[0]['tNotificationText'];

// fetch dog owner details

     $strSql="SELECT 
                   
             CONCAT(u.vFirstName,\" \",u.vLastName) AS dog_owner
             FROM missing_pets AS misp             
             INNER JOIN users AS u ON (u.iUserId = misp.iUserId)             
             WHERE misp.iUserId = '".$input_params['user_id']."'  AND misp.iMissingPetId ='".$input_params['missing_pet_id']."'";
            $result_obj = $this->db->query($strSql);
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $dog_owner= $result_arr[0]['dog_owner'];

            if($notification_for=='area')
            {
              // |dog_name| is lost in near area |missing_area| Help |sender_name| to find the dog.
              $notification_text = str_replace("|sender_name|",ucfirst($dog_owner), $notification_text);
              $notification_text = str_replace("|dog_name|",ucfirst($input_params['dog_name']), $notification_text);
              $notification_text = str_replace("|missing_area|",ucfirst($input_params['last_seen']), $notification_text);
            }
            else
            {
              // |sender_name| has tagged you in Missing Pet post, Please help to find |dog_name|
              $notification_text = str_replace("|sender_name|",ucfirst($dog_owner), $notification_text);
              $notification_text = str_replace("|dog_name|",ucfirst($input_params['dog_name']), $notification_text);
            }
// print_r($notification_text);exit;

    
    $return_array['notification_message']=$notification_text;

    return $return_array;
        
    }
    
public function uploadQueryImages($input_params=array()){
    $result_arr = array();
    $user_id=$input_params['user_id'];
    $img_name="image_";
    $missing_pet_id=$input_params['missing_pet_id'];
    $folder_name="pet_find/missing_pet_image/".$missing_pet_id."/";
  
    $return_arr = array();
    $insert_arr = array();
    $temp_var   = 0;
    $upper_limit = 5;
    
    if($input_params['images_count'] > 0)
    {
      $upper_limit = $input_params['images_count'];
    }

    for($i=1; $i<=$upper_limit; $i++)
    {
      $new_file_name=$img_name.$i;
    
      if($_FILES[$new_file_name]['name']!='')
      {
        $file_name = $_FILES[$new_file_name]['name'];
        $arrExp = explode('.', $file_name);
        $ext = strtolower(end($arrExp));
        $filename = strtolower($arrExp['0']);
        $actual_image_name = $filename. "_" .uniqid() . "." . $ext;
        $temp_file = $_FILES[$new_file_name]['tmp_name'];
        $res = $this->general->uploadAWSData($temp_file, $folder_name, $actual_image_name );
        if($res)
        {
          $insert_arr['vImageId_'.$i.''] = $actual_image_name;
          $temp_var++;
        }
      
      }
    }

   if(is_array($insert_arr) && !empty($insert_arr))
    {
      $this->db->where('iMissingPetId', $missing_pet_id);
      $strFinalResult = $this->db->update("missing_pets",$insert_arr);
     // echo $this->db->last_query();exit;
      $affected_rows = $this->db->affected_rows();
      if (!$strFinalResult || $affected_rows == -1)
      {
          throw new Exception("Failure in updation.");
      }
      $result_param = "affected_rows";
      $result_arr[0][$result_param] = $affected_rows;
      $return["success"]  = true;
    }
    //sleep(10);
    return $return;
}
    public function add_query_format_output (&$input_params = array()) {
    if(!empty($input_params['query_images']))
    {
        $image_array = array();
        foreach ($input_params['query_images'] as $key => $image)
        {
            array_push($image_array,$image['uqi_query_image']);
        }
        
        $input_params['get_query_details'][0]['images'] = $image_array;
    }
    } 
     public function checkSerialNumberExist($input_params=array()){
          
      $return_arr['message']='';
      $return_arr['status']='1';
      // print_r($input_params); exit;
       if(false == empty($input_params['item_serial_number']))
       {
            $this->db->from("item AS i");
            $this->db->select("i.vItemSerialNumber AS item_serial_number");
            $this->db->where_in("vItemSerialNumber", $input_params['item_serial_number']);
            $review_data=$this->db->get()->result_array();
            
          if(true == empty($review_data)){
             $return_arr['checkserialnumberexist']['0']['message']="No items available";
             $return_arr['checkserialnumberexist']['0']['status'] = "0";
             
             return  $return_arr;
          }else{
            $return_arr['item_serial_number']=$review_data;
          }
      }
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
      // print_r($return_arr);exit;
     
      return $return_arr;
    
  }

}
?>
