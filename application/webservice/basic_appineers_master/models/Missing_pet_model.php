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
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
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


            $this->db->set("dtAddedAt", $params_arr["_dtaddedat"]);
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
        #echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


 //  set tagged people to missing pet post
    public function set_tagged_people($params_arr = array(),$inserted_missing_pet_id)
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
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_missing_pet_details($arrResult)
    {
        try
        {
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';    
            
            $this->db->from("missing_pets AS i");
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
            $this->db->select("u.vLastName AS owner_last_name");
            $this->db->select("u.vFirstName AS owner_first_name");
            $this->db->select("u.vAptSuite AS owner_apt_suit");
            $this->db->select("u.tAddress AS owner_apt_suit");
            $this->db->join("users as u","i.iUserId = u.iUserId", "left");

            $strWhere = "ePostStatus ='Active'";

            if(false == empty($arrResult['user_id']))
            {
                $strWhere.= "AND i.iUserId ='".$arrResult['user_id']."'";
               
            }

            if(false == empty($arrResult['pet_status']) && 'found' == $arrResult['pet_status'])
            {
                $strWhere.= " AND ePetStatus = '".$arrResult['pet_status']."'";
               
            }
            if(false == empty($arrResult['pet_status']) && 'missing' == $arrResult['pet_status'] || 'found' == $arrResult['pet_status'])
            {
                $strWhere.= " AND ePetStatus in ('missing','found') ";
            }
            if(false == empty($arrResult['missing_pet_id']))
            {
                $strWhere.= " AND iMissingPetId = '".$arrResult['missing_pet_id']."'";
                
            }
             $this->db->where($strWhere);

            $result_obj = $this->db->get();

            // echo $this->db->last_query();exit;
            
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
    public function get_item_details_by_serial_number($arrResult)
    {
        try
        {
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';            
            

            $strWhere = "eItemStatus ='Active'"; 
            if(false == empty($arrResult['item_serial_number']))
            {
                $strWhere = $strWhere." AND vItemSerialNumber = '".$arrResult['item_serial_number']."'";
            }
            
            $strSql="SELECT

                    i.iItemId AS item_id, 
                    i.vItemName AS item_name,           
                    i.vItemSerialNumber AS item_serial_number,
                    i.dDateOfPurchase AS item_purchase_date,            
                    i.vPurchasedStreetAddress AS item_purchase_street_address, 
                    i.vPurchasedCity AS item_purchase_city,            
                    i.vPurchasedState AS item_purchase_state,
                    i.iPurchasedZipCode AS item_purchase_zip_code,
                    i.vDescription AS item_description,
                    i.vStoreName AS item_store_name,
                    i.eItemStatus AS item_status,
                    i.eItemReportStatus AS item_report_status,
                    i.dDateOfLost AS item_lost_date,
                    i.vLostStreetAddress AS item_lost_street_address, 
                    i.vLostCity AS item_lost_city,            
                    i.vLostState AS item_lost_state,
                    i.iLostZipCode AS item_lost_zip_code,
                    i.dLostLatitude AS item_lost_latitude, 
                    i.dLostLongitude AS item_lost_longitude,
                    i.dDateOfFound AS item_found_date, 
                    i.vFoundStreetAddress AS item_found_street_address,
                    i.vFoundCity AS item_found_city,            
                    i.vFoundState AS item_found_state, 
                    i.iFoundZipCode AS item_found_code,
                    i.dFoundLatitude AS item_found_latitude, 
                    i.dFoundLongitude AS item_found_longitude,
                    i.vImage_ID1 AS image_1,
                    i.vImage_ID2 AS image_2,
                    i.vImage_ID3 AS image_3,
                    i.vImage_ID4 AS image_4,
                    i.vImage_ID5 AS image_5,
                    concat(user.vFirstName,' ',user.vLastName) AS item_owner_name,
                    user.vEmail AS item_owner_email,
                    user.vMobileNo AS item_owner_phone,
                    user.iUserId AS item_owner_userId,
                    user.vProfileImage AS item_owner_image
                    FROM
                    item AS i
                    LEFT JOIN users AS user ON user.iUserId = i.iUserId
                    WHERE $strWhere";
            $strFinalQuery = $strSql;
            $result_obj = $this->db->query($strFinalQuery);
            
            // echo $this->db->last_query();exit;
            
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    


   /**
     * update_profile method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_missing_pet($params_arr = array(), $where_arr = array())
    {
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
            
                if (isset($params_arr["missing_pet_found_user_id"]))
                {
                    $this->db->set("vFoundUser", $params_arr["missing_pet_found_user_id"]);
                }
                if (isset($params_arr["missing_pet_found_street_address"]))
                {
                   $this->db->set("vFoundStreetAddress", $params_arr["missing_pet_found_street_address"]);
                }
                if (isset($params_arr["missing_pet_found_city"]))
                {
                    $this->db->set("vFoundCity", $params_arr["missing_pet_found_city"]);
                }
                if (isset($params_arr["missing_pet_found_state"]))
                {
                    $this->db->set("vFoundState", $params_arr["missing_pet_found_state"]);
                }
                if (isset($params_arr["missing_pet_found_latitude"]))
                {
                   $this->db->set("dFoundLatitude", $params_arr["missing_pet_found_latitude"]);
                }
                if (isset($params_arr["missing_pet_found_longitude"]))
                {
                   $this->db->set("dFoundLongitude", $params_arr["missing_pet_found_longitude"]);
                }
                if (isset($params_arr["missing_pet_found_date"]))
                {
                    $this->db->set("tUniqueTimeStamp", $params_arr["missing_pet_found_date"]);
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

                $res = $this->db->update("missing_pets");
                //echo $this->db->last_query();exit;
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
     * delete_missing_pet method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
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
