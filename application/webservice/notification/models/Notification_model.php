<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Notification Model
 *
 * @category webservice
 *
 * @package notifications
 *
 * @subpackage models
 *
 * @module Notification
 *
 * @class Notification_model.php
 *
 * @path application\webservice\notifications\models\Notification_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Notification_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * post_notification method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 05.04.2021
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
     public function post_notification($params_arr = array())
    {
         // echo __LINE__;  print_r( $params_arr["notification_message"]);
        try
        {
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
            if (isset($params_arr["sender_id"]))
            {
                $this->db->set("iSenderId", $params_arr["sender_id"]);
            }
            if (isset($params_arr["missing_pet_id"]))
            {
                $this->db->set("iMissingPetId", $params_arr["missing_pet_id"]);
            }
            if (isset($params_arr["pet_found_street_address"]))
            {
                $this->db->set("vPetFoundStreet", $params_arr["pet_found_street_address"]);
            }
             if (isset($params_arr["pet_found_city"]))
            {
                $this->db->set("vPetFoundCity", $params_arr["pet_found_city"]);
            }
            if (isset($params_arr["pet_found_state"]))
            {
                $this->db->set("vPetFoundState", $params_arr["pet_found_state"]);
            }
            if (isset($params_arr["pet_found_zipcode"]))
            {
                $this->db->set("vPetFoundZipCode", $params_arr["pet_found_zipcode"]);
            }
            if (isset($params_arr["pet_found_date"]))
            {
                $this->db->set("vPetFoundDate", $params_arr["pet_found_date"]);
            }
            if (isset($params_arr["pet_found_latitude"]))
            {
                $this->db->set("vPetFoundLattitude", $params_arr["pet_found_latitude"]);
            }
            if (isset($params_arr["pet_found_longitude"]))
            {
                $this->db->set("vPetFoundLongitude", $params_arr["pet_found_longitude"]);
            }
            if (isset($params_arr["unix_timestamp"]))
            {
                $this->db->set("vUnixTimestamp", $params_arr["unix_timestamp"]);
            }
            $this->db->set("vNotificationType", $params_arr["_enotificationtype"]);
            $this->db->set("eNotifyType", $params_arr["eNotifyType"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $this->db->set("eNotificationStatus", "Active");

            $this->db->insert("notification");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
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
     * get_notification_details method is used to execute database queries for Notification List API.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 05.04.2021
     * @param string $user_id user_id is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    public function get_notification_details($user_id = '', $input_param)
    {
        // print_r($input_param);exit;
        try
        {
            $result_arr = array();

            $this->db->start_cache();
           
            if (isset($input_param['page_code']) && $input_param['page_code'] == "notified_user_list" && isset($input_param['missing_pet_id']))
            {
                $notification_type="Notify pet owner for found pet in my area";

                    $strSql="SELECT m1.iNotificationId AS notification_id, m1.dtAddedAt AS notify_datetime, m1.iMissingPetId AS missing_pet_id, m1.vPetFoundStreet AS pet_found_street,m1.vPetFoundCity AS pet_found_city,m1.vPetFoundState AS pet_found_state, m1.vPetFoundZipCode AS pet_found_zipcode, m1.vPetFoundDate AS pet_found_date, m1.vPetFoundLattitude AS pet_found_lattitude, m1.vPetFoundLongitude AS pet_found_longitude, m1.vUnixTimestamp AS unix_timestamp,m1.vNotificationMessage AS message, m1.eNotifyType AS notify_type,
                    CONCAT(u.vFirstName,\" \",u.vLastName) AS sender_name,
                    u.iUserId AS sender_id, u.vProfileImage AS sender_profile,u.vMobileNo AS sender_phone,u.tAddress AS sender_street_address, u.vStateName AS sender_state, u.vCity AS sender_city, u.vZipCode AS sender_zip_code, u.dLatitude AS sender_lattitude,u.dLongitude AS sender_longitude,u.vEmail AS sender_email,m.vDogsName AS dog_name
, (select mi.vImage from missing_pet_images as mi where mi.iMissingPetId = m.iMissingPetId limit 1) AS dog_image

 FROM notification m1 LEFT JOIN notification m2 ON (m1.`iSenderId` = m2.`iSenderId` AND m1.`iNotificationId` < m2.`iNotificationId`) INNER JOIN users AS u ON m1.iSenderId = u.iUserId
INNER JOIN missing_pets AS m ON m.iMissingPetId = m1.iMissingPetId WHERE m2.iNotificationId IS NULL AND  m1.iMissingPetId='".$input_param['missing_pet_id']."' AND m1.vNotificationType='".$notification_type."' AND m1.iReceiverId='".$input_param['user_id']."' group by m1.iSenderId order by m1.iNotificationId desc ";

                    $result_obj = $this->db->query($strSql);

                   // echo $this->db->last_query();exit;
                    $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
                    $this->db->reset_query();

//                 SELECT m1.*
// FROM notification m1 LEFT JOIN notification m2
//  ON (m1.`iSenderId` = m2.`iSenderId` AND m1.`iNotificationId` < m2.`iNotificationId`) WHERE m2.`iNotificationId` IS NULL AND  m1.`iMissingPetId`=2;


            }
            else
            {
                 $this->db->from("notification AS n");
            $this->db->join("users AS u", "n.iSenderId = u.iUserId", "left"); 
            $this->db->join("missing_pets AS m", "m.iMissingPetId = n.iMissingPetId", "inner");
                $this->db->select("n.iNotificationId AS notification_id");
            $this->db->select("n.dtAddedAt AS notify_datetime");
            $this->db->select("n.iMissingPetId AS missing_pet_id");
            $this->db->select("n.vPetFoundStreet AS pet_found_street");
            $this->db->select("n.vPetFoundCity AS pet_found_city");
            $this->db->select("n.vPetFoundState AS pet_found_state");
            $this->db->select("n.vPetFoundZipCode AS pet_found_state");
            $this->db->select("n.vPetFoundDate AS pet_found_date");
            $this->db->select("n.vPetFoundLattitude AS pet_found_lattitude");
            $this->db->select("n.vPetFoundLongitude AS pet_found_longitude");
            $this->db->select("n.vUnixTimestamp AS unix_timestamp");
            $this->db->select("n.vNotificationMessage AS message");
            $this->db->select("n.eNotifyType AS notify_type");
            $this->db->select("n.vNotificationType AS notification_type");
            $this->db->select("concat(u.vFirstName,' ',u.vLastName) AS sender_name");
            $this->db->select("u.iUserId AS sender_id");
            $this->db->select("u.vProfileImage AS sender_profile");
            $this->db->select("u.vMobileNo AS sender_phone");
            $this->db->select("u.tAddress AS sender_street_address");
            $this->db->select("u.vStateName AS sender_state");
            $this->db->select("u.vCity AS sender_city");
            $this->db->select("u.vZipCode AS sender_zip_code");
            $this->db->select("u.dLatitude AS sender_lattitude");
            $this->db->select("u.dLongitude AS sender_longitude");
             $this->db->select("u.vEmail AS sender_email");
            $this->db->select("m.vDogsName AS dog_name");
             $this->db->select("(select mi.vImage from missing_pet_images as mi where mi.iMissingPetId = m.iMissingPetId limit 1) AS dog_image", FALSE);

             if (isset($user_id) && $user_id != "")
                {
                    $this->db->where("n.iReceiverId =", $user_id);
                }
                 $this->db->stop_cache();
            
            $this->db->order_by("n.iNotificationId", "desc");
            $this->db->order_by("n.dtAddedAt", "desc");
             $result_obj = $this->db->get();
            // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            }
           
echo $this->db->last_query();exit;

           
            $this->db->flush_cache();
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    /**
     * get_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 05.04.2021
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($user_id = '', $missing_pet_id = '')
    {
        try
        {
            $result_arr = array();
             $strSql="SELECT 
           
             m.vDogsName AS dog_name,
             s.iUserId AS s_users_id,
             s.vDeviceToken AS s_device_token,
             s.tAddress AS s_address,
             s.vCity AS s_city,
             s.vZipCode AS s_zipcode,
             s.vProfileImage AS s_profile_image,             
             CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name,
             (SELECT r.iUserId FROM users AS r WHERE r.iUserId  = '".$user_id."')  AS r_user_id,
             (SELECT r.vLastName FROM users AS r WHERE r.iUserId  = '".$user_id."')  AS r_user_last_name,
             (SELECT r.vFirstName FROM users AS r WHERE r.iUserId  = '".$user_id."')  AS r_user_first_name
             FROM users AS s LEFT JOIN missing_pets as m ON s.iUserId=m.iUserId 
              WHERE m.iMissingPetId = '".$missing_pet_id."'";
            $result_obj = $this->db->query($strSql);
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
/**
     * get_tagged_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 05.04.2021
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_tagged_user_details_for_send_notifi($user_id = '', $missing_pet_id = '')
    {
        try
        {
            $result_arr = array();
             $strSql="SELECT 
           
             m.vDogsName AS dog_name,
             s.iUserId AS s_users_id,
             s.vDeviceToken AS s_device_token,
             s.tAddress AS s_address,
             s.vCity AS s_city,
             s.vZipCode AS s_zipcode,
             s.vProfileImage AS s_profile_image,             
             CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name,
             (SELECT r.iUserId FROM users AS r WHERE r.iUserId  = '".$user_id."')  AS r_user_id,
             (SELECT r.vLastName FROM users AS r WHERE r.iUserId  = '".$user_id."')  AS r_user_last_name,
             (SELECT r.vFirstName FROM users AS r WHERE r.iUserId  = '".$user_id."')  AS r_user_first_name
             FROM users AS s 
             LEFT JOIN tag_people as t ON s.iUserId=t.iTagTo 
             LEFT JOIN missing_pets as m ON t.iMissingPetId=m.iMissingPetId 
              WHERE t.iMissingPetId = '".$missing_pet_id."'";
            $result_obj = $this->db->query($strSql);
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

/**
     * get_notifier_details method is used to execute database queries for Notification List API.
     * @created Snehal Shinde | 23.04.2021
     * @param string $user_id user_id is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    public function get_notifier_details($notification_id = '')
    {
        // print_r($input_param);exit;
        try
        {
            $result_arr = array();

            $this->db->start_cache();
            $this->db->from("notification AS n");
            $this->db->select("n.iNotificationId AS notification_id");
            $this->db->select("n.dtAddedAt AS notify_datetime");
            $this->db->select("n.iMissingPetId AS missing_pet_id");
            $this->db->select("n.iSenderId AS notifier_user_id");
            $this->db->select("n.vPetFoundStreet AS pet_found_street");
            $this->db->select("n.vPetFoundCity AS pet_found_city");
            $this->db->select("n.vPetFoundState AS pet_found_state");
            $this->db->select("n.vPetFoundZipCode AS pet_found_zipcode");
            $this->db->select("n.vPetFoundLattitude AS pet_found_lattitude");
            $this->db->select("n.vPetFoundLongitude AS pet_found_longitude");
            $this->db->select("n.vUnixTimestamp AS unix_timestamp");
            $this->db->where("n.iNotificationId =", $notification_id);

            $this->db->stop_cache();
            $result_obj = $this->db->get();
            // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $this->db->flush_cache();
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
       /**
     * update_missing_pet method is used to execute database queries for Edit Missing pet post details.
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_missing_pet($params_arr = array(), $where_arr = array())
    {
        // print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            $this->db->start_cache();
                if (isset($where_arr["missing_pet_id"]) && $where_arr["missing_pet_id"] != "")
                {
                    $this->db->where("iMissingPetId  =", $where_arr["missing_pet_id"]);
                }
                 $this->db->set($this->db->protect("dtUpdatedAt"), Now(), FALSE);
                 $this->db->set("ePetStatus", 'Found');
                $this->db->stop_cache();
               
                if (isset($params_arr["notifier_user_id"]))
                {
                    $this->db->set("vFoundUser", $params_arr["notifier_user_id"]);
                }
                if (isset($params_arr["pet_found_street"])) 
                {
                   $this->db->set("vFoundStreetAddress", $params_arr["pet_found_street"]);
                }
                if (isset($params_arr["pet_found_city"]))
                {
                    $this->db->set("vFoundCity", $params_arr["pet_found_city"]);
                }
                if (isset($params_arr["pet_found_state"]))
                {
                    $this->db->set("vFoundState", $params_arr["pet_found_state"]);
                }
                if (isset($params_arr["pet_found_zipcode"]))
                {
                    $this->db->set("vPetFoundZipCode", $params_arr["pet_found_zipcode"]);
                }
                if (isset($params_arr["pet_found_lattitude"]))
                {
                   $this->db->set("vFoundLattitude", $params_arr["pet_found_lattitude"]);
                }
                if (isset($params_arr["pet_found_longitude"]))
                {
                   $this->db->set("vFoundLongitude", $params_arr["pet_found_longitude"]);
                }
                if (isset($params_arr["notify_datetime"]))
                {
                    $this->db->set("tUniqueTimeStamp", $params_arr["notify_datetime"]);
                }

                $res = $this->db->update("missing_pets");
                // echo $this->db->last_query();exit;
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
        $this->db->stop_cache();
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

}
