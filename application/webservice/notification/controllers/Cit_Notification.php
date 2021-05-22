<?php
            
/**
 * Description of Send Message Extended Controller
 * 
 * @module Extended Send Message
 * 
 * @class Cit_Send_message.php
 * 
 * @path application\webservice\friends\controllers\Cit_Send_message.php
 * 
 * @author CIT Dev Team
 * 
 * @date 30.05.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Notification extends Notification {
        public function __construct()
{
    parent::__construct();
}
public function PrepareHelperMessage($input_params=array()){
    
    $this->db->select('nt.tNotificationText');
    $this->db->from('notification_template as nt');

    if (isset($input_params["page_code"]) && 'found_my_pet'==$input_params["page_code"])
    {
          $this->db->where('nt.vTemplateCode=','Found');
    }
    if (isset($input_params["page_code"]) && 'notify_as_found_in_my_area'==$input_params["page_code"])
    {
    $this->db->where('nt.vTemplateCode=','FoundInMyArea');
    }
    $notification_text=$this->db->get()->result_array();

    $notification_text=$notification_text[0]['tNotificationText'];

    // fetch dog owner details

     $strSql="SELECT 
                   
             u.iUserId as dog_owner_id,CONCAT(u.vFirstName,\" \",u.vLastName) AS dog_owner
             FROM missing_pets AS misp             
             LEFT JOIN users AS u ON (u.iUserId = misp.iUserId)             
             WHERE misp.iMissingPetId ='".$input_params['missing_pet_id']."'";
            $result_obj = $this->db->query($strSql);
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $dog_owner= $result_arr[0]['dog_owner']; 
            $dog_owner_id= $result_arr[0]['dog_owner_id']; 

    if (isset($input_params["page_code"]) && 'found_my_pet'==$input_params["page_code"])
    {
       // |sender_name| has found |dog_name|
              $notification_text = str_replace("|dog_owner_name|",ucfirst($dog_owner), $notification_text);
              $notification_text = str_replace("|dog_name|",ucfirst($input_params['dog_name']), $notification_text);
    }
    if (isset($input_params["page_code"]) && 'notify_as_found_in_my_area'==$input_params["page_code"])
    {
      // |notify_user_name| is found |dog_owner_name|`s |dog_name| in |area_name|

       $notify_user_name=$input_params['get_user_details_for_send_notifi'][0]['r_user_first_name']." ".$input_params['get_user_details_for_send_notifi'][0]['r_user_last_name'];
       
        $notification_text = str_replace("|notify_user_name|",ucfirst($notify_user_name), $notification_text);

        $notification_text = str_replace("|dog_owner_name|",ucfirst($dog_owner), $notification_text);

        $notification_text = str_replace("|dog_name|",ucfirst($input_params['get_user_details_for_send_notifi'][0]['dog_name']), $notification_text);
       
         $notification_text = str_replace("|area_name|",ucfirst($input_params['pet_found_street_address']." ".$input_params['pet_found_city']), $notification_text);
    }

    $return_array['notification_message']=$notification_text;
    $return_array['dog_owner']=$dog_owner;
    $return_array['dog_owner_id']=$dog_owner_id;

      
        return $return_array;
        
    }

    public function checkPostStatus($input_params=array()){
      $return_arr['message']='';
        $return_arr['status']='1';
         if(false == empty($input_params['missing_pet_id']))
         {
            $this->db->from("missing_pets AS m");
            $this->db->select("m.iMissingPetId AS missing_pet_id");
            $this->db->where_in("iMissingPetId", $input_params['missing_pet_id']);
            $this->db->where("ePetStatus",'missing');
            $review_data=$this->db->get()->result_array();

          if(true == empty($review_data)){
             // $return_arr['checkpoststatus']['0']['message']="No missing pet posts available";
             // $return_arr['checkpoststatus']['0']['status'] = "0"; 
             $return_arr['message']="No missing pet posts available";
             $return_arr['status'] = "0";
             return  $return_arr;
          }else{
            $return_arr['missing_pet_id']=$review_data;
            $return_arr['status']='1';

          }
      }
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
      return $return_arr;
    
  }

  public function checkValidPost($input_params=array()){
    $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['missing_pet_id']))
       {
          $this->db->from("missing_pets AS m");
          $this->db->select("m.iMissingPetId AS missing_pet_id");
          $this->db->where_in("iMissingPetId", $input_params['missing_pet_id']);
          $this->db->where_in("iUserId", $input_params['user_id']);
          $this->db->where("ePetStatus",'missing');
          $review_data=$this->db->get()->result_array();
          
       
        if(true == empty($review_data)){
           $return_arr['message']="No missing pet posts available for this user.";
           $return_arr['status'] = "0";
           return  $return_arr;
        }else{
          $return_arr['missing_pet_id']=$review_data;
          $return_arr['status']='1';

        }
    }
    foreach ($return_arr as $value) {
      $return_arr = $value;
      $return_arr['status']='1';
    }
    return $return_arr;
  
}

  public function checkBlockStatus($input_params=array()){
      $return_arr['message']='';
        $return_arr['status']='1';
         if(false == empty($input_params['missing_pet_id']))
         {
              // print_r($input_params);exit;

            $this->db->from("users AS us");
            $this->db->join("missing_pets AS m","us.iUserId = m.iUserId", "left");
            $this->db->join("blocked_user AS bu","m.iUserId = bu.iBlockedFrom", "left");
            $this->db->select("m.iMissingPetId AS missing_pet_id");
            $this->db->where_in("m.iMissingPetId", $input_params['missing_pet_id']);
            $this->db->where("bu.iBlockedTo",$input_params['user_id']);
            $this->db->where("bu.iBlockedFrom=m.iUserId");
            $this->db->where("m.ePetStatus",'missing');
            $blocked_data=$this->db->get()->result_array();

            // echo $this->db->last_query();exit;

          if(false == empty($blocked_data)){
             $return_arr['checkBlockStatus']['0']['message']="Pet Owner blocked you so u cant notify.";
             $return_arr['checkBlockStatus']['0']['status'] = "0";
             return  $return_arr;
          }
          else
          {
            
            $return_arr['missing_pet_id']=$input_params['missing_pet_id'];
          }
          
      }
        $return_arr['status']='1';
      return $return_arr;
    
  }
}
