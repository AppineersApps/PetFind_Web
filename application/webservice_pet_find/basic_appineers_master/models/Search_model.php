<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Search Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Search
 *
 * @class Search_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Search_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Search_model extends CI_Model
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
     * get_review_details method is used to execute database queries for search.
     * @created Snehal Shinde | 22-03-2021
     * @param string $arrResult is having input parameters
     * @return array $return_arr returns response of missing pet lists.
     */
    public function get_missing_pet_list($arrResult, &$settings_params)
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
            
            $this->db->from("missing_pets AS i");
            $this->db->join("users as u","i.iUserId = u.iUserId", "inner");

            
            if(false == empty($arrResult['user_id']) && $arrResult['page_code'] == "home")
            {
                $strWhere = "i.ePostStatus ='Active' AND (`i`.`vDogsName` LIKE '%".$arrResult['keyword']."%' ESCAPE '!' OR `u`.`vLastName` LIKE '%".$arrResult['keyword']."%' ESCAPE '!' OR `u`.`vFirstName` LIKE '%".$arrResult['keyword']."%' ESCAPE '!'
                    OR `i`.`vDogLastSeenStreet` LIKE '%".$arrResult['keyword']."%' ESCAPE '!'
                    OR `i`.`vLastSeenCity` LIKE '%".$arrResult['keyword']."%' ESCAPE '!')
                     ";

                $strWhere.= "AND i.ePetStatus = 'missing' AND i.iUserId !=".$arrResult['user_id'];
               
            } 
            if (isset($arrResult['page_code']) && $arrResult['page_code'] == "my_missing_dogs")
            {
                $strWhere = "i.ePostStatus ='Active' AND (`i`.`vDogsName` LIKE '%".$arrResult['keyword']."%' ESCAPE '!' 
                    OR `i`.`vDogLastSeenStreet` LIKE '%".$arrResult['keyword']."%' ESCAPE '!'
                    OR `i`.`vLastSeenCity` LIKE '%".$arrResult['keyword']."%' ESCAPE '!')
                     ";

                $strWhere.= "AND i.iUserId =".$arrResult['user_id'];
            }

             $this->db->where($strWhere);

            $this->db->stop_cache();
            $total_records = $this->db->count_all_results();

            $this->db->select("i.iMissingPetId AS missing_pet_id"); 
            $this->db->select("i.iUserId AS user_id"); 
            $this->db->select("i.vDogsName AS dog_name");
             $this->db->select("i.vDogsDob AS date_of_birth");           
            $this->db->select("i.vDogLastSeen AS last_seen_date");
            $this->db->select("i.vDogLastSeenStreet AS last_seen_street");            
            $this->db->select("i.vLastSeenCity AS last_seen_city"); 
            $this->db->select("i.vLastSeenState AS last_seen_state");            
            $this->db->select("i.vLastSeenZipCode AS last_seen_zip_code"); 
            $this->db->select("i.vLastSeenLattitude AS last_seen_latitude");           
            $this->db->select("i.vLastSeenLongitude AS last_seen_longitude");
            $this->db->select("i.vHairColor AS hair_color");
            $this->db->select("i.vEyeColor AS eye_color");
            $this->db->select("i.vHeight AS height"); 
            $this->db->select("i.iWeight AS weight");            
            $this->db->select("i.eGender AS gender");
            $this->db->select("i.vBreed AS breed");  
            $this->db->select("i.vIdentyMark AS identity_mark");  
            $this->db->select("i.vBodyType AS body_type");  
            $this->db->select("i.vdogDetails AS dog_details");  
            $this->db->select("i.ePetStatus AS pet_status");  
            $this->db->select("i.vFoundUser AS found_user_id");  
            $this->db->select("i.vFoundStreetAddress AS pet_found_street_address"); 
            $this->db->select("i.vFoundCity AS pet_found_city");            
            $this->db->select("i.vFoundState AS pet_found_state"); 
            $this->db->select("i.vFoundLattitude AS pet_found_latitude"); 
            $this->db->select("i.vFoundLongitude AS pet_found_longitude");
            $this->db->select("i.tUniqueTimeStamp AS pet_found_date");
            $this->db->select("i.vImageId_1 AS image_1");
            $this->db->select("i.vImageId_2 AS image_2");
            $this->db->select("i.vImageId_3 AS image_3");
            $this->db->select("i.vImageId_4 AS image_4");
            $this->db->select("i.vImageId_5 AS image_5");
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

             $settings_params['count'] = $total_records;
            $record_limit = intval("".$arrResult["perpage_record"]."");
            $current_page = intval($arrResult["page_index"]) > 0 ? intval($arrResult["page_index"]) : 1;
            $total_pages = getTotalPages($total_records, $record_limit);
            $start_index = getStartIndex($total_records, $current_page, $record_limit);
            $settings_params['per_page'] = $record_limit;
            $settings_params['curr_page'] = $current_page;
            $settings_params['prev_page'] = ($current_page > 1) ? 1 : 0;
            $settings_params['next_page'] = ($current_page+1 > $total_pages) ? 0 : 1;
            
            
             $this->db->group_by("i.iMissingPetId");
             $this->db->order_by("i.iMissingPetId",'desc');
             $this->db->limit($record_limit, $start_index);
            $result_obj = $this->db->get();
            $settings_params['count'] = $result_obj->num_rows();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $this->db->reset_query();
            $this->db->_reset_all();
// print_r($result_arr);exit;
            // echo $this->db->last_query();exit;
            
//  here we recreate array for fetching user profile image from aws and add it to final array
            $result_arr = array_map(function (array $arr) { 


                $tagcomment_result = array();
                    $strSql1="SELECT 
                    
                     count(t.iTagId) AS total_tags FROM tag_people AS t               
                     WHERE t.iMissingPetId='".$arr['missing_pet_id']."'";
                    $result_obj1 = $this->db->query($strSql1);
                    $tag_result = is_object($result_obj1) ? $result_obj1->result_array() : array();
                    $this->db->reset_query();
                    $this->db->_reset_all();
                    $total_tags=$tag_result[0]['total_tags'];

                    $comment_result = array();
                    $strSql2="SELECT 
                     count(c.iCommentId) AS total_comments
                     FROM comments AS c WHERE c.iMissingPetId='".$arr['missing_pet_id']."'";
                    $result_obj2 = $this->db->query($strSql2);
                    $comment_result = is_object($result_obj2) ? $result_obj2->result_array() : array();
                    $this->db->reset_query();
                    $this->db->_reset_all();

                    $total_comments=$comment_result[0]['total_comments'];
                    
// get missing pet images path 
            if (isset($arr['missing_pet_id']) && $arr['missing_pet_id'] != "")
            {
                $this->db->from("missing_pet_images");
                $this->db->select("Distinct(iImageId) AS image_id");
                $this->db->select("vImage as image_url");
                $this->db->where("iMissingPetId =", $arr['missing_pet_id']);
                $this->db->where("iUserId =", $arr['user_id']);

                $result_obj_img = $this->db->get();
                $pet_images=$result_obj_img->result_array();
                $missing_pet_id=$arr['missing_pet_id'];
                //  get aws images path for each image 
                $imgArr = [];
                foreach ($pet_images as $key => $value)
                {
                   
                    $data1 = $value["image_url"];
                        $image_arr = array();
                        $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                        $image_arr["image_name"] = $data1;
                         $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                         $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $p_key = ($arr["missing_pet_id"] != "") ? $arr["missing_pet_id"] : $arr["missing_pet_id"];
                        $image_arr["pk"] = $p_key;
                        $image_arr["path"] =$aws_folder_name. "/missing_pet_image";
                        $data1 = $this->general->get_image_aws($image_arr);
                         $imgArr[$key]["image_id"]=$value["image_id"];
                         $imgArr[$key]["image_url"]=$data1;
                }                                 
            }
            $arr['missing_pet_image']=$imgArr;

// get pet owner image 

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
                $arr['total_tags'] = $total_tags;
                $arr['total_comments'] = $total_comments;

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
     * get_user_list method is used to execute database queries for search.
     * @created Snehal Shinde | 22-03-2021
     * @param string $arrResult  is used for input parameters.
     * @return array $return_arr returns response of user lists.
     */
    public function get_user_list($arrResult, &$settings_params)
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
            
            $this->db->from("users AS u");
             $strWhere = "u.eStatus ='Active' AND u.iUserId !='".$arrResult['user_id']."'";

             $this->db->where($strWhere);
             if(false == empty($arrResult['user_id']))
            {
                $this->db->where("(`u`.`vFirstName` LIKE '%".$arrResult['keyword']."%' ESCAPE '!'
OR `u`.`vLastName` LIKE '%".$arrResult['keyword']."%' ESCAPE '!')", FALSE, FALSE);
                
                // $this->db->like("u.vFirstName", $arrResult['keyword'],"both"); 
                // $this->db->or_like("u.vLastName", $arrResult['keyword'],"both");
               
            }
            $this->db->stop_cache();

            $this->db->select("u.iUserId AS user_id");
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
            $settings_params['count'] = $result_obj->num_rows();
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





    


}
