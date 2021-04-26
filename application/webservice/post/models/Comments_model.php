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
     * @modified Snehal Shinde | 15-03-2021
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
     * @modified Snehal Shinde | 15-03-2021
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
     * @modified Snehal Shinde | 15-03-2021
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
     * @modified Snehal Shinde | 15-03-2021
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_comments($insert_id = '')
    {
        //print_r($comment_id);exit();
        try {
            $result_arr = array();
                                
            $this->db->from("comments AS c");
            $this->db->join("missing_pets AS u", "c.iMissingPetId = u.iMissingPetId", "left");
            $this->db->join("users AS us","c.iCommentFrom = us.iUserId","left");
            $this->db->select("c.iMissingPetId AS missing_pets_id");
            $this->db->select("c.vComment AS comments");
            $this->db->select("c.dtAddedAt AS added_at");
            $this->db->select("c.iCommentId AS comment_id");
            $this->db->select("c.iCommentFrom AS comment_from"); 
            $this->db->select("concat_ws(' ' , us.vFirstName,us.vLastName) AS user_name");
            $this->db->select("us.vProfileImage AS user_profile_image");
            $this->db->select("us.tAddress AS user_address");
            $this->db->select("us.vCity AS user_city");
            $this->db->select("us.vStateName AS user_state");
            $this->db->select("us.dLatitude AS user_lattitude");
            $this->db->select("us.dLongitude AS user_longitude");
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
     * get_all_comments method is used to execute database queries for Comment Listing API.
     * @created Chetan Dvs | 16.09.2019
     * @modified Snehal Shinde | 15-03-2021
     * @param string $perpage_record perpage_record is used to process query block.
     * @param string $post_id post_id is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    public function get_all_comments($comment_id = '', $missing_pet_id)
    {
        try {
            $result_arr = array();
                        
            $this->db->start_cache();
            $this->db->from("comments AS c");
            $this->db->join("users AS use", "c.iCommentFrom = use.iUserId", "left");
            $this->db->join("missing_pets AS u", "c.iMissingPetId = u.iMissingPetId", "left");
            
            if(isset($comment_id) && $comment_id != ""){ 
                $this->db->where("c.iCommentId =", $comment_id);
            }
            if(isset($missing_pet_id) && $missing_pet_id != ""){ 
                $this->db->where("c.iMissingPetId =", $missing_pet_id);
            }
            
            $this->db->stop_cache();
            $total_records = $this->db->count_all_results();
            
            $this->db->select("c.vComment AS c_comment");
            $this->db->select("c.dtAddedAt AS c_added_at");
            $this->db->select("c.iCommentFrom AS comment_user_id");
            $this->db->select("c.iMissingPetId AS c_missing_pets_id");
            $this->db->select("c.iCommentId AS c_comment_id");
            $this->db->select("concat_ws(' ' , use.vFirstName,use.vLastName) AS user_name");
            $this->db->select("use.vProfileImage AS user_profile_image");
            $this->db->select("use.tAddress AS user_address");
            $this->db->select("use.vCity AS user_city");
            $this->db->select("use.vStateName AS user_state");
            $this->db->select("use.dLatitude AS user_lattitude");
            $this->db->select("use.dLongitude AS user_longitude");

            $this->db->order_by("c.dtAddedAt", "desc");
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
     * update_post_status method is used to execute database queries for Delete Account API.
     * @created priyanka chillakuru | 22.03.2019
     * @modified Snehal Shinde | 15-03-2021
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
     * get_posted_by_user_details method is used to execute database queries for Comment A post API.
     * @created Chetan Dvs | 13.09.2019
     * @modified Snehal Shinde | 15-03-2021
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