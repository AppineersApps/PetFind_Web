<?php
/**
 * Description of Missing Pet Extended Controller
 * 
 * @module Extended Missing pet
 * 
 * @class Cit_Missing_pet.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Missing_pet.php
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
  if($notification_for=='tag' && isset($input_params["missing_pet_found_user_id"]))
  {
        $this->db->where('nt.vTemplateCode=','Found');
  }
   if($notification_for=='tag') 
  {
     $this->db->where('nt.vTemplateCode=','NewPostCreate');
  }
  // else
  // {
  //    $this->db->where('nt.vTemplateCode=','NewPostCreate');
  // }
    $this->db->from('notification_template as nt');
    $this->db->select('nt.tNotificationText');
    
    
    $notification_text=$this->db->get()->result_array();

    $notification_text=$notification_text[0]['tNotificationText'];

// if pet status is found then get user name who help to find pet(when found_user_id is there)
 if(isset($input_params["missing_pet_found_user_id"]))
            {
             
                $strSql1="SELECT 
                     
               CONCAT(u.vFirstName,\" \",u.vLastName) AS notify_by_user_name
               FROM users AS u WHERE u.iUserId = '".$input_params["missing_pet_found_user_id"]."'";
              $result_obj1 = $this->db->query($strSql1);
              $result_arr1 = is_object($result_obj1) ? $result_obj1->result_array() : array();
              $notify_by_user_name= $result_arr1[0]['notify_by_user_name'];
               // print_r($notify_by_user_name);exit;
            }


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
            else if($notification_for=='tag' && isset($input_params["missing_pet_found_user_id"]))
            {
              // |sender_name| has found their |dog_name|
              $notification_text = str_replace("|dog_owner_name|",ucfirst($dog_owner), $notification_text);
              $notification_text = str_replace("|dog_name|",ucfirst($input_params['dog_name']), $notification_text);
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
    $user_id=$input_params['user_id'];
    $img_name="image_";
    $missing_pet_id=$input_params['missing_pet_id'];
    $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
    $folder_name=$aws_folder_name."/missing_pet_image/".$missing_pet_id."/";
  
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
        
        $temp_file    = $_FILES[$new_file_name]['tmp_name'];
      $image_name   = $_FILES[$new_file_name]['name'];
      list($file_name, $extension)  = $this->general->get_file_attributes($image_name);
          $res = $this->general->uploadAWSData($temp_file, $folder_name, $file_name );
  
      if($res)
      {
          $insert_arr[$temp_var]['iMissingPetId']=$missing_pet_id;
          $insert_arr[$temp_var]['iUserId']=$user_id;
          $insert_arr[$temp_var]['vImage']=$file_name;
          $insert_arr[$temp_var]['dtAddedAt']=date('Y-m-d H:i:s');
          $temp_var++;
      }
    
    }

  }

   if(is_array($insert_arr) && !empty($insert_arr))
  {
    $this->db->insert_batch("missing_pet_images",$insert_arr);
  }
  
 $affected_rows = $this->db->affected_rows();
  if ($affected_rows == -1)
    {
        throw new Exception("Failure in Addition.");
    }
  $result_param = "affected_rows";
  $result_arr[0][$result_param] = $affected_rows;
  $return["success"]  = true; 
  return $return;
    
}


}
?>
