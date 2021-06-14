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

class Send_notification_model extends CI_Model
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
     * @modified Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function post_notification($params_arr = array())
    {
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
            $result_param = "insert_id1";
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
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01-03-2021
     * @param string $input_params is used for input params.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($input_params)
    {
        try
        {
            $result_arr = array();
            $strSql="SELECT 
            
            distinct(t.iTagTo) AS receiver_id,
             u.vDeviceToken AS u_device_token,             
             u.vProfileImage AS u_profile_image,         
             CONCAT(u.vFirstName,\" \",u.vLastName) AS u_name,
             misp.vDogsName AS dog_name,
             CONCAT(misp.vDogLastSeenStreet,\" \",misp.vLastSeenCity) AS last_seen
             FROM tag_people AS t             
             INNER JOIN missing_pets AS misp ON t.iMissingPetId = misp.iMissingPetId
             INNER JOIN users AS u ON (u.iUserId = t.iTagTo)             
             WHERE t.iTagFrom = '".$input_params['user_id']."'  AND t.iMissingPetId ='".$input_params['missing_pet_id']."'";
            $result_obj = $this->db->query($strSql);

           // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
$result_arr = array_map(function (array $arr) {
                $arr['notification_for'] = 'tagged user';

                return $arr;
            }, $result_arr);


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
        //echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    } 


    /**
     * get_user_details_for_send_notifi_area method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01-03-2021
     * @param string $input_params is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi_area($input_params)
    {
        // print_r($input_params);exit;
        try
        {
            $strWhere ='';     
            // if($input_params['notification_for']=="tagged user" && false == empty($input_params['receiver_id']))
            // {
                $owner_user_id=$input_params['user_id'];
                $strWhere =" u.tAddress = '".$input_params['last_seen_street']."'  AND u.vCity ='".$input_params['last_seen_city']."' AND u.iUserId != '".$owner_user_id."'";
            // }
            // else
            // {
            //     $strWhere =" u.tAddress = '".$input_params['last_seen_street']."'  AND u.vCity ='".$input_params['last_seen_city']."' ";
            // }

            $result_arr = array();
            $strSql="SELECT 
            
            distinct(u.iUserId) AS receiver_id,
             u.vDeviceToken AS u_device_token,             
             u.vProfileImage AS u_profile_image,         
             CONCAT(u.vFirstName,\" \",u.vLastName) AS u_name, misp.vDogsName AS dog_name  
             FROM users AS u         
             LEFT JOIN missing_pets AS misp ON (misp.iUserId = u.iUserId)             
             WHERE $strWhere";
            $result_obj = $this->db->query($strSql);

           // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
$result_arr = array_map(function (array $arr) {
                $arr['notification_for'] = 'near area';

                return $arr;
            }, $result_arr);


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
        //echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
