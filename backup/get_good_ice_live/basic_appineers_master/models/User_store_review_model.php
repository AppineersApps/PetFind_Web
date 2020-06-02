<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User review Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User review
 *
 * @class User_review_model.php
 *
 * @path application\webservice\basic_appineers_master\models\User_review_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class User_store_review_model extends CI_Model
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
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function set_store_review($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            $this->db->set("dtAddedAt", $params_arr["_dtaddedat"]);
            $this->db->set("eStatus", $params_arr["_estatus"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["ice_type_id"]))
            {
                $this->db->set("iIcetypeId", $params_arr["ice_type_id"]);
            }
            if (isset($params_arr["ice_quantity_id"]))
            {
                $this->db->set("iIceQuantityId", $params_arr["ice_quantity_id"]);
            }
            if (isset($params_arr["store_id"]))
            {
                $this->db->set("iStoreId", $params_arr["store_id"]);
            }
            if (isset($params_arr["comment"]))
            {
                $this->db->set("tComment", $params_arr["comment"]);
            }
            
            $this->db->insert("user_store_review");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "review_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        #echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_review_details($review_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_store_review AS usr");
            $this->db->select("usr.iUserReviewId AS usr_review_id");
            $this->db->select("usr.tComment AS usr_comment");
            $this->db->select("usr.iUserId AS usr_user_id");
            $this->db->select("usr.iIcetypeId AS usr_ice_type");
            $this->db->select("usr.iIceQuantityId AS usr_ice_quantity");
            $this->db->select("usr.iStoreId AS usr_store_id");
            $this->db->select("usr.eStatus AS usr_status");
            $this->db->select("usr.dtAddedAt AS usr_added_at");
            $this->db->select("usr.dtUpdatedAt AS usr_updated_at");
            //$this->db->select("user_review_images.vReviewImage AS reviewImage");
            //$this->db->join('user_review_images', 'user_review_images.iUserReviewId = usr.iUserReviewId', 'left');
             $this->db->select("(".$this->db->escape("").") AS images", FALSE);
            if (isset($review_id) && $review_id != "")
            {
                $this->db->where("usr.iUserReviewId =", $review_id);
            }

            //$this->db->limit(1);
           //echo $this->db->last_query();exit;

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
        //echo $this->db->last_review();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    public function update($params_arr = array())
    {
        try
        {
            $result_arr = array();
            
            if (isset($params_arr["review_id"]) && $params_arr["review_id"] != "")
            {
                $this->db->where("iUserReviewId =", $params_arr["review_id"]);
            }
            $this->db->set("iAbusiveReportsForReviewId", $params_arr["iAbusiveReportsForReviewId"]);
            $this->db->set("dtUpdatedAt", $params_arr["dtUpdatedAt"]);
            $res = $this->db->update("user_store_review");
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
        
        return $return_arr;
    }
}
