<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Search Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 * 
 * @subpackage controllers
 *
 * @module Search
 *
 * @class Search.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Search.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 
 */

class Search extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "set_missing_pet",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('search_model');
    }

    /**
     * start_search method is used to initiate api execution flow.
     * @created Snehal Shinde | 22-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_search($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
          case 'GET':
                        if(true == isset($request_arr['page_code']))
                        {
                            // print_r($request_arr);exit;
                           $output_response =  $this->get_missing_pets($request_arr);
                        
                        }
                        else
                        {
                             
                           $output_response =  $this->get_tag_people($request_arr);
                        }
                        return  $output_response;
                        break;

          case 'POST':

                       $output_response =  $this->add_missing_pet($request_arr);
                       return  $output_response;
                       break;

          case 'DELETE':

                        $output_response = $this->delete_missing_pet($request_arr);
                        return  $output_response;
                        break;
        }
    }
    
    /**
     * get_missing_pets method is used to initiate api execution flow of get missing pet list.
     * @created Snehal Shinde | 22-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function get_missing_pets($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_missing_pets($request_arr);
            if ($validation_res["success"] == "-5")
            {
                if ($inner_api === TRUE)
                {
                    return $validation_res;
                }
                else
                {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }


            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();
            $input_params = $this->get_all_missing_pets($input_params);
            
            $condition_res = $this->is_posted($input_params);
            if ($condition_res["success"])
            {
                
                $output_response = $this->get_missing_pet_finish_success($input_params);
                return $output_response;
            }

            else
            {
 
                $output_response = $this->get_missing_pet_finish_success_1($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

     /**
     * rules_get_missing_pets method is used to validate api input params.
     * @created Snehal Shinde | 22-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_missing_pets($request_arr = array())
    {

            $valid_arr = array(            
        
           "user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "user_id_number"
                    )
                ),
            "page_code" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "page_code_required"
                )
            )
            );
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_missing_pets");

        return $valid_res;
    }

     /**
     * get_all_missing_pets method is used to process review block.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_missing_pets($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $arrResult = array();
                      
           
            $arrResult['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            $arrResult['page_code'] = isset($input_params["page_code"]) ? $input_params["page_code"] : "";
            $arrResult['keyword'] = isset($input_params["keyword"]) ? $input_params["keyword"] : "";

             $arrResult['perpage_record'] = isset($input_params["perpage_record"]) ? $input_params["perpage_record"] : "";
            $arrResult['page_index'] = isset($input_params["page_index"]) ? $input_params["page_index"] : 1;

            $this->block_result = $this->search_model->get_missing_pet_list($arrResult,$this->settings_params);

            // echo'<pre>';print_r($this->block_result);echo'<pre>';exit;
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
           
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_all_missing_pets"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }

     /**
     * get_tag_people method is used to initiate api execution flow of get user list accornig to search name.
     * @created Snehal Shinde | 22-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function get_tag_people($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_tag_people($request_arr);
            if ($validation_res["success"] == "-5")
            {
                if ($inner_api === TRUE)
                {
                    return $validation_res;
                }
                else
                {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }


            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();
            $input_params = $this->get_search_user_list($input_params);
            
            if (count($input_params['get_user_list'])>0)
            {
                
                $output_response = $this->get_user_list_finish_success($input_params);
                return $output_response;
            }

            else
            {
 
                $output_response = $this->get_user_list_finish_failed($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

     /**
     * rules_get_tag_people method is used to validate api input params.
     * @created Snehal Shinde | 22-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_tag_people($request_arr = array())
    {

            $valid_arr = array(            
        
           "user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "user_id_number"
                    )
                )
            );
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_tag_people");

        return $valid_res;
    }



  /**
     * get_search_user_list method is used to get user list accorning to search criteria.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_search_user_list($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $arrResult = array();
                      
           
            $arrResult['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $arrResult['keyword'] = isset($input_params["keyword"]) ? $input_params["keyword"] : "";

            $this->block_result = $this->search_model->get_user_list($arrResult,$this->settings_params);

            // echo'<pre>';print_r($this->block_result);echo'<pre>';exit;
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
             $this->block_result["data"] = $result_arr;

                if (is_array($result_arr) && count($result_arr) > 0)
                {
                   
                    $this->block_result["data"] = $result_arr;
                }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_list"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }


    /**
     * is_posted method is used to check conditions.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_posted($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $cc_lo_0 = (is_array($input_params["missing_pet_id"])) ? count($input_params["missing_pet_id"]):$input_params["missing_pet_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    } 
   
  /**
     * get_missing_pet_finish_success method is used to process finish flow of fetch listing success.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_missing_pet_finish_success($input_params = array())
    {
       
            $setting_fields = array(
            "success" => "1",
            "message" => "home_search_success"
        );

        
       
        $output_fields = array(
             "missing_pet_id",
            "user_id",
            "dog_name",
            "date_of_birth",
            "last_seen_date",
            "last_seen_street",
            "last_seen_city",
            "last_seen_state",
            "last_seen_zip_code",
            "last_seen_latitude",
            "last_seen_longitude",
            "hair_color",
            "eye_color",
            "height",
            "weight",
            "gender",
            "breed",
            "identity_mark",
            "body_type",
            "dog_details",
            "pet_status",
            "found_user_id",
            "pet_found_street_address",
            "pet_found_city",
            "pet_found_state",
            "pet_found_latitude",
            "pet_found_longitude",
            "pet_found_date",
            "user_first_name",
            "user_last_name",
            "user_apt_suit",
            "user_address",
            "user_city",
            "user_state",
            "user_zip_code",
            "user_profile_image",
            "user_lattitude",
            "user_longitude",
            "total_tags",
            "total_comments",
            // "images"
            "missing_pet_image"

        );
         $output_keys = array(
            'get_all_missing_pets',
        );
        $ouput_aliases = array(
            "missing_pet_id" => "missing_pet_id",
            "user_id" => "user_id",
            "dog_name" => "dog_name",
            "date_of_birth" => "date_of_birth",
            "last_seen_date" => "last_seen_date",
            "last_seen_street" => "last_seen_street",
            "last_seen_city" =>"last_seen_city",
            "last_seen_state" => "last_seen_state",
            "last_seen_zip_code" => "last_seen_zip_code",
            "last_seen_latitude" => "last_seen_latitude",
            "last_seen_longitude" => "last_seen_longitude",
            "hair_color" => "hair_color",
            "eye_color" => "eye_color",
            "height" => "height",
            "weight" => "weight",
            "gender" => "gender",
            "breed" => "breed",
            "identity_mark" => "identity_mark",
            "body_type" => "body_type",
            "dog_details" => "dog_details",
            "pet_status" => "pet_status",
            "found_user_id" => "found_user_id",
            "pet_found_street_address" => "pet_found_street_address",
            "pet_found_city" => "pet_found_city",
            "pet_found_state" => "pet_found_state",
            "pet_found_latitude" => "pet_found_latitude",
            "pet_found_longitude" => "pet_found_longitude",
            "pet_found_date" => "pet_found_date",
            "user_last_name" => "user_last_name",
            "user_first_name" => "user_first_name",
            "user_apt_suit" => "user_apt_suit",
            "user_address" => "user_address",
            "user_city" => "user_city",
            "user_state" => "user_state",
            "user_zip_code" => "user_zip_code",
            "user_profile_image" => "user_profile_image",
            "user_lattitude" => "user_lattitude",
            "user_longitude" => "user_longitude",
            "total_tags" => "total_tags",
            "total_comments" => "total_comments",
            "missing_pet_image" => "missing_pet_image"
        );
        
        // $output_array["settings"] = $setting_fields;
        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "get_missing_pets";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
/**
     * get_update_finish_success_1 method is used to process finish flow of update operation failed.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_missing_pet_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "home_search_failed",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_missing_pets";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
 /**
     * get_missing_pet_finish_success method is used to process finish flow of fetch listing success.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_user_list_finish_success($input_params = array())
    {
       
            $setting_fields = array(
            "success" => "1",
            "message" => "tag_search_success"
        );

        
       
        $output_fields = array(
            "user_id",
            "user_first_name",
            "user_last_name",
            "user_apt_suit",
            "user_address",
            "user_city",
            "user_state",
            "user_zip_code",
            "user_profile_image",
            "user_lattitude",
            "user_longitude"

        );
         $output_keys = array(
            'get_user_list',
        );
        $ouput_aliases = array(
            "user_id" => "user_id",
            "user_last_name" => "user_last_name",
            "user_first_name" => "user_first_name",
            "user_apt_suit" => "user_apt_suit",
            "user_address" => "user_address",
            "user_city" => "user_city",
            "user_state" => "user_state",
            "user_zip_code" => "user_zip_code",
            "user_profile_image" => "user_profile_image",
            "user_lattitude" => "user_lattitude",
            "user_longitude" => "user_longitude"
        );
        
        // $output_array["settings"] = $setting_fields;
        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "get_tag_people";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
/**
     * get_update_finish_success_1 method is used to process finish flow of update operation failed.
     * @created Snehal Shinde | 22-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_user_list_finish_failed($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "tag_search_failed",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_tag_people";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


}

     
