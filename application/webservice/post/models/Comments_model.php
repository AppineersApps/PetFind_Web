<?php  

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Comments Model
 * 
 * @category webservice
 *            
 * @package post
 *
 * @subpackage models
 *
 * @module Comments
 * 
 * @class Comments_model.php
 * 
 * @path application\webservice\post\models\Comments_model.php
 * 
 * @version 4.3
 *
 * @author CIT Dev Team
 * 
 * @since 28.03.2020
 */
 
class Comments_model extends CI_Model
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
     * get_post_comments method is used to execute database queries for Post Listing API.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param string $p_post_id p_post_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_post_comments($comment_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("comments AS c");
            $this->db->join("missing_pets AS u", "c.iMissingPetId = u.iMissingPetId", "left");
            
            $this->db->select("c.iCommentFrom AS c_comment_form");
            $this->db->select("c.vComment AS c_comment");
            $this->db->select("c.dtAddedAt AS c_added_at");
            //$this->db->select("concat_ws(u.vFirstName,vLastName) AS u_first_name");
            //$this->db->select("u.vEmail AS u_email");
            if(isset($comment_id) && $comment_id != ""){ 
                $this->db->where("c.iCommentId =", $comment_id);
            }
            
            
            
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
     * fetch_post_comments method is used to execute database queries for Post Listing API.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param string $p_post_id_1 p_post_id_1 is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function fetch_post_comments($comment_id_1 = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("comments AS c");
            //$this->db->join("users AS u", "c.iUsersId = u.iUserId", "left");
            $this->db->join("missing_pets AS u", "c.iMissingPetId = u.iMissingPetId", "left");
            $this->db->select("c.vComment AS c_comment_1");
            $this->db->select("c.dtAddedAt AS c_added_at_1");
            // $this->db->select("concat_ws(' ' , u.vFirstName,u.vLastName) AS u_first_name_1");
            // $this->db->select("u.vEmail AS u_email_1");
            if(isset($comment_id_1) && $comment_id_1 != ""){ 
                $this->db->where("c.iCommentId =", $comment_id_1);
            }
            
            
            
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
     * insert_comment method is used to execute database queries for Comment A post API.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function insert_comment($params_arr = array())
    {
        try {
            $result_arr = array();
                        
            if(!is_array($params_arr) || count($params_arr) == 0){
                throw new Exception("Insert data not found.");
            }

            
            if(isset($params_arr["missing_pets_id"])){
                $this->db->set("iMissingPetId", $params_arr["missing_pets_id"]);
            }
            // if(isset($params_arr["comment_id"])){
            //     $this->db->set("iCommentId", $params_arr["comment_id"]);
            // }
            if(isset($params_arr["comments_from"])){
                $this->db->set("iCommentFrom", $params_arr["comments_from"]);
            }
            if(isset($params_arr["comments"])){
                $this->db->set("vComment", $params_arr["comments"]);
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
           // $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->insert("comments");
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
     * get_comments method is used to execute database queries for Comment A post API.
     * @created Chetan Dvs | 17.09.2019
     * @modified Chetan Dvs | 20.09.2019
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_comments($insert_id = '')
    {
        //print_r($comment_id);exit();
        try {
            $result_arr = array();
                                
            $this->db->from("comments AS c");
            //$this->db->join("posts AS p", "c.iPostId = p.iPostId", "left");
            $this->db->join("missing_pets AS u", "c.iMissingPetId = u.iMissingPetId", "left");
            $this->db->join("users AS us","u.iUserId = us.iUserId","left");
            $this->db->select("c.iMissingPetId AS c_missing_pets_id");
            $this->db->select("c.vComment AS c_comment");
            $this->db->select("c.dtAddedAt AS c_added_at");
            $this->db->select("c.iCommentId AS c_comment_id");
            $this->db->select("c.iCommentFrom AS c_comment_from"); 
            //$this->db->select("p.iPostId AS p_post_id_1");
            //$this->db->select("u.iMissingPetId AS u_missing_pets_id_2");
            // $this->db->select("concat_ws(' ' , u.vFirstName , u.vLastName ) AS u_first_name_1");
            // $this->db->select("u.vProfileImage AS u_profile_image");
            if(isset($insert_id) && $insert_id != ""){ 
                $this->db->where("c.iCommentId =", $insert_id);
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
        //echo $this->db->last_query();exit();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    
    
    /**
     * get_comments_v1 method is used to execute database queries for Comment A post API.
     * @created CIT Dev Team
     * @modified Chetan Dvs | 20.09.2019
     * @param string $insert_id1 insert_id1 is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    // public function get_comments_v1($insert_id1 = '')
    // {
    //     try {
    //         $result_arr = array();
                                
    //         $this->db->from("comments AS c");
    //         $this->db->join("posts AS p", "c.iPostId = p.iPostId", "left");
    //         $this->db->join("users AS u", "c.iUsersId = u.iUserId", "left");
            
    //         $this->db->select("c.iUsersId AS c_users_id_v1");
    //         $this->db->select("c.tComment AS c_comment_v1");
    //         $this->db->select("c.dtAddedAt AS c_added_at_v1");
    //         $this->db->select("p.iPostId AS p_post_id_1_v1");
    //         $this->db->select("u.iUserId AS u_user_id_2_v1");
    //         $this->db->select("concat_ws(' ' , u.vFirstName , u.vLastName ) AS u_first_name_1_v1");
    //         $this->db->select("u.vProfileImage AS u_profile_image_v1");
    //         if(isset($insert_id1) && $insert_id1 != ""){ 
    //             $this->db->where("c.iCommentsId =", $insert_id1);
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
     * get_all_comments method is used to execute database queries for Comment Listing API.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 19.09.2019
     * @param string $perpage_record perpage_record is used to process query block.
     * @param string $post_id post_id is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    public function get_all_comments($perpage_record = '', $comment_id = '', $page_index = 1, &$settings_params = array())
    {
        try {
            $result_arr = array();
                        
            $this->db->start_cache();
            $this->db->from("comments AS c");
            //$this->db->join("posts AS p", "c.iPostId = p.iPostId", "left");
            $this->db->join("missing_pets AS u", "c.iMissingPetId = u.iMissingPetId", "left");
            
            if(isset($comment_id) && $comment_id != ""){ 
                $this->db->where("c.iCommentId =", $comment_id);
            }
            
            $this->db->stop_cache();
            $total_records = $this->db->count_all_results();
            
            $this->db->select("c.vComment AS c_comment");
            $this->db->select("c.dtAddedAt AS c_added_at");
            //$this->db->select("p.iPostId AS p_post_id");
            //$this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("c.iMissingPetId AS c_missing_pets_id");
            $this->db->select("c.iCommentId AS c_comment_id");

            $settings_params['count'] = $total_records;
            
            $record_limit = intval("".$perpage_record."");
            $current_page = intval($page_index) > 0 ? intval($page_index) : 1;
            $total_pages = getTotalPages($total_records, $record_limit);
            $start_index = getStartIndex($total_records, $current_page, $record_limit);
            $settings_params['per_page'] = $record_limit;
            $settings_params['curr_page'] = $current_page;
            $settings_params['prev_page'] = ($current_page > 1) ? 1 : 0;
            $settings_params['next_page'] = ($current_page + 1 > $total_pages) ? 0 : 1;
            
            $this->db->order_by("c.dtAddedAt", "desc");
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
    
    
}