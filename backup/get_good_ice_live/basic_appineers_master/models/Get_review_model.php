<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Post a Feedback Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Post a Feedback
 *
 * @class Post_a_feedback_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Post_a_feedback_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Get_review_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }
     /**
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_all_store_reviews_details($store_id = '',$user_id='')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_store_review AS usr");

            $this->db->select("usr.tComment AS usr_comment");
            $this->db->select("usr.iUserId AS usr_user_id");
            $this->db->select("usr.iIcetypeId AS usr_ice_type");
            $this->db->select("usr.iIceQuantityId AS usr_ice_quantity");
            $this->db->select("usr.iStoreId AS usr_store_id");
            $this->db->select("usr.eStatus AS usr_status");
            $this->db->select("usr.dtAddedAt AS usr_added_at");
            $this->db->select("usr.dtUpdatedAt AS usr_updated_at");
            $this->db->select("user_review_images.vReviewImage AS images");
            $this->db->select("users.vUserName AS user_name");
            $this->db->select("users.tAddress AS user_address");
            $this->db->select("users.vProfileImage AS user_profileimage");
            $this->db->join('user_review_images', 'user_review_images.iUserReviewId = usr.iUserReviewId', 'left');
            $this->db->join('users', 'users.iUserId = usr.iUserReviewId', 'left');
            if (isset($store_id) && $store_id != "")
            {
                $this->db->where("usr.iStoreId =", $store_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("usr.iUserId =", $user_id);
            }

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
}
