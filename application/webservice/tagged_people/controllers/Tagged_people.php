<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of tagged_people Controller
 *
 * @category webservice
 *
 * @package tagged_people
 * 
 * @subpackage controllers
 *
 * @module Missing pet
 *
 * @class tagged_people.php
 *
 * @path application\webservice\tagged_people\controllers\tagged_people.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 
 */

class Tagged_people extends Cit_Controller
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
        $this->single_keys = array();
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('tagged_people_model');
    }

/**
     * start_tagged_people method is used to initiate api execution flow.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
 */
    public function start_tagged_people($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
          case 'GET':
                    // print_r($request_arr);exit;
                    $output_response =  $this->get_tagged_people($request_arr);
                    return  $output_response;
                    break;

          case 'POST':
                     // print_r($request_arr);exit; 
                     $output_response =  $this->untag_tagged_people($request_arr);
                     return  $output_response;
                     break;

          case 'DELETE':

                    $output_response = "";
                    return  $output_response;
                    break;
        }
    }

    
/**
     * get_tagged_people method is used to initiate api execution flow of get tagged people list.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
*/
    public function get_tagged_people($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_tagged_people($request_arr);
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
            $input_params = $this->get_all_tagged_people($input_params);
            // print_r($input_params);exit;
            $condition_res = $this->is_get_record($input_params);
           
            if ($condition_res["success"])
            {
                
                $output_response = $this->get_tagged_people_finish_success($input_params);
                return $output_response;
            }

            else
            {
                $output_response = $this->get_tagged_people_finish_failed($input_params);
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
     * rules_get_tagged_people method is used to validate api input params.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_tagged_people($request_arr = array())
    {

            $valid_arr = array(            
        
           "user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "user_id_number"
                    )
                ),
            "missing_pet_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "missing_pet_id_required"
                )
            )
            );

           $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_tagged_people");

           return $valid_res;
    }

 /**
     * get_all_tagged_people method is used to fetch tagged people list.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_tagged_people($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $arrResult = array();
                      
           
            $arrResult['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $arrResult['missing_pet_id'] = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            
            $this->block_result = $this->tagged_people_model->get_tagged_people($arrResult);

            // echo'<pre>';print_r($this->block_result);exit;
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
        $input_params["get_all_tagged_people"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }    

/**
     * is_get_record method is used to check conditions.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
 */
    public function is_get_record($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $cc_lo_0 = (is_array($input_params["tag_id"])) ? count($input_params["tag_id"]):$input_params["tag_id"];
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
     * get_tagged_people_finish_success method is used to process finish flow of fetch listing success.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_tagged_people_finish_success($input_params = array())
    {
            $setting_fields = array(
            "success" => "1",
            "message" => "get_tagged_people_finish_success"
             );
      
        $output_fields = array(
             "tag_id",
             "missing_pet_id",
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
            'get_all_tagged_people',
        );
        $ouput_aliases = array(
            "tag_id" => "tag_id",
            "missing_pet_id" => "missing_pet_id",
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
        
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "get_tagged_people";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
/**
     * get_tagged_people_finish_failed method is used to process finish flow of get operation failed.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_tagged_people_finish_failed($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_tagged_people_finish_failed",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_tagged_people";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * untag_tagged_people method is used to Update missing pet post
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function untag_tagged_people($request_arr = array(), $inner_api = FALSE)
    {

        try
        {
            $validation_res = $this->rules_untag_tagged_people($request_arr);

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

            $input_params = $this->check_taged_people_exist($input_params);
          
            if ($input_params["checkTaggedPeopleExist"]["status"])
            {   
                $input_params = $this->update_tag_people($input_params);
                if ($input_params["affected_rows"])
                {
                   
                    
                    $output_response = $this->untag_tagged_people_success($input_params);
                        return $output_response;
                }else{
                    $output_response = $this->untag_tagged_people_failed($input_params);
                    return $output_response;
                }
            }
            else
            {

                $output_response = $this->untag_tagged_people_no_data($input_params);
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
     * rules_untag_tagged_people method is used to validate api input params.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
 */
    public function rules_untag_tagged_people($request_arr = array())
    {

            $valid_arr = array(            
        
           "user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "user_id_number"
                    )
                ),
           "untag_user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "untag_user_id_number"
                    )
                ),
            "missing_pet_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "missing_pet_id_required"
                )
            )
            );

           $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "untag_tagged_people");

           return $valid_res;
    }

      /**
     * check_taged_people_exist method is used to check tag is exist or not.
     * @created Snehal Shinde  | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_taged_people_exist($input_params = array())
    {

        if (!method_exists($this, "checkTaggedPeopleExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkTaggedPeopleExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkTaggedPeopleExist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        // print_r($input_params);
        return $input_params;
    }

    /**
     * update_tag_people  method is used to untag user
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_tag_people($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
           
            $arrResult['missing_pet_id']  = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $arrResult['untag_user_id']  = isset($input_params["untag_user_id"]) ? $input_params["untag_user_id"] : "";
            $arrResult['user_id']  = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $arrResult['tag_id']  = isset($input_params["tag_id"]) ? $input_params["tag_id"] : "";
            
            $this->block_result = $this->tagged_people_model->untag_tagged_people($arrResult);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
          $this->block_result["data"] = $result_arr;
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["untag_tagged_people"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }

/**
     * untag_tagged_people_success method is used to process finish flow.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
*/
    public function untag_tagged_people_success($input_params = array())
    {
     $setting_fields = array(
            "success" => "1",
            "message" => "untag_tagged_people_success"
        );
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "untag_tagged_people";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

        /**
     * untag_tagged_people_failed method is used to process finish flow of update operation failed.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function untag_tagged_people_failed($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "untag_tagged_people_failed",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "untag_tagged_people";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

  /**
     * untag_tagged_people_failed method is used to process finish flow of update operation failed.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function untag_tagged_people_no_data($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "untag_tagged_people_no_data",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "untag_tagged_people";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }




}