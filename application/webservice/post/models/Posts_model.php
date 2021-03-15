<?php  

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Posts Model
 * 
 * @category webservice
 *            
 * @package post
 *
 * @subpackage models
 *
 * @module Posts
 * 
 * @class Posts_model.php
 * 
 * @path application\webservice\post\models\Posts_model.php
 * 
 * @version 4.3
 *
 * @author CIT Dev Team
 * 
 * @since 28.03.2020
 */
 
class Posts_model extends CI_Model
{
    public $default_lang = 'EN';
    
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }
    
    /**
     * insert_post method is used to execute database queries for Add Post API.
     * @created priyanka chillakuru | 18.12.2018
     * @modified saikumar anantham | 11.07.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function insert_post($params_arr = array())
    {
        try {
            $result_arr = array();
                        
            if(!is_array($params_arr) || count($params_arr) == 0){
                throw new Exception("Insert data not found.");
            }

            
            if(isset($params_arr["title"])){
                $this->db->set("vTitle", $params_arr["title"]);
            }
            if(isset($params_arr["description"])){
                $this->db->set("vDescription", $params_arr["description"]);
            }
            if(isset($params_arr["zipcode"])){
                $this->db->set("vZipcode", $params_arr["zipcode"]);
            }
            if(isset($params_arr["latitude"])){
                $this->db->set("vLocationLat", $params_arr["latitude"]);
            }
            if(isset($params_arr["longitude"])){
                $this->db->set("vLocationLong", $params_arr["longitude"]);
            }
            if(isset($params_arr["contactinfo"])){
                $this->db->set("vContactInfo", $params_arr["contactinfo"]);
            }
            $this->db->set($this->db->protect("dtAddedDate"), $params_arr["_dtaddeddate"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedDate"), $params_arr["_dtupdateddate"], FALSE);
            if(isset($params_arr["startdate"])){
                $this->db->set("dStartDate", $params_arr["startdate"]);
            }
            if(isset($params_arr["enddate"])){
                $this->db->set("dEndDate", $params_arr["enddate"]);
            }
            if(isset($params_arr["starttime"])){
                $this->db->set("vStartTime", $params_arr["starttime"]);
            }
            if(isset($params_arr["endtime"])){
                $this->db->set("vEndTime", $params_arr["endtime"]);
            }
            if(isset($params_arr["user_id"])){
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if(isset($params_arr["type"])){
                $this->db->set("eCategoryType", $params_arr["type"]);
            }
            if(isset($params_arr["address"])){
                $this->db->set("vLocation", $params_arr["address"]);
            }
            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->insert("posts");
            $insert_id = $this->db->insert_id();
            if(!$insert_id){
                 throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
            
        } catch (Exception $e) {
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
     * get_posted_details method is used to execute database queries for Add Post API.
     * @created priyanka chillakuru | 18.12.2018
     * @modified priyanka chillakuru | 02.01.2019
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    // public function get_posted_details($insert_id = '')
    // {
    //     try {
    //         $result_arr = array();
                                
    //         $this->db->from("posts AS p");
    //         $this->db->join("users AS u", "p.iUserId = u.iUserId", "left");
            
    //         $this->db->select("p.vTitle AS p_title");
    //         $this->db->select("p.vDescription AS p_description");
    //         $this->db->select("p.iPostId AS p_post_id");
    //         $this->db->select("p.vZipcode AS p_zipcode");
    //         $this->db->select("p.vLocation AS p_location");
    //         $this->db->select("p.vLocationLat AS p_location_lat");
    //         $this->db->select("p.vLocationLong AS p_location_long");
    //         $this->db->select("p.vContactInfo AS p_contact_info");
    //         $this->db->select("p.dStartDate AS p_start_date");
    //         $this->db->select("p.dEndDate AS p_end_date");
    //         $this->db->select("p.vStartTime AS p_start_time");
    //         $this->db->select("p.vEndTime AS p_end_time");
    //         $this->db->select("u.vFirstName AS u_name");
    //         $this->db->select("u.iUserId AS u_user_id");
    //         $this->db->select("p.eCategoryType AS p_category_type");
    //         $this->db->select("p.dtAddedDate AS p_added_date");
    //         $this->db->select("(DATE_FORMAT(p.dtAddedDate,\"%b %d %Y\")) AS posted_at", FALSE);
    //         $this->db->select("(TIME_FORMAT(p.vStartTime,\"%h:%i %p\")) AS start_time", FALSE);
    //         $this->db->select("(TIME_FORMAT(p.vEndTime,\"%h:%i %p\")) AS end_time", FALSE);
    //         $this->db->select("(DATE_FORMAT(p.dStartDate,\"%b %d %Y\")) AS start_date", FALSE);
    //         $this->db->select("(DATE_FORMAT(p.dEndDate,\"%b %d %Y\")) AS end_date", FALSE);
    //         if(isset($insert_id) && $insert_id != ""){ 
    //             $this->db->where("p.iPostId =", $insert_id);
    //         }
            
            
            
    //         $this->db->limit(1);
            
    //         $result_obj = $this->db->get();
    //         $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
    //         if(!is_array($result_arr) || count($result_arr) == 0){
    //             throw new Exception('No records found.');
    //         }
    //         $success = 1;
    //     } catch (Exception $e) {
    //         $success = 0;
    //         $message = $e->getMessage();
    //     }
        
    //     $this->db->_reset_all();
    //     //echo $this->db->last_query();
    //     $return_arr["success"] = $success;
    //     $return_arr["message"] = $message;
    //     $return_arr["data"] = $result_arr;
    //     return $return_arr;
    // }
    
    
    /**
     * get_post_listing method is used to execute database queries for Post Listing API.
     * @created priyanka chillakuru | 18.12.2018
     * @modified Chetan Dvs | 13.09.2019
     * @param string $perpage_record perpage_record is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @param string $type type is used to process query block.
     * @param string $zip_code_only zip_code_only is used to process query block.
     * @param string $search_by search_by is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    // public function get_post_listing($perpage_record = '', $user_id = '', $type = '', $zip_code_only = '', $search_by = '', $page_index = 1, &$settings_params = array())
    // {
    //     try {
    //         $result_arr = array();
                        
    //         $this->db->start_cache();
    //         $this->db->from("posts AS p");
            
    //         if($tmp_arr = filterEmptyValues($type)){
    //             $old_arr = $type;
    //             $type = $tmp_arr;
    //             $this->db->where_in("p.eCategoryType", $type);
    //             $type = $old_arr;
    //         }
    //         if(isset($zip_code_only) && $zip_code_only != ""){ 
    //             $this->db->where("p.vZipcode =", $zip_code_only);
    //         }
    //         $this->db->where("p.dEndDate >= (CURDATE())", FALSE, FALSE);
    //         $this->db->where("(p.eStatus IN('Active'))and(p.vTitle like '%".$search_by."%' or p.vZipcode like '%".$search_by."%')", FALSE, FALSE);
            
    //         $this->db->stop_cache();
    //         $total_records = $this->db->count_all_results();
            
    //         $this->db->select("p.iPostId AS p_post_id");
    //         $this->db->select("p.vTitle AS p_title");
    //         $this->db->select("p.vDescription AS p_description");
    //         $this->db->select("p.vZipcode AS p_zipcode");
    //         $this->db->select("p.vLocation AS p_location");
    //         $this->db->select("p.vLocationLat AS p_location_lat");
    //         $this->db->select("p.vLocationLong AS p_location_long");
    //         $this->db->select("p.vContactInfo AS p_contact_info");
    //         $this->db->select("p.dtAddedDate AS p_added_date");
    //         $this->db->select("p.dtUpdatedDate AS p_updated_date");
    //         $this->db->select("p.dStartDate AS p_start_date");
    //         $this->db->select("p.dEndDate AS p_end_date");
    //         $this->db->select("p.vStartTime AS p_start_time");
    //         $this->db->select("p.vEndTime AS p_end_time");
    //         $this->db->select("p.iUserId AS p_user_id");
    //         $this->db->select("p.eCategoryType AS p_category_type");
    //         $this->db->select("(DATE_FORMAT(p.dtAddedDate,\"%b %d %Y\")) AS posted_at", FALSE);
    //         $this->db->select("(TIME_FORMAT(p.vStartTime,\"%h:%i %p\")) AS start_time", FALSE);
    //         $this->db->select("(TIME_FORMAT(p.vEndTime,\"%h:%i %p\")) AS end_time", FALSE);
    //         $this->db->select("(DATE_FORMAT(p.dStartDate,\"%b %d %Y\")) AS start_date", FALSE);
    //         $this->db->select("(DATE_FORMAT(p.dEndDate,\"%b %d %Y\")) AS end_date", FALSE);
    //         $this->db->select("p.vWebsiteLink AS p_website_link_1");
    //         $this->db->select("p.vFacebookLink AS p_facebook_link_1");
    //         $this->db->select("p.vTwitterLink AS p_twitter_link_1");
    //         $this->db->select("p.vInstagramLink AS p_instagram_link_1");
    //         $this->db->select("p.vCouponCode AS p_vcoupon_code");
    //         $this->db->select("(select count(*) from likes where iPostId = p.iPostId AND eStatus = 'like') AS likes_count", FALSE);
    //         $this->db->select("(select count(*) from comments where iPostId = p.iPostId) AS comments_count", FALSE);
    //         $this->db->select("(SELECT if(count(*)>0,1,0) as count FROM `likes` where iPostId = p.iPostId  and iUsersId='".$user_id."' and eStatus in('Like') ) AS is_liked", FALSE);

    //         $settings_params['count'] = $total_records;
            
    //         $record_limit = intval("".$perpage_record."");
    //         $current_page = intval($page_index) > 0 ? intval($page_index) : 1;
    //         $total_pages = getTotalPages($total_records, $record_limit);
    //         $start_index = getStartIndex($total_records, $current_page, $record_limit);
    //         $settings_params['per_page'] = $record_limit;
    //         $settings_params['curr_page'] = $current_page;
    //         $settings_params['prev_page'] = ($current_page > 1) ? 1 : 0;
    //         $settings_params['next_page'] = ($current_page + 1 > $total_pages) ? 0 : 1;
            
    //         $this->db->order_by("p.dtAddedDate", "desc");
    //         $this->db->limit($record_limit, $start_index);
    //         $result_obj = $this->db->get();
    //         $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
    //         $this->db->flush_cache();
            
    //         if(!is_array($result_arr) || count($result_arr) == 0){
    //             throw new Exception('No records found.');
    //         }
    //         $success = 1;
    //     } catch (Exception $e) {
    //         $success = 0;
    //         $message = $e->getMessage();
    //     }
        
    //     $this->db->_reset_all();
    //     //echo $this->db->last_query();
    //     $return_arr["success"] = $success;
    //     $return_arr["message"] = $message;
    //     $return_arr["data"] = $result_arr;
    //     return $return_arr;
    // }
    
    
    /**
     * get_post_listing_v1 method is used to execute database queries for Post Listing API.
     * @created priyanka chillakuru | 11.01.2019
     * @modified priyanka chillakuru | 01.10.2019
     * @param string $perpage_record perpage_record is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @param string $type type is used to process query block.
     * @param string $zip_code_only zip_code_only is used to process query block.
     * @param string $search_by search_by is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    public function get_post_listing_v1($perpage_record = '', $user_id = '', $type = '', $zip_code_only = '', $search_by = '', $page_index = 1, &$settings_params = array())
    {
        try {
            $result_arr = array();
                        
            $this->db->start_cache();
            $this->db->from("posts AS p");
            
            if($tmp_arr = filterEmptyValues($type)){
                $old_arr = $type;
                $type = $tmp_arr;
                $this->db->where_in("p.eCategoryType", $type);
                $type = $old_arr;
            }
            if(isset($zip_code_only) && $zip_code_only != ""){ 
                $this->db->where("p.vZipcode =", $zip_code_only);
            }
            $this->db->where("(p.eStatus IN('Active'))and(p.vTitle like '%".$search_by."%' or p.vZipcode like '%".$search_by."%')", FALSE, FALSE);
            
            $this->db->stop_cache();
            $total_records = $this->db->count_all_results();
            
            $this->db->select("p.iPostId AS p_post_id_1");
            $this->db->select("p.iUserId AS p_user_id_1");
            $this->db->select("p.vTitle AS p_title_1");
            $this->db->select("p.vDescription AS p_description_1");
            $this->db->select("p.vZipcode AS p_zipcode_1");
            $this->db->select("p.vLocation AS p_location_1");
            $this->db->select("p.vLocationLat AS p_location_lat_1");
            $this->db->select("p.vLocationLong AS p_location_long_1");
            $this->db->select("p.vContactInfo AS p_contact_info_1");
            $this->db->select("p.dtAddedDate AS p_added_date_1");
            $this->db->select("p.dtUpdatedDate AS p_updated_date_1");
            $this->db->select("p.dStartDate AS p_start_date_1");
            $this->db->select("p.dEndDate AS p_end_date_1");
            $this->db->select("p.vStartTime AS p_start_time_1");
            $this->db->select("p.vEndTime AS p_end_time_1");
            $this->db->select("p.eCategoryType AS p_category_type_1");
            $this->db->select("(DATE_FORMAT(p.dtAddedDate,\"%b %d %Y\")) AS posted_at_v1", FALSE);
            $this->db->select("(TIME_FORMAT(p.vStartTime,\"%h:%i %p\")) AS start_time_v1", FALSE);
            $this->db->select("(TIME_FORMAT(p.vEndTime,\"%h:%i %p\")) AS end_time_v1", FALSE);
            $this->db->select("(DATE_FORMAT(p.dStartDate,\"%b %d %Y\")) AS start_date_v1", FALSE);
            $this->db->select("(DATE_FORMAT(p.dEndDate,\"%b %d %Y\")) AS end_date_v1", FALSE);
            $this->db->select("p.vWebsiteLink AS p_website_link");
            $this->db->select("p.vFacebookLink AS p_facebook_link");
            $this->db->select("p.vTwitterLink AS p_twitter_link");
            $this->db->select("p.vInstagramLink AS p_instagram_link");
            $this->db->select("p.vCouponCode AS p_vcoupon_code_1");
            $this->db->select("(select count(*) from likes where iPostId = p.iPostId AND eStatus = 'like') AS likes_count_v1", FALSE);
            $this->db->select("(select count(*) from comments where iPostId = p.iPostId) AS comments_count_v1", FALSE);
            $this->db->select("(SELECT if(count(*)>0,1,0) as count FROM `likes` where iPostId = p.iPostId  and iUsersId='".$user_id."' and eStatus in('Like') ) AS is_liked_v1", FALSE);

            $settings_params['count'] = $total_records;
            
            $record_limit = intval("".$perpage_record."");
            $current_page = intval($page_index) > 0 ? intval($page_index) : 1;
            $total_pages = getTotalPages($total_records, $record_limit);
            $start_index = getStartIndex($total_records, $current_page, $record_limit);
            $settings_params['per_page'] = $record_limit;
            $settings_params['curr_page'] = $current_page;
            $settings_params['prev_page'] = ($current_page > 1) ? 1 : 0;
            $settings_params['next_page'] = ($current_page + 1 > $total_pages) ? 0 : 1;
            
            $this->db->order_by("p.dtAddedDate", "desc");
            $this->db->limit($record_limit, $start_index);
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $this->db->flush_cache();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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
     * get_posts_of_zip_code method is used to execute database queries for Global Search API.
     * @created priyanka chillakuru | 02.01.2019
     * @modified priyanka chillakuru | 03.10.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $zip_code zip_code is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_posts_of_zip_code($user_id = '', $zip_code = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("posts AS p");
            
            $this->db->select("p.iPostId AS p_post_id");
            $this->db->select("p.iUserId AS p_user_id");
            $this->db->select("p.vTitle AS p_title");
            $this->db->select("p.vDescription AS p_description");
            $this->db->select("p.vLocation AS p_location");
            $this->db->select("p.vContactInfo AS p_contact_info");
            $this->db->select("p.dtAddedDate AS p_added_date");
            $this->db->select("p.dtUpdatedDate AS p_updated_date");
            $this->db->select("p.dStartDate AS p_start_date");
            $this->db->select("p.dEndDate AS p_end_date");
            $this->db->select("p.vStartTime AS p_start_time");
            $this->db->select("p.vEndTime AS p_end_time");
            $this->db->select("p.eCategoryType AS p_category_type");
            $this->db->select("(DATE_FORMAT(p.dtAddedDate,\"%b %d %Y\")) AS posted_at", FALSE);
            $this->db->select("(TIME_FORMAT(p.vStartTime,\"%h:%i %p\")) AS start_time", FALSE);
            $this->db->select("(TIME_FORMAT(p.vEndTime,\"%h:%i %p\")) AS end_time", FALSE);
            $this->db->select("(DATE_FORMAT(p.dStartDate,\"%b %d %Y\")) AS start_date", FALSE);
            $this->db->select("(DATE_FORMAT(p.dEndDate,\"%b %d %Y\")) AS end_date", FALSE);
            $this->db->select("p.vLocationLat AS p_location_lat");
            $this->db->select("p.vLocationLong AS p_location_long");
            $this->db->select("p.vZipcode AS p_zipcode");
            $this->db->select("p.vWebsiteLink AS p_website_link");
            $this->db->select("p.vFacebookLink AS p_facebook_link");
            $this->db->select("p.vTwitterLink AS p_twitter_link");
            $this->db->select("p.vInstagramLink AS p_instagram_link");
            $this->db->select("(select count(*) from likes where iPostId = p.iPostId AND eStatus = 'like') AS likes_count_v1", FALSE);
            $this->db->select("( select count(*) from comments where iPostId = p.iPostId) AS comments_count_v1", FALSE);
            $this->db->select("(SELECT if(count(*)>0,1,0) as count FROM `likes` where iPostId = p.iPostId  and iUsersId='".$user_id."' and eStatus in('Like') ) AS is_liked_v1", FALSE);
            $this->db->where_in("p.eStatus", array('Active'));
            if(isset($zip_code) && $zip_code != ""){ 
                $this->db->where("p.vZipcode =", $zip_code);
            }
            
            $this->db->order_by("p.dtAddedDate", "desc");
            
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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
     * get_posts_v1 method is used to execute database queries for Send Notification API.
     * @created priyanka chillakuru | 08.01.2019
     * @modified priyanka chillakuru | 06.03.2019
     * @param string $post_id post_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_posts_v1($post_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("posts AS p");
            
            $this->db->select("p.vZipcode AS p_zipcode");
            $this->db->select("p.eCategoryType AS p_category_type");
            $this->db->select("p.iUserId AS p_user_id");
            $this->db->where_in("p.eStatus", array('Active'));
            if(isset($post_id) && $post_id != ""){ 
                $this->db->where("p.iPostId =", $post_id);
            }
            
            
            
            $this->db->limit(1);
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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
     * check_for_that_user_posts method is used to execute database queries for Delete Account API.
     * @created priyanka chillakuru | 22.03.2019
     * @modified priyanka chillakuru | 22.03.2019
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_for_that_user_posts($user_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("posts AS p");
            
            $this->db->select("p.iPostId AS p_post_id");
            if(isset($user_id) && $user_id != ""){ 
                $this->db->where("p.iUserId =", $user_id);
            }
            $this->db->where_in("p.eStatus", array('Pending'));
            
            
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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
     * update_post_status method is used to execute database queries for Delete Account API.
     * @created priyanka chillakuru | 22.03.2019
     * @modified priyanka chillakuru | 22.03.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_post_status($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
                        
            
            
            $this->db->where("".$where_arr["where"]."", FALSE, FALSE);
            
            
            $this->db->set("eStatus", $params_arr["_estatus"]);
            $res = $this->db->update("posts");
            $affected_rows = $this->db->affected_rows();
            if(!$res || $affected_rows == -1){
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows1";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
            
        } catch (Exception $e) {
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
     * get_pending_posts_of_user method is used to execute database queries for Make Archieved User Post As InActive API.
     * @created priyanka chillakuru | 22.03.2019
     * @modified priyanka chillakuru | 22.03.2019
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_pending_posts_of_user($user_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("posts AS p");
            
            $this->db->select("p.iPostId AS p_post_id");
            if(isset($user_id) && $user_id != ""){ 
                $this->db->where("p.iUserId =", $user_id);
            }
            $this->db->where_in("p.eStatus", array('Pending'));
            
            
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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
     * update_posts_as_inactive method is used to execute database queries for Make Archieved User Post As InActive API.
     * @created priyanka chillakuru | 22.03.2019
     * @modified priyanka chillakuru | 22.03.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_posts_as_inactive($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
                        
            
            
            $this->db->where("".$where_arr["where"]."
", FALSE, FALSE);
            
            
            $this->db->set("eStatus", $params_arr["_estatus"]);
            $res = $this->db->update("posts");
            $affected_rows = $this->db->affected_rows();
            if(!$res || $affected_rows == -1){
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
            
        } catch (Exception $e) {
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
     * insert_post_v1 method is used to execute database queries for Add Post_v1 API.
     * @created CIT Dev Team
     * @modified saikumar anantham | 11.07.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function insert_post_v1($params_arr = array())
    {
        try {
            $result_arr = array();
                        
            if(!is_array($params_arr) || count($params_arr) == 0){
                throw new Exception("Insert data not found.");
            }

            
            if(isset($params_arr["title"])){
                $this->db->set("vTitle", $params_arr["title"]);
            }
            if(isset($params_arr["description"])){
                $this->db->set("vDescription", $params_arr["description"]);
            }
            if(isset($params_arr["zipcode"])){
                $this->db->set("vZipcode", $params_arr["zipcode"]);
            }
            if(isset($params_arr["latitude"])){
                $this->db->set("vLocationLat", $params_arr["latitude"]);
            }
            if(isset($params_arr["longitude"])){
                $this->db->set("vLocationLong", $params_arr["longitude"]);
            }
            if(isset($params_arr["contactinfo"])){
                $this->db->set("vContactInfo", $params_arr["contactinfo"]);
            }
            $this->db->set($this->db->protect("dtAddedDate"), $params_arr["_dtaddeddate"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedDate"), $params_arr["_dtupdateddate"], FALSE);
            if(isset($params_arr["startdate"])){
                $this->db->set("dStartDate", $params_arr["startdate"]);
            }
            if(isset($params_arr["enddate"])){
                $this->db->set("dEndDate", $params_arr["enddate"]);
            }
            if(isset($params_arr["starttime"])){
                $this->db->set("vStartTime", $params_arr["starttime"]);
            }
            if(isset($params_arr["endtime"])){
                $this->db->set("vEndTime", $params_arr["endtime"]);
            }
            if(isset($params_arr["user_id"])){
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if(isset($params_arr["type"])){
                $this->db->set("eCategoryType", $params_arr["type"]);
            }
            if(isset($params_arr["address"])){
                $this->db->set("vLocation", $params_arr["address"]);
            }
            if(isset($params_arr["website_link"])){
                $this->db->set("vWebsiteLink", $params_arr["website_link"]);
            }
            if(isset($params_arr["facebook_link"])){
                $this->db->set("vFacebookLink", $params_arr["facebook_link"]);
            }
            if(isset($params_arr["twitter_link"])){
                $this->db->set("vTwitterLink", $params_arr["twitter_link"]);
            }
            if(isset($params_arr["instagram_link"])){
                $this->db->set("vInstagramLink", $params_arr["instagram_link"]);
            }
            if(isset($params_arr["coupon_code"])){
                $this->db->set("vCouponCode", $params_arr["coupon_code"]);
            }
            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->insert("posts");
            $insert_id = $this->db->insert_id();
            if(!$insert_id){
                 throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
            
        } catch (Exception $e) {
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
     * get_posted_details_v1 method is used to execute database queries for Add Post_v1 API.
     * @created CIT Dev Team
     * @modified saikumar anantham | 10.07.2019
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_posted_details_v1($insert_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("missing_pets AS p");
            $this->db->join("users AS u", "p.iMissingPetId = u.iMissingPetId", "left");
            
            $this->db->select("p.vTitle AS p_title");
            $this->db->select("p.vDescription AS p_description");
            $this->db->select("p.iPostId AS p_post_id");
            $this->db->select("p.vZipcode AS p_zipcode");
            $this->db->select("p.vLocation AS p_location");
            $this->db->select("p.vLocationLat AS p_location_lat");
            $this->db->select("p.vLocationLong AS p_location_long");
            $this->db->select("p.vContactInfo AS p_contact_info");
            $this->db->select("p.dStartDate AS p_start_date");
            $this->db->select("p.dEndDate AS p_end_date");
            $this->db->select("p.vStartTime AS p_start_time");
            $this->db->select("p.vEndTime AS p_end_time");
            $this->db->select("u.vFirstName AS u_name");
            $this->db->select("u.iUserId AS u_user_id");
            //$this->db->select("p.eCategoryType AS p_category_type");
            $this->db->select("p.dtAddedDate AS p_added_date");
            // $this->db->select("(DATE_FORMAT(p.dtAddedDate,\"%b %d %Y\")) AS posted_at", FALSE);
            // $this->db->select("(TIME_FORMAT(p.vStartTime,\"%h:%i %p\")) AS start_time", FALSE);
            // $this->db->select("(TIME_FORMAT(p.vEndTime,\"%h:%i %p\")) AS end_time", FALSE);
            // $this->db->select("(DATE_FORMAT(p.dStartDate,\"%b %d %Y\")) AS start_date", FALSE);
            // $this->db->select("(DATE_FORMAT(p.dEndDate,\"%b %d %Y\")) AS end_date", FALSE);
            // $this->db->select("p.vWebsiteLink AS p_website_link");
            // $this->db->select("p.vFacebookLink AS p_facebook_link");
            // $this->db->select("p.vTwitterLink AS p_twitter_link");
            // $this->db->select("p.vInstagramLink AS p_instagram_link");
            // $this->db->select("p.vCouponCode AS p_vcoupon_code");
             if(isset($insert_id) && $insert_id != ""){ 
                $this->db->where("p.iMissingPetId =", $insert_id);
            }
            
            
            
            $this->db->limit(1);
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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
     * get_posted_by_user_details method is used to execute database queries for Comment A post API.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 18.09.2019
     * @param string $post_id post_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_posted_by_user_details($insert_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("missing_pets AS p");
            $this->db->join("comments AS u", "p.iMissingPetId = u.iMissingPetId", "left");
            
            $this->db->select("p.iMissingPetId AS p_post_id");
            //$this->db->select("u.iMissingPetId AS u_user_id");
            $this->db->select("p.vDogsName AS p_dog_name");
            $this->db->select("p.vHairColor AS p_hair_color");
            //$this->db->select("u.iUserId AS u_user_id");
            $this->db->select("p.iUserId AS p_user_id");
            if(isset($missing_pet_id) && $missing_pet_id != ""){ 
                $this->db->where("p.iMissingPetId =", $missing_pet_id);
            }
            
            
            
            $this->db->limit(1);
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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