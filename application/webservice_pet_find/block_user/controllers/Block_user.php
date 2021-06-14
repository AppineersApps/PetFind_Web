<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Block_user Controller
 *
 * @category webservice
 *
 * @package Block_user
 * 
 * @subpackage controllers
 *
 * @module Block_user
 *
 * @class Block_user.php
 *
 * @path application\webservice\block_user\controllers\Block_user.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 
 */

class Block_user extends Cit_Controller
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
        $this->load->model('block_user_model');
    }

/**
     * start_block_user method is used to initiate api execution flow.
     * @created Snehal Shinde | 12-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
 */
    public function start_block_user($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
          case 'GET':
                    $output_response =  $this->get_blocked_users($request_arr);
                    return  $output_response;
                    break;

          case 'POST':
                     // print_r($request_arr);exit; 
                     $output_response =  $this->block_user($request_arr);
                     return  $output_response;
                     break;

          case 'DELETE':

                    $output_response = "";
                    return  $output_response;
                    break;
        }
    }

    
/**
     * get_blocked_users method is used to initiate api execution flow of get tagged people list.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
*/
    public function get_blocked_users($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_blocked_users($request_arr);
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
            $input_params = $this->get_blocked_users_list($input_params);
             
            $condition_res = $this->is_get_user_list($input_params);
           
            if ($condition_res["success"])
            {
              // print_r($input_params);exit;
                $output_response = $this->get_blocked_users_success($input_params);
                return $output_response;
            }

            else
            {
                $output_response = $this->get_blocked_users_no_data($input_params);
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
     * rules_get_blocked_users method is used to validate api input params.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_blocked_users($request_arr = array())
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

           $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_blocked_users");

           return $valid_res;
    }

 /**
     * get_blocked_users_list method is used to fetch tagged people list.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_blocked_users_list($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $arrResult = array();
                      
           
            $arrResult['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
           
            $this->block_result = $this->block_user_model->get_blocked_users($arrResult);

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
        $input_params["get_blocked_users"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }    

/**
     * is_get_user_list method is used to check conditions.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
 */
    public function is_get_user_list($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $cc_lo_0 = (is_array($input_params["block_id"])) ? count($input_params["block_id"]):$input_params["block_id"];
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
     * get_blocked_users_success method is used to process finish flow of fetch listing success.
     * @created Snehal Shinde | 12-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_blocked_users_success($input_params = array())
    {
            $setting_fields = array(
            "success" => "1",
            "message" => "get_blocked_users_success"
             );
      
        $output_fields = array(
            "block_id",
            "blocked_from_user_id",
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
            'get_blocked_users',
        );
        $ouput_aliases = array(
            "block_id" => "block_id",
            "blocked_from_user_id" => "blocked_from_user_id",
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

        $func_array["function"]["name"] = "get_blocked_users";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
/**
     * get_blocked_users_no_data method is used to process finish flow of get operation failed.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_blocked_users_no_data($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "get_blocked_users_no_data",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_blocked_users";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * block_user method is used to block user.
     * @created Snehal Shinde | 12-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function block_user($request_arr = array(), $inner_api = FALSE)
    {

        try
        {
            $validation_res = $this->rules_block_user($request_arr);

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

            $input_params = $this->block_user_exist($input_params);

            if ( true == empty($input_params["check_block_user_exist"][0]["block_id"]) || $input_params["block_user"]=='false')
            {
               // if blocked user is not available

                if(true == empty($input_params["check_block_user_exist"][0]["block_id"]) &&  $input_params["block_user"]=='false')
                {
                   
                  //  if blocked user not available
                    $output_response = $this->block_user_not_available($input_params);
                    return $output_response;
                }
                else
                {
                  $input_params = $this->update_block_user($input_params);
                 
                    if ($input_params["affected_rows"])
                    {
                       // print_r($input_params);exit;
                            $output_response = $this->block_user_success($input_params);
                            return $output_response;
                    }else{
                       // if database affected rows are not affected(failure in database operation)

                        $output_response = $this->block_user_failed($input_params);
                        return $output_response;
                    }

                }
                
            }
            else
            {
                // if blocked user is already available
                $output_response = $this->block_user_already_available($input_params);
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
     * rules_block_user method is used to validate api input params.
     * @created Snehal Shinde | 31-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
 */
    public function rules_block_user($request_arr = array())
    {

            $valid_arr = array(            
        
           "user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "user_id_number"
                    )
                ),
           "block_user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "block_user_id_number"
                    )
                ),
            "block_user" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "block_user_required"
                )
            )
            );

           $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "block_user");

           return $valid_res;
    }

      /**
     * block_user_exist method is used to check block user is exist or not.
     * @created Snehal Shinde  | 12-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function block_user_exist($input_params = array())
    {

        if (!method_exists($this, "check_block_user_exist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->check_block_user_exist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["check_block_user_exist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        // print_r($input_params);
        return $input_params;
    }

    /**
     * update_block_user  method is used to block user
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_block_user($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();

            $arrResult['user_id']  = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $arrResult['block_user_id']  = isset($input_params["block_user_id"]) ? $input_params["block_user_id"] : "";
            $arrResult['block_user']  = isset($input_params["block_user"]) ? $input_params["block_user"] : "";
            
            $this->block_result = $this->block_user_model->update_block_user($arrResult);
            
             if($input_params["block_user"]=="true")
            {
              //  if block any user then untag that people from tag list
              $untag_result = $this->block_user_model->untag_tagged_people($arrResult);
              //  if block any user then remove previous notifications.
              $notification_result = $this->block_user_model->remove_notifications($arrResult);
              // print_r($untag_result);exit;
            }

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
        $input_params["block_user"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }

/**
     * block_user_success method is used to process finish flow.
     * @created Snehal Shinde | 12-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
*/
    public function block_user_success($input_params = array())
    {
      if($input_params['database_operation']=='block')
      {
        $setting_fields = array(
            "success" => "1",
            "message" => "block_user_success"
        );
      }
      else
      {
        $setting_fields = array(
            "success" => "1",
            "message" => "unblock_user_success"
        );
      }
        
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

/**
     * block_user_already_available method is used to process finish flow.
     * @created Snehal Shinde | 12-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
*/
    public function block_user_already_available($input_params = array())
    {
      
        $setting_fields = array(
            "success" => "1",
            "message" => "block_user_already_available"
        );
      
        
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

/**
     * block_user_not_available method is used to process finish flow.
     * @created Snehal Shinde | 12-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
*/
    public function block_user_not_available($input_params = array())
    {
      
        $setting_fields = array(
            "success" => "1",
            "message" => "block_user_not_available"
        );
      
        
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

        /**
     * block_user_failed method is used to process finish flow of block user/unblock user failed.
     * @created Snehal Shinde | 31-03-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function block_user_failed($input_params = array())
    {
      if($input_params['database_operation']=='block')
      {
        $setting_fields = array(
            "success" => "0",
            "message" => "block_user_failed",
        );
      }
      else
      {
        $setting_fields = array(
            "success" => "0",
            "message" => "unblock_user_failed",
        );
      }

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }



}