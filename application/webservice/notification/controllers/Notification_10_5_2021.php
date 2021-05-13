<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Notification Controller
 *
 * @category webservice
 *
 * @package notification
 *
 * @subpackage controllers
 *
 * @module Notification 
 *
 * @class Notification.php
 *
 * @path application\webservice\notification\controllers\Notification.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 27.06.2019
 */

class Notification extends Cit_Controller
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
            "read_notifications",
        );
        $this->multiple_keys = array(
            "get_notification_details",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model("notification_model");
        $this->load->model("basic_appineers_master/missing_pet_model");
    }


    /**
     * start_notification method is used to initiate api execution flow.
     * @created Snehal Shinde | 01-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_notification($request_arr = array(), $inner_api = FALSE)
    {

    // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
          case 'GET':
                $output_response =  $this->get_notification($request_arr);
                return  $output_response;
                break;
          case 'POST':  
                // print_r($request_arr);exit;            
                $output_response =  $this->send_message($request_arr);
                return  $output_response;
             break;
          case 'DELETE':

            $output_response = $this->get_deleted_item($request_arr);
            return  $output_response;
             break;
        }
    }


    /**
     * rules_send_message method is used to validate api input params.
     * @created priyanka chillakuru | 09.05.2019
     * @modified Snehal Shinde | 01.04.2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_send_message($request_arr = array())
    {
        if(false == empty($request_arr['page_name']) && 'found_my_pet' == $request_arr['page_name'])
        {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ), 
            "missing_pet_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "missing_pet_id_required",
                )
            )
        );
    }

    if(false == empty($request_arr['page_name']) && 'notify_as_found_in_my_area' == $request_arr['page_name'])
        {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ), 
            "missing_pet_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "missing_pet_id_required",
                )
            ),
            "found_address" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "found_address_required",
                )
            ),
            "found_city" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "found_city_required",
                )
            ),
            "found_state" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "found_state_required",
                )
            ),
            "found_zip_code" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "found_zip_code_required",
                )
            )
        );
    }
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "send_message");

        return $valid_res;
    }

    /**
     * get_notified_user_list method is used to get notified user listing
     * @created Snehal Shinde | 05-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function get_notification($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_notification_list($request_arr);
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

            // $input_params = $this->get_notified_user_list_details($input_params);
            $input_params = $this->get_notification_details($input_params);

            $condition_res = $this->is_notify_found($input_params);
            if ($condition_res["success"])
            {

                $input_params = $this->get_image($input_params);

                //$input_params = $this->read_notifications($input_params);

                $output_response = $this->notification_finish_success_1($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->notification_finish_failure($input_params);
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
     * send_message method is used to initiate api execution flow.
     * @created priyanka chillakuru | 04.06.2019
     * @modified Snehal Shinde | 01.04.2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function send_message($request_arr = array(), $inner_api = FALSE)
    {

        try
        {
            $validation_res = $this->rules_send_message($request_arr);
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
            $input_params = $this->check_post_status($input_params);
            $block_status = $this->check_user_blocked($input_params);


          
            if ($input_params["checkPostStatus"]["status"]==1 && isset($block_status['checkBlockStatus']['missing_pet_id']))
            {
                // print_r('hi');exit;
               if(isset($input_params["page_code"]) && 'found_my_pet'==$input_params["page_code"])
                {

                   //  if notification id is presend then fetch notify user data and update into missing pet table else only update missing pet flag with found status.
                    if(isset($input_params["notification_id"])){

                        $notification_id = $input_params["notification_id"];

                        $get_notifier_details = $this->notification_model->get_notifier_details($notification_id);

                    if (isset($get_notifier_details["data"]) && !empty($get_notifier_details["data"])) {

                        $where_arr['missing_pet_id'] = $input_params["missing_pet_id"];
                        $param_arr = $get_notifier_details["data"][0];

                        $update_missing_pet_result = $this->notification_model->update_missing_pet($param_arr, $where_arr);

                        if ($update_missing_pet_result['data']) {
                            $input_params = $this->get_user_details_for_send_notifi($input_params);
                            $input_params = $this->custom_function($input_params);
                            $input_params = $this->start_loop($input_params);
                        }
                    } 

                    }
                    else{

                        $where_arr['missing_pet_id'] = $input_params["missing_pet_id"];
                        $params_arr["notify_datetime"] = date("Y-m-d h:i:s");
                        $update_missing_pet_result = $this->notification_model->update_missing_pet($params_arr, $where_arr);

                        if ($update_missing_pet_result['data']) {
                            $input_params = $this->get_user_details_for_send_notifi($input_params);
                            $input_params = $this->custom_function($input_params);
                            $input_params = $this->start_loop($input_params);
                        }

                    }
                   
                    
                }
                else
                {
                    $input_params = $this->get_user_details_for_send_notifi($input_params);
                    $input_params = $this->custom_function($input_params);

                    $input_params = $this->start_loop($input_params); 
                }
                if($input_params)
                {
                    // success message
                    $output_response = $this->messages_finish_success($input_params);
                   return $output_response;
                }
                else
                {
                    //  failed to send message
                      $output_response = $this->messages_finish_failure($input_params);
                       return $output_response; 
                }
               

                
            }
            else
                {

                   
                    if($block_status['checkBlockStatus']['status']==0)
                    {
                         //  if user is blocked
                        $output_response = $this->messages_blocked_finish_failure($input_params);
                    }
                    else if($input_params["checkPostStatus"]["status"]==0)
                    {
                        // if missing post is not available
                      $output_response = $this->no_missing_pet_post_available($input_params);
                    }else
                    {
                         //  failed to send message
                      $output_response = $this->messages_finish_failure($input_params);
                    }
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
     * get_user_details_for_send_notifi method is used to process query block.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_for_send_notifi($input_params = array())
    {

        $this->block_result = array();
        try
        {
            
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $missing_pet_id = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $receiver_user_id = isset($input_params["receiver_user_id"]) ? $input_params["receiver_user_id"] : "";
           $tagged_user_result = $this->notification_model->get_tagged_user_details_for_send_notifi($user_id, $missing_pet_id);

            if(isset($input_params["page_code"]) && 'found_my_pet'==$input_params["page_code"])
            {

                $this->block_result["data"]=$tagged_user_result['data']; 
            }
            else
            {
                 $pet_owner_result = $this->notification_model->get_user_details_for_send_notifi($user_id, $missing_pet_id); 
                 
               $this->block_result["data"]=array_merge($tagged_user_result['data'],$pet_owner_result['data']); 
            }

           
            // print_r($this->block_result['data']);exit;

            if (!$this->block_result['data'])
            {
                throw new Exception("No records found.");
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
     /**
     * custom_function method is used to process custom function.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "PrepareHelperMessage"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->PrepareHelperMessage($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }
    /**
     * post_notification method is used to process query block.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_notification($input_params = array())
    {
        // print_r($input_params);exit;
        
        $this->block_result = array();
        try
        {
            $params_arr = array();
            if (isset($input_params["page_code"]) && 'notify_as_found_in_my_area'==$input_params["page_code"])
            {
                $params_arr["_enotificationtype"] = "Notify pet owner for found pet in my area";
            }
            if (isset($input_params["page_code"]) && 'found_my_pet'==$input_params["page_code"])
            {
                 $params_arr["_enotificationtype"] = "Missing pet is found";
            }
            if (isset($input_params["notification_message"]))
            { 
                // print_r($input_params['s_users_id']);exit;

                if($input_params['dog_owner_id']==$input_params['s_users_id'])
                {
                                        
                    $notification_string=$input_params['notification_message'];
                    $dog_owner_name_string=ucfirst($input_params['dog_owner'])."`s";
                    $notification_message=str_replace($dog_owner_name_string," ",$notification_string);
                    $params_arr["notification_message"] = $notification_message;

                }
                else
                {
                    $params_arr["notification_message"] = $input_params["notification_message"];
                }

               
            }
            if (isset($input_params["r_user_id"]))
            {
                $params_arr["sender_id"] = $input_params["r_user_id"];
            }
            if (isset($input_params["s_users_id"]))
            {
                $params_arr["receiver_id"] = $input_params["s_users_id"];
            }
            if (isset($input_params["missing_pet_id"]))
            {
                $params_arr["missing_pet_id"] = $input_params["missing_pet_id"];
            }
            if (isset($input_params["pet_found_street_address"]))
            {
                $params_arr["pet_found_street_address"] = $input_params["pet_found_street_address"];
            }
            if (isset($input_params["pet_found_city"]))
            {
                $params_arr["pet_found_city"] = $input_params["pet_found_city"];
            }
            if (isset($input_params["pet_found_state"]))
            {
                $params_arr["pet_found_state"] = $input_params["pet_found_state"];
            }
            if (isset($input_params["pet_found_zipcode"]))
            {
                $params_arr["pet_found_zipcode"] = $input_params["pet_found_zipcode"];
            }
            if (isset($input_params["pet_found_date"]))
            {
                $params_arr["pet_found_date"] = $input_params["pet_found_date"];
            }
            if (isset($input_params["pet_found_latitude"]))
            {
                $params_arr["pet_found_latitude"] = $input_params["pet_found_latitude"];
            }
            if (isset($input_params["pet_found_longitude"]))
            {
                $params_arr["pet_found_longitude"] = $input_params["pet_found_longitude"];
            } 
             if (isset($input_params["unix_timestamp"])) {
                $params_arr["unix_timestamp"] = $input_params["unix_timestamp"];
            }   
            if (isset($input_params["missing_pet_found_user_id"]))
            {
                $params_arr["missing_pet_found_user_id"] = $input_params["missing_pet_found_user_id"];
                $params_arr["ePetStatus"] = "found";
            }

            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Active";

            if (isset($input_params["missing_pet_id"]))
            {
                $where_arr["missing_pet_id"] = $input_params["missing_pet_id"];
            }

             // print_r($params_arr);exit;
            $this->block_result = $this->notification_model->post_notification($params_arr);

                   }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_notification"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
/**
     * get_notification_details method is used to process query block.
     * @created Snehal Shinde | 05-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_notification_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            $this->block_result = $this->notification_model->get_notification_details($user_id, $input_params);
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
        $input_params["get_notification_details"] = $this->block_result["data"];

        return $input_params;
    }

    /**
     * is_notify_found method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_notify_found($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_notification_details"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
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
     * start_loop method is used to process loop flow.
     * @created Devangi Nirmal | 11.06.2019
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function start_loop($input_params = array())
    {
        $this->iterate_start_loop($input_params["get_user_details_for_send_notifi"], $input_params);
        return $input_params;
    }

    /**
     * get_image method is used to process query block.
     * @created Devangi Nirmal | 11.06.2019
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_image($input_params = array())
    {
        // print_r($input_params);exit;
        $this->block_result = array();
        try
        {
            $result_arr = $input_params["get_notification_details"];

            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {
                   
                    $data =array();
                    $data = $data_arr["sender_profile"];
                    $image_arr = array();
                    $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $p_key = ($data_arr["sender_id"] != "") ? $data_arr["sender_id"] : $input_params["sender_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] =$aws_folder_name. "/user_profile";
                    $data = $this->general->get_image_aws($image_arr);

                     $data_img = $data_arr["dog_image"];
                    $dogImage_arr["image_name"] = $data_img;
                    $dogImage_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $dog_p_key = ($data_arr["missing_pet_id"] != "") ? $data_arr["missing_pet_id"] : $input_params["missing_pet_id"];
                    $dogImage_arr["pk"] = $dog_p_key;
                    $dogImage_arr["color"] = "FFFFFF";
                    $dogImage_arr["no_img"] = FALSE;
                    $dogImage_arr["path"] = $aws_folder_name . "/missing_pet_image";
                    $data_img = $this->general->get_image_aws($dogImage_arr);

                    $result_arr[$data_key]["dog_image"] = $data_img;
                    $result_arr[$data_key]["sender_profile"] = $data;

                    $i++;
                }

                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        // print_r($this->block_result["data"]);exit;
        $input_params["get_notification_details"] = $this->block_result["data"];
        // $input_params = $this->wsresponse->assignSingleRecord($input_params["get_notification_details"],$input_params);
        $input_params = $this->wsresponse->assignSingleRecord($input_params["get_notification_details"],$input_params);
        return $input_params;
    }
       /**
     * rules_notification_list method is used to validate api input params.
     * @created priyanka chillakuru | 04.06.2019
     * @modified Snehal Shinde | 31.03.2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_notification_list($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_notification");

        return $valid_res;
    }

    /**
     * notification_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function notification_finish_success_1($input_params = array())
    {
        // print_r($input_params['page_code']);exit;
        if($input_params['page_code']=="notified_user_list")
        {
             $setting_fields = array(
                "success" => "1",
                "message" => "notify_finish_success_1",
             );
        }
        else
        {
             $setting_fields = array(
                "success" => "1",
                "message" => "notification_finish_success_1",
             );
        }
       
        $output_fields = array(
            'notification_id',
            'message',
            'notification_type',
            'sender_name',
            'sender_id',
            'missing_pet_id',
            'notify_datetime',
            'sender_profile',
            'dog_image',
            'pet_found_street',
            'pet_found_city',
            'pet_found_state',
            'pet_found_date',
            'pet_found_lattitude',
            'pet_found_longitude',
            'unix_timestamp',
            'dog_name',
            'sender_street_address',
            'sender_state',
            'sender_city',
            'sender_zip_code',
            'sender_lattitude',
            'sender_longitude',
            'sender_email',
            'sender_phone'
        );
        $output_keys = array(
            'get_notification_details',
        );

        // $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_notification";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * notification_finish_failure method is used to process finish flow.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function notification_finish_failure($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "notification_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_notification";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * iterate_start_loop method is used to iterate loop.
     * @created Devangi Nirmal | 11.06.2019
     * @modified Snehal Shinde | 01.04.2021
     * @param array $get_notification_details_lp_arr get_notification_details_lp_arr array to iterate loop.
     * @param array $input_params_addr $input_params_addr array to address original input params.
     */
    public function iterate_start_loop(&$get_notification_details_lp_arr = array(), &$input_params_addr = array())
    {

        $input_params_loc = $input_params_addr;
        $_loop_params_loc = $get_notification_details_lp_arr;
        $_lp_ini = 0;
        $_lp_end = count($_loop_params_loc);
        for ($i = $_lp_ini; $i < $_lp_end; $i += 1)
        {
            $get_notification_details_lp_pms = $input_params_loc;

            unset($get_notification_details_lp_pms["get_user_details_for_send_notifi"]);
            if (is_array($_loop_params_loc[$i]))
            {
                $get_notification_details_lp_pms = $_loop_params_loc[$i]+$input_params_loc;
            }
            else
            {
                $get_notification_details_lp_pms["get_user_details_for_send_notifi"] = $_loop_params_loc[$i];
                $_loop_params_loc[$i] = array();
                $_loop_params_loc[$i]["get_user_details_for_send_notifi"] = $get_notification_details_lp_pms["get_user_details_for_send_notifi"];
            }

            $get_notification_details_lp_pms["i"] = $i;
            $input_params = $get_notification_details_lp_pms;
            //print_r($input_params); exit;

            // $input_params = $this->get_image($input_params);

            $condition_res = $this->check_receiver_device_token($input_params);

            $input_params = $this->post_notification($input_params);
            $input_params = $this->push_notification($input_params);


            $get_notification_details_lp_arr[$i] = $this->wsresponse->filterLoopParams($input_params, $_loop_params_loc[$i], $get_notification_details_lp_pms);
        }
    }

    /**
     * check_receiver_device_token method is used to process conditions.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receiver_device_token($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["r_device_token"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $cc_lo_1 = $input_params["r_notification"];
            $cc_ro_1 = "Yes";

            $cc_fr_1 = ($cc_lo_1 == $cc_ro_1) ? TRUE : FALSE;
            if (!$cc_fr_1)
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
     * push_notification method is used to process mobile push notification.
     * @created CIT Dev Team
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {
// print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            if($input_params['dog_owner_id']==$input_params['s_users_id'])
                {
                    $notification_string=$input_params['notification_message'];
                    $dog_owner_name_string=ucfirst($input_params['dog_owner'])."`s";
                    $notification_message=str_replace($dog_owner_name_string," ",$notification_string);
                    $params_arr["notification_message"] = $notification_message;
                    
                }
                else
                {
                    $params_arr["notification_message"] = $input_params["notification_message"];
                }


            $device_id = $input_params["s_device_token"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "type",
                    "value" => "Message",
                    "send" => "Yes",
                ),
                array(
                    "key" => "receiver_id",
                    "value" => $input_params["s_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_id",
                    "value" => $input_params["r_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_name",
                    "value" => $input_params["s_name"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_profile",
                    "value" => $input_params["s_profile_image"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_image",
                    "value" => $input_params["ui_image"],
                    "send" => "Yes",
                )
            );
            $push_msg = "".$params_arr["notification_message"]."";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "runtime";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            $uni_id = $this->general->insertPushNotification($send_arr);
            if (!$uni_id)
            {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = 1;
            $message = "Push notification send succesfully.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification"] = $this->block_result["success"];

        return $input_params;
    }
     /**
     * check_post_status method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_post_status($input_params = array())
    {
        if (!method_exists($this, "checkPostStatus"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkPostStatus($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkPostStatus"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }  

    /**
     * check_user_blocked method is used to process custom function.
     * @created Snehal Shinde | 20.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_user_blocked($input_params = array())
    {

        if (!method_exists($this, "checkBlockStatus"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkBlockStatus($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkBlockStatus"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * messages_finish_failure method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_failure($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "messages_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
  /**
     * messages_blocked_finish_failure method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_blocked_finish_failure($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_blocked_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * messages_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success($input_params = array())
    {
        if((isset($input_params["page_code"]) && 'found_my_pet'==$input_params["page_code"]))
        {
            $setting_fields = array(
                "success" => "1",
                "message" => "messages_found_finish_success",
           );
        }
        else
        {
            $setting_fields = array(
                "success" => "1",
                "message" => "messages_finish_success",
            );
        }

        
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    } 

    /**
     * no_missing_pet_post_available method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function no_missing_pet_post_available($input_params = array())
    {
        
            $setting_fields = array(
                "success" => "1",
                "message" => "no_missing_pet_post_available",
            );

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }



}
