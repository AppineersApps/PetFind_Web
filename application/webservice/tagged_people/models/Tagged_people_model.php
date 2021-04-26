<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Tagged_people Model
 *
 * @category webservice
 *
 * @package tagged_people
 *
 * @subpackage models
 *
 * @module Tagged people
 *
 * @class Tagged_people_model.php
 *
 * @path application\webservice\Tagged_people\models\Tagged_people_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Tagged_people_model extends CI_Model
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
     * get_tagged_people method is used to execute database queries for getting tagged people list.
     * @created Snehal Shinde | 31-03-2021
     * @param string $arrResult  is used for input array values.
     * @return array $return_arr returns response of input values.
*/
    public function get_tagged_people($arrResult)
    {
        // print_r($arrResult);exit;
        try
        {
            $result_arr = array();
             $this->db->start_cache();

            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';    
            
            $this->db->from("tag_people AS t");
            $this->db->join("users as u","t.iTagTo = u.iUserId", "left");
             $strWhere = "t.iSysRecDeleted ='0'";

            if(false == empty($arrResult['missing_pet_id']))
            {
                $strWhere.= " AND t.iMissingPetId = '".$arrResult['missing_pet_id']."'";
                
            }
             $this->db->where($strWhere);
            $this->db->stop_cache();

            $this->db->select("t.iMissingPetId AS missing_pet_id"); 
            $this->db->select("t.iTagTo AS user_id"); 
            $this->db->select("t.iTagId AS tag_id"); 
            $this->db->select("u.vLastName AS user_last_name");
            $this->db->select("u.vFirstName AS user_first_name");
            $this->db->select("u.vAptSuite AS user_apt_suit");
            $this->db->select("u.tAddress AS user_address");
            $this->db->select("u.vCity AS user_city");
            $this->db->select("u.vStateName AS user_state");
            $this->db->select("u.vZipCode AS user_zip_code");
            $this->db->select("u.vProfileImage AS user_profile_image");
            $this->db->select("u.dLatitude AS user_lattitude");
            $this->db->select("u.dLongitude AS user_longitude");
           
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

// print_r($result_arr);exit;
            // echo $this->db->last_query();exit;
            
//  here we recreate array for fetching user profile image from aws and add it to final array
            $result_arr = array_map(function (array $arr) { 

                $data_1 = $arr["user_profile_image"];
                $image_arr = array();
                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $image_arr["image_name"] = $data_1;
                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                $p_key = ($arr["user_id"] != "") ? $arr["user_id"] : $arr["user_id"];
                    $image_arr["pk"] = $p_key;
                $image_arr["color"] = "FFFFFF";
                $image_arr["no_img"] = false;
                $image_arr["path"] =$aws_folder_name. "/user_profile";
                $data_1 = $this->general->get_image_aws($image_arr);
                $arr['user_profile_image'] = $data_1;
                 
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     /**
     * untag_tagged_people  method is used to execute database queries for untag user.
     * @created Snehal Shinde | 31-3-2021
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function untag_tagged_people($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["missing_pet_id"]))
            {
                $this->db->where("iMissingPetId =", $params_arr["missing_pet_id"]);
            }
            if (isset($params_arr["untag_user_id"]))
            {
                $this->db->where(" iTagTo =", $params_arr["untag_user_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->where(" iTagFrom =", $params_arr["user_id"]);
            }
            if (isset($params_arr["tag_id"]))
            {
                $this->db->where(" iTagId =", $params_arr["tag_id"]);
            }
            $this->db->stop_cache();
           
            $res = $this->db->delete("tag_people");

            $affected_rows = $this->db->affected_rows();

            //echo $this->db->last_query();exit;
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in deletion.");
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

}