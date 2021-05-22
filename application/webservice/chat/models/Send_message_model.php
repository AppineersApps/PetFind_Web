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
    public function check_chat_intiated_or_not($user_id = '', $receiver_id = '', $firebase_id = '', $missing_pet_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("messages AS m"); 

            $this->db->select("m.iMessageId AS m_message_id");
            $this->db->where("m.iMessageId =",$firebase_id);
            $this->db->or_where("(m.iMessageFrom IS NOT NULL AND m.iMessageFrom <> '')", FALSE, FALSE);
            $this->db->where("m.iMessagePetId = ",$missing_pet_id);
            $this->db->where("(m.iMessageFrom = ".$user_id." AND m.iMessageTo = ".$receiver_id." ) OR (m.iMessageFrom = ".$receiver_id." AND m.iMessageTo = ".$user_id.")", FALSE, FALSE);

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
            if (isset($where_arr["m_message_id"]) && $where_arr["m_message_id"] != "")
            {
                $this->db->where("iMessageId =", $where_arr["m_message_id"]);
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
                $this->db->set("iMessagePetId", $params_arr["missing_pet_id"]);
            }
            if (isset($params_arr["message"]))
            {
                $this->db->set("vMessage", $params_arr["message"]);
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
                $this->db->set("iMessagePetId", $params_arr["missing_pet_id"]);
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
            $this->db->join("users AS r", "m.iMessageTo = r.iUserId", "left");

            $this->db->select("s.iUserId AS s_users_id");
            $this->db->select("r.iUserId AS r_users_id");
            $this->db->select("r.vDeviceToken AS r_device_token");
            $this->db->select("CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name");
           // $this->db->select("r.eNotificationType AS r_notification");
            $this->db->where("(m.iMessageFrom = ".$user_id." AND m.iMessageTo = ".$receiver_id.")", FALSE, FALSE);
             $this->db->where("r.eStatus","Active");

            $this->db->limit(1);

            $result_obj = $this->db->get();
            #echo $this->db->last_query();exit;
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
     * post_notification method is used to execute database queries for Send Message API.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function post_notification($params_arr = array())
    {
          //print_r($params_arr);exit;
        try
        {
            if (isset($params_arr['check_notification_exists']['notification_id'])){

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


}
