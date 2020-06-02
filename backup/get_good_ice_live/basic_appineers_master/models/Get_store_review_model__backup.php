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

class Get_store_review_model extends CI_Model
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
    public function get_all_store_reviews_details($store_id = '',$user_id='',$page='')
    {
        try
        {
            $result_arr = array();
            $strWhere ='';
            $page = ($page != '') ? $page : 0;
            $rec_per_page =($page != '') ? 10 : 0;
            $start_from = ($page-1) * $rec_per_page;
            $strPaginationSql ='';

            if (isset($store_id) && $store_id != "")
            {
                $strWhere = "usr.iStoreId='$store_id'";
            }
            if (isset($user_id) && $user_id != "")
            {
                $strWhere = "usr.iUserId='$user_id'";
            }
            if (isset($user_id) && $user_id != "" && isset($store_id) && $store_id != "")
            {
                $strWhere = "usr.iUserId='$user_id' AND usr.iStoreId='$store_id'";
            }
            $strSql="SELECT
                     usr.iUserReviewId AS total_count,
                     usr.iUserReviewId AS usr_review_id,
                     usr.tComment AS usr_comment,
                     usr.iUserId AS usr_user_id,
                     usr.iIcetypeId AS usr_ice_type,
                     usr.iIceQuantityId AS usr_ice_quantity,
                     usr.iStoreId AS usr_store_id,
                     usr.eStatus AS usr_status,
                     usr.dtAddedAt AS usr_added_at,
                     usr.dtUpdatedAt AS usr_updated_at,
                     (SELECT GROUP_CONCAT(vReviewImage) FROM user_review_images uri WHERE uri.iUserReviewId = usr.iUserReviewId ) AS images,
                     concat(users.vCity,', ',(SELECT vState FROM mod_state ms WHERE ms.iStateId = users.iStateId )) AS user_address,
                     users.vProfileImage AS user_profileimage
                     FROM
                     user_store_review AS usr
                     LEFT JOIN users ON users.iUserId = usr.iUserId
                     LEFT JOIN icetype ON icetype.iIcetypeId = usr.iIcetypeId
                     WHERE $strWhere
                     AND usr.eStatus ='Active'
                     AND icetype.eStatus ='Active'
                     ORDER BY usr.iUserReviewId DESC";

            if($page != ''){
              $strPaginationSql =" LIMIT $start_from, $rec_per_page";  
            }

            $results = $this->db->query($strSql);
            $total_count = $results->num_rows();
            $strFinalQuery = $strSql.$strPaginationSql;
            $result_obj = $this->db->query($strFinalQuery);
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            foreach($result_arr AS $data_key => $data_arr){
             $result_arr[$data_key]["total_count"]  = $total_count;
            }

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
        $return_arr["total_count"] = $total_count;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    /**
     * get_image_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_image_details($review_id = '')
    {
        try
        {
            $result_arr = array();
            $strWhere ='';
            if (isset($review_id) && $review_id != "")
            {
                $this->db->where("uri.iUserReviewId =", $review_id);
            }
            $this->db->from("user_review_images AS uri");
            $this->db->select("uri.iUserReviewImageId AS uri_id");
            $this->db->select("uri.vReviewImage AS uri_review_image");
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
