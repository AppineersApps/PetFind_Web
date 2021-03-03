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
     * @modified ---
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
            if (isset($params_arr["o_service_id"]))
            {
                $this->db->set("iServiceId", $params_arr["o_service_id"]);
            }
            if (isset($params_arr["o_offer_id"]))
            {
                $this->db->set("iOfferId", $params_arr["o_offer_id"]);
            }
            if (isset($params_arr["user_type"]))
            {
                $this->db->set("vReceiverUserType", $params_arr["user_type"]);
            }
            $this->db->set("vNotificationType", $params_arr["_enotificationtype"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $this->db->set("eNotificationStatus", "Active");

            /*if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }*/
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
     * get_service_area method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_service_area_details($service_id)
    {
        try
        {
            $result_arr = array();
             $strSql="SELECT 
            iServiceAreaId AS service_id, dStartDate As service_date,dStartTime As service_time,iCategoryId As service_category,iServiceAreaId As service_area FROM service WHERE iServiceId='".$service_id."'";
            $result_obj = $this->db->query($strSql);
            //echo $this->db->last_query();exit;
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
     * get_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($input_params)
    {
        // print_r($input_params);exit;
        try
        {
            $result_arr = array();
             $strSql="SELECT 
            
            distinct(t.iTagTo) AS receiver_id,
             u.vDeviceToken AS u_device_token,             
             u.vProfileImage AS u_profile_image,         
             CONCAT(u.vFirstName,\" \",u.vLastName) AS u_name
             FROM tag_people AS t             
             INNER JOIN missing_pets AS misp ON t.iMissingPetId = misp.iMissingPetId
             INNER JOIN users AS u ON (u.iUserId = t.iTagFrom)             
             WHERE t.iTagFrom = '".$input_params['user_id']."'  AND t.iMissingPetId ='".$input_params['missing_pet_id']."'";
            $result_obj = $this->db->query($strSql);

           //echo $this->db->last_query();exit;
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
        //echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
