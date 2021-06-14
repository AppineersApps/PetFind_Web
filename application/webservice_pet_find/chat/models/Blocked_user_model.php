<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Blocked User Model
 *
 * @category webservice
 *
 * @package friends
 *
 * @subpackage models
 *
 * @module Blocked User
 *
 * @class Blocked_user_model.php
 *
 * @path application\webservice\friends\models\Blocked_user_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 01.08.2019
 */

class Blocked_user_model extends CI_Model
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
     * if_blocked method is used to execute database queries for Send Message API.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 04.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    /**
     * if_blocked method is used to execute database queries for Send Message API.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 04.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function if_blocked($user_id = '', $receiver_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedTo AS bu_blocked_to");
            $this->db->select("bu.iBlockedFrom AS bu_blocked_by");
            $this->db->where("(bu.dtAddedAt IS NOT NULL AND bu.dtAddedAt <> '')", FALSE, FALSE);
            $this->db->where("(bu.iBlockedTo = ".$user_id." AND bu.iBlockedFrom = ".$receiver_id.") OR (bu.iBlockedTo = ".$receiver_id." AND bu.iBlockedFrom = ".$user_id.")", FALSE, FALSE);

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
     * block_user method is used to execute database queries for Block User API.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function block_user($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["block_id"]))
            {
                $this->db->set("iBlockedTo", $params_arr["block_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iBlockedBy", $params_arr["user_id"]);
            }
            $this->db->set($this->db->protect("dAddedDate"), $params_arr["_daddeddate"], FALSE);
            $this->db->insert("blocked_user");
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
     * check_is_blocked method is used to execute database queries for Block User API.
     * @created Devangi Nirmal | 21.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param string $block_id block_id is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_is_blocked($block_id = '', $user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedUserId AS bu_blocked_user_id");
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("bu.iBlockedTo =", $block_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iBlockedBy =", $user_id);
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



}
