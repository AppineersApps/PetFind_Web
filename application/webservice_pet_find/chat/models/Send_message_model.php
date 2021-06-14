<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send Message Model
 *
 * @category webservice
 *
 * @package chat
 *
 * @subpackage models
 *
 * @module Send Message
 *
 * @class Send_message_model.php
 *
 * @path application\webservice\chat\models\Send_message_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Send_message_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }
     /**
     * check_chat_intiated_or_not method is used to execute database queries for Send Message API.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_chat_intiated_or_not($input_params)
    {
       
        // print_r($input_params);exit;
        try
        {
            $result_arr = array();

            $this->db->from("messages AS m"); 
            $this->db->select("m.iMessageId AS m_message_id");

            if (isset($input_params["message_id"]))
            {
                $this->db->where("m.iMessageId = ",$input_params["message_id"]);
            }  
            if (isset($input_params["missing_pet_id"]))
            {
                $this->db->where("m.iMissingPetId = ",$input_params["missing_pet_id"]);
            }   
            $this->db->where("(m.iMessageFrom = ".$input_params["user_id"]." AND m.iMessageTo = ".$input_params["receiver_id"]." ) OR (m.iMessageFrom = ".$input_params["receiver_id"]." AND m.iMessageTo = ".$input_params["user_id"].")", FALSE, FALSE);
             
            $result_obj = $this->db->get();

           
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * update_message method is used to execute database queries for Send Message API.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_message($params_arr = array(), $where_arr = array())
    {
       // print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            if (isset($where_arr["message_id"]) && $where_arr["message_id"] != "")
            {
                $this->db->where("iMessageId =", $where_arr["message_id"]);
            }     
            if (isset($params_arr["firebase_id"]))
            {
                $this->db->set("vFirebaseId", $params_arr["firebase_id"]);
            }
            if (isset($params_arr["message_status"]))
            {
                $this->db->set("eMessageStatus", $params_arr["message_status"]);
            }
            
            if (isset($params_arr["receiver_id"]))
            {
                $this->db->set("iMessageTo", $params_arr["receiver_id"]);
            }
            
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iMessageFrom", $params_arr["user_id"]);
            }
            
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtmodifieddate"], FALSE);
            $res = $this->db->update("messages");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * add_message method is used to execute database queries for Send Message API.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function add_message($params_arr = array())
    {
    //    print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["firebase_id"]) && $params_arr["firebase_id"] != "")
            {
                $this->db->set("iMessageId", $params_arr["firebase_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iMessageFrom", $params_arr["user_id"]);
            }
            if (isset($params_arr["receiver_id"]))
            {
                $this->db->set("iMessageTo", $params_arr["receiver_id"]);
            }
            if (isset($params_arr["missing_pet_id"]))
            {
                $this->db->set("iMissingPetId", $params_arr["missing_pet_id"]);
            }
            if (isset($params_arr["message"]))
            {
                $this->db->set("vMessage", $params_arr["message"]);
            }
            if (isset($params_arr["eMessageStatus"]))
            {
                $this->db->set("eMessageStatus", $params_arr["eMessageStatus"]);
            }
            
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddeddate"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtmodifieddate"], FALSE);
            
            $this->db->insert("messages");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "m_message_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($user_id = '', $receiver_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("messages AS m");
            $this->db->join("users AS s", "m.iMessageFrom = s.iUserId", "left");
            // $this->db->join("users AS r", "m.iMessageTo = r.iUserId", "left");
            $this->db->join("missing_pets AS mp", "mp.iUserId = m.iMessageFrom", "left");

            $this->db->select("mp.vDogsName AS dog_name");
            // $this->db->select("s.iUserId AS s_users_id");
            // $this->db->select("r.iUserId AS r_users_id");
            // $this->db->select("s.vDeviceToken AS r_device_token");
            $this->db->select("CASE WHEN m.iMessageFrom= '".$receiver_id."' THEN (select vDeviceToken from users WHERE iUserId='".$receiver_id."')
           WHEN  m.iMessageTo= '".$receiver_id."' THEN (select vDeviceToken from users WHERE iUserId='".$receiver_id."')
           END AS  r_device_token
           ");

            $this->db->select("CASE WHEN m.iMessageFrom= '".$user_id."' THEN (select CONCAT(vFirstName,\" \",vLastName) from users WHERE iUserId='".$user_id."')
            WHEN m.iMessageTo= '".$user_id."' THEN (select CONCAT(vFirstName,\" \",vLastName) from users WHERE iUserId='".$user_id."')
            END AS s_name
           ");
           $this->db->select("CASE WHEN m.iMessageFrom= '".$user_id."' THEN (select iUserId from users WHERE iUserId='".$user_id."')
           WHEN m.iMessageTo= '".$user_id."' THEN (select iUserId from users WHERE iUserId='".$user_id."')
           END AS s_users_id
           ");
           $this->db->select("CASE WHEN m.iMessageFrom= '".$receiver_id."' THEN (select iUserId from users WHERE iUserId='".$receiver_id."')
           WHEN  m.iMessageTo= '".$receiver_id."' THEN (select iUserId from users WHERE iUserId='".$receiver_id."')
           END AS  r_users_id
           ");


            // $this->db->select("CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name");
           // $this->db->select("r.eNotificationType AS r_notification");
           $this->db->where("((m.iMessageFrom = ".$user_id." AND m.iMessageTo = ".$receiver_id.") OR (m.iMessageFrom = ".$receiver_id." AND m.iMessageTo = ".$user_id."))", FALSE, FALSE);

            // $this->db->where("(m.iMessageFrom = ".$user_id." AND m.iMessageTo = ".$receiver_id.")", FALSE, FALSE);
             $this->db->where("s.eStatus","Active");

            $this->db->limit(1);

            $result_obj = $this->db->get();
            // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


    public function get_user_id($message_id,$user_id)
    {
        try
        {
            $result_arr = array();

            $this->db->from("messages AS m");
            
            $this->db->select("CASE WHEN iMessageFrom= '".$user_id."' THEN (select `iMessageTo` from messages WHERE iMessageId='".$message_id."')
            WHEN iMessageTo= '".$user_id."' THEN (select `iMessageFrom` from messages WHERE iMessageId='".$message_id."')
           END AS s_users_id
           ");

            // $this->db->select("m.iMessageTo AS s_users_id");
            $this->db->select("m.iMissingPetId AS missing_pet_id");
             $this->db->where("m.iMessageId",$message_id);
            $result_obj = $this->db->get();
            // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            else{
            
                $result_arr['success']=1;   
                
            }
           
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        return $result_arr;
    }
        /**
     * post_notification method is used to execute database queries for Send Message API.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function post_notification($params_arr = array())
    {
        //   print_r($params_arr['_enotificationtype']);exit;
        try
        {
            if (isset($params_arr['check_notification_exists']['notification_id']) && $params_arr['_enotificationtype']!='request_message'
            && $params_arr['_enotificationtype']!='accept_message' && $params_arr['_enotificationtype']!='decline_message' ){

                $result_arr = array();
                $this->db->start_cache();
                if (isset($params_arr['check_notification_exists']['notification_id']) && $params_arr['check_notification_exists']['notification_id'] != "")
                {
                    $this->db->where("iNotificationId =", $params_arr['check_notification_exists']['notification_id']);
                }
                $this->db->where_in("eNotificationStatus", array('Active'));
                $this->db->stop_cache();
                $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
              
                $res = $this->db->update("notification");
                $affected_rows = $this->db->affected_rows();
                if (!$res || $affected_rows == -1)
                {
                    throw new Exception("Failure in updation.");
                }
                $result_param = "affected_rows";
                $result_arr[0][$result_param] = $affected_rows;
                $success = 1;

            }else{

                $result_arr = array();
                if (!is_array($params_arr) || count($params_arr) == 0)
                {
                    throw new Exception("Insert data not found.");
                }
                if (isset($params_arr["notification_message"]))
                {
                    $this->db->set("vNotificationMessage", $params_arr["notification_message"]);
                }
                if (isset($params_arr["receiver_id"]))
                {
                    $this->db->set("iReceiverId", $params_arr["receiver_id"]);
                } 

                $this->db->set("vNotificationType",$params_arr["_enotificationtype"]);
                $this->db->set("eNotifyType", $params_arr["eNotifyType"]);
                $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
                $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
                $this->db->set("eNotificationStatus", "Active");
                if (isset($params_arr["user_id"]))
                {
                    $this->db->set("iSenderId", $params_arr["user_id"]);
                }
                 if (isset($params_arr["missing_pet_id"]))
                {
                    $this->db->set("iMissingPetId", $params_arr["missing_pet_id"]);
                }
                $this->db->insert("notification");
                $insert_id = $this->db->insert_id();
                if (!$insert_id)
                {
                    throw new Exception("Failure in insertion.");
                }
                $result_param = "insert_id1";
                $result_arr[0][$result_param] = $insert_id;
                $success = 1;   
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    /**
     * get_message method is used to execute database queries for Get Message List API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param string $where_clause where_clause is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_message($user_id)
    {
        // print_r($user_id);exit;
       
        try
        {
            $result_arr = array();

            $this->db->from("messages AS m");
            $this->db->join("users AS u", "m.iMessageFrom = u.iUserId", "left");
            $this->db->join("users AS u1", "m.iMessageTo = u1.iUserId", "left");
            $this->db->join("missing_pets AS mp", "mp.iMissingPetId = m.iMissingPetId", "left");

            $this->db->select("m.iMessageFrom AS sender_id");
            $this->db->select("m.iMessageTo AS receiver_id");

            $this->db->select("m.vFirebaseId AS firebase_id");
            $this->db->select("m.iMessageId AS message_id");
            $this->db->select("m.iMissingPetId AS missing_pet_id");
            $this->db->select("m.eMessageStatus AS message_status");
            $this->db->select("m.eIsBlock AS user_block_status");
            $this->db->select("mp.vDogsName AS dog_name");
            $this->db->select("mp.ePetStatus AS pet_status");
            $this->db->select("concat(u.vFirstName,\" \",u.vLastName) AS sender_name");
            $this->db->select("concat(u1.vFirstName,\" \",u1.vLastName) AS receiver_name");
            $this->db->select("m.dtAddedAt AS added_at");
            $this->db->select("m.dtAddedAt AS updated_at");
            $this->db->select("(".$this->db->escape("").") AS sender_image", FALSE);
            $this->db->select("(".$this->db->escape("").") AS receiver_image", FALSE);
            $this->db->where("m.iMessageTo",$user_id);
            $this->db->where("m.eMessageStatus",'pending');

            $this->db->order_by("m.dtAddedAt", "desc");

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

        /**
     * get_send_image method is used to execute database queries for Get Message List API.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 30.07.2019
     * @param string $sender_id sender_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_send_image($sender_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.vProfileImage AS u_image");
            $this->db->select("u.iUserId AS u_users_id");
            if (isset($sender_id) && $sender_id != "")
            {
                $this->db->where("u.iUserId =", $sender_id);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_receiver_images method is used to execute database queries for Get Message List API.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 30.07.2019
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_receiver_images($receiver_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS ui");

            $this->db->select("ui.vProfileImage AS ui_image");
            $this->db->select("ui.iUserId AS ui_users_id");
            if (isset($receiver_id) && $receiver_id != "")
            {
                $this->db->where("ui.iUserId =", $receiver_id);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     /**
     * get_users_list method is used to execute database queries for User Sign Up Email API.
     * @created Kavita sawant | 27.05.2020
     * @modified Kavita sawant | 27.05.2020
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_users_block_details($user_id = '',$connection_id='',$other_user_id='')
    {
         try
        {

            $result_arr = array();
           
            if($connection_id != $user_id){
               $strSql="SELECT 
               			CASE WHEN iBlockedTo= ".$user_id." AND iBlockedFrom=".$connection_id." THEN '1'
               				WHEN iBlockedTo= ".$connection_id." AND iBlockedFrom=".$user_id." THEN '2'
               				ELSE '0' 
               				END AS block_status
       
                        FROM blocked_user LIMIT 1";

                $result_obj =  $this->db->query($strSql);
           

            }else{
                $strSql="SELECT 
               			CASE WHEN iBlockedTo= ".$user_id." AND iBlockedFrom=".$other_user_id." THEN '1'
               				WHEN iBlockedTo= ".$other_user_id." AND iBlockedFrom=".$user_id." THEN '2'
               				ELSE '0' 
               				END AS block_status
       
                        FROM blocked_user LIMIT 1";

                $result_obj =  $this->db->query($strSql);

            }
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
           
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

}
