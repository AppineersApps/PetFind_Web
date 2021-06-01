<?php
            
/**
 * Description of Send Message Extended Controller
 * 
 * @module Extended Send Message
 * 
 * @class Cit_Send_message.php
 * 
 * @path application\webservice\chat\controllers\Cit_Send_message.php
 * 
 * @author CIT Dev Team
 * 
 * @date 14-04-2021
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Send_message extends Send_message {
        public function __construct()
    {
        parent::__construct();
    }
    public function PrepareHelperMessage($input_params=array()){
        
        
        $this->db->select('nt.tNotificationText, nt.vTemplateCode');
        $this->db->from('notification_template as nt');

        if (isset($input_params["message_status"]))
        {
                if ($input_params["message_status"]=="decline")
                {
                    $this->db->where('nt.vTemplateCode','decline_message');
                }
                else{
                    $this->db->where('nt.vTemplateCode','accept_message');
                } 
            
        }
        else{
            $this->db->where('nt.vTemplateCode','request_message');
        }

        
        $notification_text_arr=$this->db->get()->result_array();
        $notification_text=$notification_text_arr[0]['tNotificationText'];

        $notification_type=$notification_text_arr[0]['vTemplateCode'];
        
        // |sender_name| has declined your chat request.

        if ($input_params["message_status"]=="accept"){

            $notification_text = str_replace("|pet_name|",ucfirst($input_params['get_user_details_for_send_notifi'][0]['dog_name']), $notification_text);
            $notification_text = str_replace("|sender_name|",ucfirst($input_params['get_user_details_for_send_notifi'][0]['s_name']), $notification_text);
        }
        else{
            $notification_text = str_replace("|sender_name|",ucfirst($input_params['get_user_details_for_send_notifi'][0]['s_name']), $notification_text);
        }

       
        $return_array['notification_message']=$notification_text;
        $return_array['notification_type']=$notification_type;
       // print_r($return_array);exit;
        return $return_array;
        
    }

    public function checkNotificationExists($input_params = array()){
        $return_arr['message']='';
        $return_arr['status']='1';
      //print_r($input_params); exit;

        $this->db->from("notification AS n");
        $this->db->select("n.iNotificationId AS notification_id");
        $this->db->where_in("iSenderId", $input_params['user_id']);
        $this->db->where_in("vNotificationMessage", $input_params['notification_message']);
        $this->db->where_in("iReceiverId", $input_params['receiver_id']);
        $this->db->where_in("iMissingPetId", $input_params['missing_pet_id']);
        $notification_data=$this->db->get()->result_array();


        if(true == empty($notification_data)){
           $return_arr['checknotificationexists']['0']['message']="No notification available";
           $return_arr['checknotificationexists']['0']['status'] = "0";
           return  $return_arr;
        }else{
            $return_arr['notification_id']=$notification_data;
        }

        foreach ($return_arr as $value) {
          $return_arr = $value;
          $return_arr['status']='1';
        }
        return $return_arr;
        
    }

    public function format_images(&$input_params)
{
    if(!empty($input_params['get_send_image']))
    {
        foreach ($input_params['get_send_image'] as $key => $image)
        {
            if(!empty($image['u_image']))
            {
                $input_params['sender_image'] = $image['u_image'];
            }
        }
        
    }
    
    if(!empty($input_params['get_receiver_images']))
    {
        foreach ($input_params['get_receiver_images'] as $key => $image)
        {
            if(!empty($image['ui_image']))
            {
                $input_params['receiver_image'] = $image['ui_image'];
            }
        }
        
    }
 
}

}
