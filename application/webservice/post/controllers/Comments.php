<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Missing Pet Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 * 
 * @subpackage controllers
 *
 * @module Missing pet
 *
 * @class Missing_pet.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Missing_pet.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 
 */

class Comments extends Cit_Controller
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
            "get_posted_by_user_details",
            "insert_comment",
            "get_comments",
            "get_commented_user_details",
            "insert_comments_v1",
            "insert_notification",
            "get_comments_v1",
            "get_all_comments",
            "custom_function",
        );
        $this->multiple_keys = array(
            "prepare_message",
        );
        $this->block_result = array();

         $this->load->library('wsresponse');
         $this->load->model("post/comments_model");

    }


    /**
     * start_missing_pet method is used to initiate api execution flow.
     * @created Snehal Shinde | 01.03.2021
     * @modified Snehal Shinde | 01.03.2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_comments($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
          case 'GET':

               $output_response =  $this->get_comment_list($request_arr);
               return  $output_response;
               break;

          case 'POST':

                $output_response =  $this->post_comment($request_arr);
                return  $output_response;
                 break;

          case 'DELETE':

            $output_response = $this->delete_comment($request_arr);
            return  $output_response;
             break;
        }
    }

    /**
     * post_comment method is used to initiate api execution flow.
     * @created Snehal Shinde | 13.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function post_comment($request_arr = array(), $inner_api = FALSE)
    {
        // print_r($request_arr);exit;
        try
        {
            $validation_res = $this->rules_comment_a_post($request_arr);
            //print_r($validation_res);exit();
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
            //echo $input_params;exit();
            $output_array = $func_array = array();

            $input_params = $this->get_posted_by_user_details($input_params);
            //print_r($input_params);exit();

            $condition_res = $this->if_same_user($input_params);
            //print_r($condition_res);exit();
            if ($condition_res["success"])
            {

                $input_params = $this->insert_comment($input_params);
                //print_r($input_params);exit();

                $condition_res = $this->check_if_inserted($input_params);
                //print_r($condition_res);exit();
                if ($condition_res["success"])
                {

                    $input_params = $this->get_comments($input_params);
                    //print_r($input_params);exit(); 
                    $output_response = $this->comments_finish_success_1($input_params);
                    return $output_response;
                }

                else
                {

                    $output_response = $this->comments_finish_success($input_params);
                    return $output_response;
                }
            }

            else
            {

                $input_params = $this->get_commented_user_details($input_params);

                $input_params = $this->prepare_message($input_params);
                    
                $input_params = $this->insert_comments_v1($input_params);

                $input_params = $this->insert_notification($input_params);


                $input_params = $this->get_comments_v1($input_params);


                $condition_res = $this->condition($input_params);
                if ($condition_res["success"])
                {

                    $input_params = $this->push_notification($input_params);
                    
        
                    $output_response = $this->posts_finish_success_1($input_params);
                    return $output_response;
                }

                else
                {

                    $output_response = $this->posts_finish_success($input_params);
                    return $output_response;
                }
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * rules_comment_a_post method is used to validate api input params.
     * @created Snehal Shinde | 15-03-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_comment_a_post($request_arr = array())
    {
        $valid_arr = array(
            "missing_pets_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "missing_pets_id_required",
                )
            ),
            "comments" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "comments_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "comment_a_post");

        return $valid_res;
    }

        /**
     * rules_comment_listing method is used to validate api input params.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 20.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_comment_listing($request_arr = array())
    {
        $valid_arr = array();
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "comment_listing");

        return $valid_res;
    }

/**
     * get_posted_by_user_details method is used to process query block.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_posted_by_user_details($input_params = array())
    {
        //print_r($input_params);exit();

        $this->block_result = array();
        try
        {

            $insert_id = isset($input_params["insert_id"]) ? $input_params["insert_id"] : "";
            $this->block_result = $this->comments_model->get_posted_by_user_details($insert_id);
            //echo $this->block_result;exit();
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_posted_by_user_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * if_same_user method is used to process conditions.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function if_same_user($input_params = array())
    {
        //print_r($input_params);exit();
        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["p_user_id"];
            $cc_ro_0 = $input_params["user_id"];

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
     * insert_comment method is used to process query block.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function insert_comment($input_params = array())
    {
        //print_r($input_params);exit();
        $this->block_result = array();
        try
        {

            $params_arr = array();

            if (isset($input_params["missing_pets_id"]))
            {
                $params_arr["missing_pets_id"] = $input_params["missing_pets_id"];
            }
            if (isset($input_params["comments"]))
            {
                $params_arr["comments"] = $input_params["comments"];
            }
            if (isset($input_params["user_id"]))
            {
                $params_arr["comments_from"] = $input_params["user_id"];
            }
            $params_arr["_dtaddedat"] = "NOW()";
            //$params_arr["_estatus"] = "Active";
            $this->block_result = $this->comments_model->insert_comment($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["insert_comment"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * check_if_inserted method is used to process conditions.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_if_inserted($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["insert_comment"];
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
     * get_comments method is used to process query block.
     * @created Chetan Dvs | 17.09.2019
     * @modified Chetan Dvs | 20.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_comments($input_params = array())
    {
        //print_r($input_params);exit();
        $this->block_result = array();
        try
        {

            $insert_id = isset($input_params["insert_id"]) ? $input_params["insert_id"] : "";
            
            $this->block_result = $this->comments_model->get_comments($insert_id);
            
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_comments"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * comments_finish_success_1 method is used to process finish flow.
     * @created Chetan Dvs | 13.09.2019
     * @modified saikrishna bellamkonda | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function comments_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "comments_finish_success_1",
        );
        $output_fields = array(
            'c_missing_pets_id',
            'c_comment',
            'c_added_at',
            'c_comment_id',
            'c_comment_from'
            
        );
        $output_keys = array(
            'get_comments',
        );
        $output_aliases = array(
            //"get_comments" => "get_all_comments",
            "c_missing_pets_id"=>"missing_pets_id",
            "c_comment" => "comments",
            "c_added_at" => "added_at",
            "c_comment_id"=>"comment_id",
            "c_comment_from"=>"comment_from"
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "comment_a_post";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * comments_finish_success method is used to process finish flow.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function comments_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "comments_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "comment_a_post";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    
    /**
     * prepare_message method is used to process custom function.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function prepare_message($input_params = array())
    {
        if (!method_exists($this, "prepareNotificationMessage"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->prepareNotificationMessage($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["prepare_message"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    

    /**
     * condition method is used to process conditions.
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["u_device_token"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $cc_lo_1 = $input_params["u_is_notifications_enable"];
            $cc_ro_1 = 1;

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
     * @created Chetan Dvs | 13.09.2019
     * @modified Chetan Dvs | 24.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {
    $this->block_result = array();
        try
        {

            $device_id = $input_params["u_device_token"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "message",
                    "value" => $input_params["message"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "type",
                    "value" => "comments",
                    "send" => "Yes",
                ),
                array(
                    "key" => "post_id",
                    "value" => $input_params["post_id"],
                    "send" => "Yes",
                )
            );
            $push_msg = "#message# ";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "default";

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
     * get_comment_list method is used to initiate api execution flow.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 20.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function get_comment_list($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_comment_listing($request_arr);
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

            $input_params = $this->get_all_comments($input_params);

            $condition_res = $this->if_comments_available($input_params);
            if ($condition_res["success"])
            {

                $input_params = $this->custom_function($input_params);

                $output_response = $this->comments_list_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->comments_list_finish_success($input_params);
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
     * get_all_comments method is used to process query block.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 19.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_comments($input_params = array())
    {
        // print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            $perpage_record = isset($input_params["perpage_record"]) ? $input_params["perpage_record"] : "";
            $missing_pet_id = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $comment_id = isset($input_params["comment_id"]) ? $input_params["comment_id"] : "";
            $page_index = isset($input_params["page_index"]) ? $input_params["page_index"] : 1;
            $this->block_result = $this->comments_model->get_all_comments($perpage_record, $comment_id, $page_index, $this->settings_params,$missing_pet_id);


            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {
                    $data = $data_arr["c_added_at"];
                    if (method_exists($this->general, "prepareDateFormat"))
                    {
                        $data = $this->general->prepareDateFormat($data, $result_arr[$data_key], $i, $input_params);
                    }
                    $result_arr[$data_key]["c_added_at"] = $data;

                    $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                    $data = $data_arr["user_profile_image"];
                    $image_arr = array();
                    $p_key = ($data_arr["comment_user_id"] != "") ? $data_arr["comment_user_id"] : $data_arr["comment_user_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] =$aws_folder_name. "/user_profile";
                    $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["user_profile_image"] = $data;

                    $i++;
                }

                // print_r($result_arr);exit;
                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_all_comments"] = $this->block_result["data"];

        return $input_params;
    }

    /**
     * if_comments_available method is used to process conditions.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 16.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function if_comments_available($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_all_comments"]) ? 0 : 1);
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
     * custom_function method is used to process custom function.
     * @created Chetan Dvs | 19.09.2019
     * @modified Chetan Dvs | 19.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "formatOrderOfData"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->formatOrderOfData($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }




    /**
     * comments_finish_success method is used to process finish flow.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 19.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function comments_list_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "comments_finish_success",
        );
        $output_fields = array(
            'c_comment',
            'c_added_at',
            'c_missing_pets_id',
            'user_name',
            'user_profile_image',
            'c_comment_id',
        );
        $output_keys = array(
            'get_all_comments',
        );
        $ouput_aliases = array(
            "c_comment" => "comments",
            "c_added_at" => "added_at",
            "c_missing_pets_id" => "missing_pets_id",
            "user_name" => "user_name",
            "user_profile_image" => "user_profile_image",
            "c_comment_id" => "comment_id",
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "comment_listing";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }



    /**
     * comments_list_finish_success_1 method is used to process finish flow.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function comments_list_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "comments_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "comment_listing";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }



}
