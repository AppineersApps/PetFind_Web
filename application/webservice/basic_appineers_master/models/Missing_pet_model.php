<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of missing pet Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User review
 *
 * @class Missing_pet_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Missing_pet_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Missing_pet_model extends CI_Model
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
     * set_missing_pet method is used to execute database queries for Set missing pet post
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process input params.
     * @return array $return_arr returns response of added missing pet post.
     */
    public function set_missing_pet($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            // print_r($params_arr["_dtaddedat"]);exit;
            // $this->db->set("dtAddedAt", $params_arr["_dtaddedat"]);
             $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);

            $this->db->set("ePetStatus", $params_arr["ePetStatus"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["dog_name"]))
            {
                $this->db->set("vDogsName", $params_arr["dog_name"]);
            }
            if (isset($params_arr["last_seen_date"]))
            {
                $this->db->set("vDogLastSeen", $params_arr["last_seen_date"]);
            }
            if (isset($params_arr["date_of_birth"]))
            {
                $this->db->set("vDogsDob", $params_arr["date_of_birth"]);
            }
            if (isset($params_arr["last_seen_street"]))
            {
                $this->db->set("vDogLastSeenStreet", $params_arr["last_seen_street"]);
            }
            if (isset($params_arr["last_seen_city"]))
            {
                $this->db->set("vLastSeenCity", $params_arr["last_seen_city"]);
            }
            if (isset($params_arr["last_seen_state"]))
            {
                $this->db->set("vLastSeenState", $params_arr["last_seen_state"]);
            }
            if (isset($params_arr["last_seen_zip_code"]))
            {
                $this->db->set("vLastSeenZipCode", $params_arr["last_seen_zip_code"]);
            }
            if (isset($params_arr["last_seen_lattitude"]))
            {
                $this->db->set("vLastSeenLattitude", $params_arr["last_seen_lattitude"]);
            }
            if (isset($params_arr["last_seen_longitude"]))
            {
                $this->db->set("vLastSeenLongitude", $params_arr["last_seen_longitude"]);
            }
            if (isset($params_arr["hair_color"]))
            {
                $this->db->set("vHairColor", $params_arr["hair_color"]);
            }
            if (isset($params_arr["eye_color"]))
            {
                $this->db->set("vEyeColor", $params_arr["eye_color"]);
            }
            if (isset($params_arr["height"]))
            {
                $this->db->set("vHeight", $params_arr["height"]);
            }
             if (isset($params_arr["weight"]))
            {
                $this->db->set("iWeight", $params_arr["weight"]);
            }
             if (isset($params_arr["gender"]))
            {
                $this->db->set("eGender", $params_arr["gender"]);
            } 
            if (isset($params_arr["breed"]))
            {
                $this->db->set("vBreed", $params_arr["breed"]);
            } 
            if (isset($params_arr["body_type"]))
            {
                $this->db->set("vBodyType", $params_arr["body_type"]);
            }
            if (isset($params_arr["identity_mark"]))
            {
                $this->db->set("vIdentyMark", $params_arr["identity_mark"]);
            }
             if (isset($params_arr["dog_details"]))
            {
                $this->db->set("vdogDetails", $params_arr["dog_details"]);
            }
            
            
            $this->db->insert("missing_pets");
            $insert_id = $this->db->insert_id();
               // echo "<pre>"; print_r($this->db->last_query());exit;
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "missing_pet_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        //$this->db->_reset_all();
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
 //  set tagged people to missing pet post
/**
     * set_tagged_people method is used to execute database queries for set tagged people to missing pet post
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process input params.
     * @return array $return_arr returns tag id.
*/

    public function set_tagged_people($params_arr = array(),$inserted_missing_pet_id)
    {
       // echo '<pre>'; print_r($params_arr['tag_people']);exit;
         try
        {
             $result_arr = array();
             $blockedUserId = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
             if (isset($params_arr["user_id"]))
            {
                $tagged_people=explode(",",$params_arr["tag_people"]);
                // $current_date=date("Y-m-d H:i:s");
                $current_date="NOW()";
                $blocked_user_name=array();
    
               foreach ($tagged_people as $tag_people_value) {

                $this->db->from("blocked_user AS bu");
                $this->db->select("bu.iBlockedId  AS block_id");
                $this->db->where("(bu.iBlockedTo = ".$tag_people_value." AND bu.iBlockedFrom = ".$params_arr["user_id"].") OR (bu.iBlockedTo = ".$params_arr["user_id"]." AND bu.iBlockedFrom = ".$tag_people_value.")", FALSE, FALSE);
                $this->db->limit(1);
                $result_obj = $this->db->get();
                $blocked_user_data = is_object($result_obj) ? $result_obj->result_array() : array();



                //  if user is not blocked then mark that user as tag user
                if(count($blocked_user_data)=='0')
                {
                    $this->db->set("iMissingPetId", $inserted_missing_pet_id);
                     $this->db->set("iTagFrom", $params_arr["user_id"]);
                     $this->db->set("iTagTo", $tag_people_value);
                     $this->db->set("dtAddedAt", $current_date);
                      $this->db->insert("tag_people");
                      $insert_id = $this->db->insert_id();

                      if (!$insert_id)
                        {
                            throw new Exception("Failure in insertion.");
                        }

                }
                else
                {
                     $blocked_user_id = $tag_people_value;
                     $this->db->from("users AS us");
                     $this->db->select("us.iUserId AS blocked_user_id");
                     $this->db->select("concat(us.vFirstName,\" \",us.vLastName) AS user_name");
                    $this->db->where("us.iUserId  ", $tag_people_value);
                    $block_user_result=$this->db->get()->row();
                    array_push($result_arr,$block_user_result->user_name);
                    array_push($blockedUserId,$block_user_result->blocked_user_id);
                    
                }

                }
              
            }
               
            $result_arr_1['blocked_user'] = $result_arr;
            $result_arr_1['blocked_user_id'] = $blockedUserId;
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
        $return_arr["data"] = $result_arr_1;
        return $return_arr;
    }


 //  set tagged people to missing pet post
/**
     * set_tagged_people method is used to execute database queries for set tagged people to missing pet post
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process input params.
     * @return array $return_arr returns tag id.
*/

    public function set_tagged_people_20_4($params_arr = array(),$inserted_missing_pet_id)
    {
       // echo '<pre>'; print_r($params_arr['tag_people']);exit;
         try
        {
             $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
             if (isset($params_arr["user_id"]))
            {
                $tagged_people=explode(",",$params_arr["tag_people"]);
                // $current_date=date("Y-m-d H:i:s");
                $current_date="NOW()";
    
               foreach ($tagged_people as $tag_people_value) {
              
                     $this->db->set("iMissingPetId", $inserted_missing_pet_id);
                     $this->db->set("iTagFrom", $params_arr["user_id"]);
                     $this->db->set("iTagTo", $tag_people_value);
                     $this->db->set("dtAddedAt", $current_date);
                      $this->db->insert("tag_people");
                      $insert_id = $this->db->insert_id();
                }
            }
               
               // echo "<pre>"; print_r($this->db->last_query());exit;
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "tag_id";
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
     * get_missing_pet_details method is used to get missing pet details 
     * @created Snehal Shinde | 01-03-2021
     * @param string $arrResult is used for input params .
     * @return array $return_arr returns missing pet details.
     */
    public function get_missing_pet_details($arrResult, &$settings_params)
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
            $this->db->join("users as u","i.iUserId = u.iUserId", "left");
            $this->db->join("blocked_user AS bu", "bu.iBlockedTo = ".$arrResult['user_id']." AND bu.iBlockedFrom = i.iUserId AND u.eStatus ='Active'", "left");

             $strWhere = "ePostStatus ='Active'";

            if(false == empty($arrResult['user_id']) && $arrResult['page_code']=='pet_list')
            {
                // $strWhere.= "AND i.iUserId ='".$arrResult['user_id']."'";
                $strWhere.= "AND i.ePetStatus = 'missing'";
               
            } 
            $strWhere.= " AND bu.iBlockedId IS NULL";
            if(false == empty($arrResult['user_id']) && $arrResult['page_code']=='my_pet_list')
            {
                $strWhere.= "AND i.iUserId ='".$arrResult['user_id']."'";
               
            }
            if(false == empty($arrResult['missing_pet_id']))
            {
                $strWhere.= " AND i.iMissingPetId = '".$arrResult['missing_pet_id']."'";
                
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

                if($arr["found_user_id"]!=null)
                {

                   $result_found = array();
                    $strSql="SELECT 
                    
                    u.vFirstName AS found_user_first_name,
                     u.vLastName AS found_user_last_name
                     FROM users AS u      
                     LEFT JOIN missing_pets AS i ON (u.iUserId = i.vFoundUser)             
                     WHERE i.iMissingPetId='".$arr['missing_pet_id']."'";
                    $result_obj = $this->db->query($strSql);

                   // echo $this->db->last_query();exit;
                    $result_found = is_object($result_obj) ? $result_obj->result_array() : array();
                    $this->db->reset_query();
                    $this->db->_reset_all();

                    $found_user_first_name=$result_found[0]['found_user_first_name'];
                    $found_user_last_name=$result_found[0]['found_user_last_name'];

                     
                }
                else
                {
                    $found_user_first_name='';
                    $found_user_last_name='';
                }
                 if($arr["height"]!=null)
                {
                        preg_match_all('!\d+!', $arr['height'], $height_split);
                        $height_feet=$height_split[0][0];
                        $height_inches=$height_split[0][1];

                }
                else
                {   
                        $height_feet='';
                        $height_inches='';
                }

               
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

 // get dog owner image path from aws 
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
                 $arr['height_feet'] = $height_feet;
                $arr['height_inches'] = $height_inches;
                $arr['found_user_first_name'] = $found_user_first_name;
                $arr['found_user_last_name'] = $found_user_last_name;
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
     * get_missing_pet_details method is used to get missing pet details 
     * @created Snehal Shinde | 01-03-2021
     * @param string $arrResult is used for input params .
     * @return array $return_arr returns missing pet details.
     */
    public function get_missing_pet_details_22_4($arrResult, &$settings_params)
    {
        try
        {
            $result_arr = array();
             $this->db->start_cache();

            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';    
            
            $this->db->from("missing_pets AS i");
            $this->db->join("users as u","i.iUserId = u.iUserId", "left");
            $this->db->join("blocked_user AS bu", "bu.iBlockedTo = ".$arrResult['user_id']." AND bu.iBlockedFrom = i.iUserId AND u.eStatus ='Active'", "left");
             $strWhere = "ePostStatus ='Active'";

            if(false == empty($arrResult['user_id']) && $arrResult['page_code']=='pet_list')
            {
                // $strWhere.= "AND i.iUserId ='".$arrResult['user_id']."'";
                $strWhere.= "AND i.ePetStatus = 'missing'";
               
            } 
            $strWhere.= " AND bu.iBlockedId IS NULL";
            if(false == empty($arrResult['user_id']) && $arrResult['page_code']=='my_pet_list')
            {
                $strWhere.= "AND i.iUserId ='".$arrResult['user_id']."'";
               
            }
            if(false == empty($arrResult['missing_pet_id']))
            {
                $strWhere.= " AND i.iMissingPetId = '".$arrResult['missing_pet_id']."'";
                
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
                    $total_tags=$tag_result[0]['total_tags'];

                    $comment_result = array();
                    $strSql2="SELECT 
                     count(c.iCommentId) AS total_comments
                     FROM comments AS c WHERE c.iMissingPetId='".$arr['missing_pet_id']."'";
                    $result_obj2 = $this->db->query($strSql2);
                    $comment_result = is_object($result_obj2) ? $result_obj2->result_array() : array();

                    $total_comments=$comment_result[0]['total_comments'];

                if($arr["found_user_id"]!=null)
                {

                   $result_found = array();
                    $strSql="SELECT 
                    
                    u.vFirstName AS found_user_first_name,
                     u.vLastName AS found_user_last_name
                     FROM users AS u      
                     LEFT JOIN missing_pets AS i ON (u.iUserId = i.vFoundUser)             
                     WHERE i.iMissingPetId='".$arr['missing_pet_id']."'";
                    $result_obj = $this->db->query($strSql);

                   // echo $this->db->last_query();exit;
                    $result_found = is_object($result_obj) ? $result_obj->result_array() : array();



                    $found_user_first_name=$result_found[0]['found_user_first_name'];
                    $found_user_last_name=$result_found[0]['found_user_last_name'];

                     
                }
                else
                {
                    $found_user_first_name='';
                    $found_user_last_name='';
                }
                 if($arr["height"]!=null)
                {
                        preg_match_all('!\d+!', $arr['height'], $height_split);
                        $height_feet=$height_split[0][0];
                        $height_inches=$height_split[0][1];

                }
                else
                {   
                        $height_feet='';
                        $height_inches='';
                }





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
                 $arr['height_feet'] = $height_feet;
                $arr['height_inches'] = $height_inches;
                $arr['found_user_first_name'] = $found_user_first_name;
                $arr['found_user_last_name'] = $found_user_last_name;
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
     * update_missing_pet method is used to execute database queries for Edit Missing pet post details.
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_missing_pet($params_arr = array(), $where_arr = array())
    {
        //print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            $this->db->start_cache();
                if (isset($where_arr["missing_pet_id"]) && $where_arr["missing_pet_id"] != "")
                {
                    $this->db->where("iMissingPetId  =", $where_arr["missing_pet_id"]);
                }
                if (isset($params_arr["missing_pet_id"]) && $params_arr["missing_pet_id"] != "")
                {
                    $this->db->where("iMissingPetId   =", $params_arr["missing_pet_id"]);
                }
                 //$this->db->set("dtUpdatedAt", $params_arr["_dtupdatedat"]);
                 $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
                $this->db->stop_cache();
                if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["dog_name"]))
            {
                $this->db->set("vDogsName", $params_arr["dog_name"]);
            }
            if (isset($params_arr["last_seen_date"]))
            {
                $this->db->set("vDogLastSeen", $params_arr["last_seen_date"]);
            }
            if (isset($params_arr["date_of_birth"]))
            {
                $this->db->set("vDogsDob", $params_arr["date_of_birth"]);
            }
            if (isset($params_arr["last_seen_street"]))
            {
                $this->db->set("vDogLastSeenStreet", $params_arr["last_seen_street"]);
            }
            if (isset($params_arr["last_seen_city"]))
            {
                $this->db->set("vLastSeenCity", $params_arr["last_seen_city"]);
            }
            if (isset($params_arr["last_seen_state"]))
            {
                $this->db->set("vLastSeenState", $params_arr["last_seen_state"]);
            }
            if (isset($params_arr["last_seen_zip_code"]))
            {
                $this->db->set("vLastSeenZipCode", $params_arr["last_seen_zip_code"]);
            }
            if (isset($params_arr["last_seen_lattitude"]))
            {
                $this->db->set("vLastSeenLattitude", $params_arr["last_seen_lattitude"]);
            }
            if (isset($params_arr["last_seen_longitude"]))
            {
                $this->db->set("vLastSeenLongitude", $params_arr["last_seen_longitude"]);
            }
            if (isset($params_arr["hair_color"]))
            {
                $this->db->set("vHairColor", $params_arr["hair_color"]);
            }
            if (isset($params_arr["eye_color"]))
            {
                $this->db->set("vEyeColor", $params_arr["eye_color"]);
            }
            if (isset($params_arr["height"]))
            {
                $this->db->set("vHeight", $params_arr["height"]);
            }
             if (isset($params_arr["weight"]))
            {
                $this->db->set("iWeight", $params_arr["weight"]);
            }
             if (isset($params_arr["gender"]))
            {
                $this->db->set("eGender", $params_arr["gender"]);
            } 
            if (isset($params_arr["breed"]))
            {
                $this->db->set("vBreed", $params_arr["breed"]);
            } 
            if (isset($params_arr["body_type"]))
            {
                $this->db->set("vBodyType", $params_arr["body_type"]);
            }
            if (isset($params_arr["identity_mark"]))
            {
                $this->db->set("vIdentyMark", $params_arr["identity_mark"]);
            }
             if (isset($params_arr["dog_details"]))
            {
                $this->db->set("vdogDetails", $params_arr["dog_details"]);
            }
            
               if(isset($params_arr["image_1"]))
                {
                    $this->db->set("vImageId_1", $params_arr["image_1"]);
                }
                if(isset($params_arr["image_2"]))
                {
                    $this->db->set("vImageId_2", $params_arr["image_2"]);
                }
                if(isset($params_arr["image_3"]))
                {
                    $this->db->set("vImageId_3", $params_arr["image_3"]);
                }
                if(isset($params_arr["image_4"]))
                {
                    $this->db->set("vImageId_4", $params_arr["image_4"]);
                }
                if(isset($params_arr["image_5"]))
                {
                    $this->db->set("vImageId_5", $params_arr["image_5"]);
                }
                if(isset($params_arr["ePetStatus"]))
                {
                    $this->db->set("ePetStatus", $params_arr["ePetStatus"]);
                }

                $res = $this->db->update("missing_pets");
                // echo $this->db->last_query();exit;
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

      /**
     * delete_missing_pet method is used to execute database queries for delete missing pet  post.
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_missing_pet($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["missing_pet_id"]))
            {
                $this->db->where("iMissingPetId =", $params_arr["missing_pet_id"]);
            }
            $this->db->stop_cache();
           
            $res = $this->db->delete("missing_pets");

            $affected_rows = $this->db->affected_rows();
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
