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