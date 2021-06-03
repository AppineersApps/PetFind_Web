<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send Message Controller
 *
 * @category webservice
 *
 * @package chat
 *
 * @subpackage controllers
 *
 * @module Send Message
 *
 * @class Send_message.php
 *
 * @path application\webservice\chat\controllers\Send_message.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Send_message extends Cit_Controller
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
            "if_blocked",
            "check_chat_intiated_or_not",
            "update_message",
            "get_user_details_for_send_notifi",
            "post_notification",
            "get_sender_image",
            "add_message",
        );
        $this->multiple_keys = array(
            "custom_function",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('send_message_model');
        $this->load->model("block_user/block_user_model");
        $this->load->model("notification/notification_model");
        $this->load->model("basic_appineers_master/users_model");
    }


    /**
     * start_send_message method is used to initiate api execution flow.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_send_message($request_arr = array(), $inner_api = FALSE)
    {
        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
            case 'GET':
                $output_response =  $this->get_message_list($request_arr);
                return  $output_response;
                break;

            case 'POST':
                //  print_r($request_arr);exit; 
                $output_response =  $this->send_message($request_arr);
                return  $output_response;
                break;

            case 'DELETE':

                $output_response =  $this->delete_message($request_arr);
                return  $output_response;
                break;
        }
    }

    /**
     * send_message method is used to send message
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function send_message($request_arr)
    {

        try {
            $validation_res = $this->rules_send_message($request_arr);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->if_blocked($input_params);


            $condition_res = $this->is_blocked($input_params);
            //  if user is not blocked then call this if 
            if ($condition_res["success"] && isset($input_params['receiver_id'])) {

                $input_params = $this->check_chat_intiated_or_not($input_params);

                $condition_res = $this->is_intiated($input_params);



                // if chat initiated previuosly then call if condition
                if ($condition_res["success"]) {

                    $input_params = $this->update_message($input_params);
                } else {

                    $input_params = $this->add_message($input_params);

                    //    print_r($input_params);exit; 

                }
                $input_params = $this->get_user_details_for_send_notifi($input_params);

                $input_params = $this->custom_function($input_params);

                $input_params = $this->post_notification($input_params);
                $input_params = $this->push_notification($input_params);


                $output_response = $this->messages_finish_success_1($input_params);
                return $output_response;
            } else {
                //  if user is blocked then called this else
                $output_response = $this->blocked_user_finish_success($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }


    /**
     * rules_send_message method is used to validate api input params.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_send_message($request_arr = array())
    {

        if ($request_arr["message_id"] != "") {
            if ($request_arr["message_status"] == "accept") {
                $valid_arr = array(
                    "message_status" => array(
                        array(
                            "rule" => "required",
                            "value" => TRUE,
                            "message" => "message_status_required",
                        )
                    ),
                    "message_id" => array(
                        array(
                            "rule" => "required",
                            "value" => TRUE,
                            "message" => "message_id_required",
                        )
                    )
                    // "firebase_id" => array(
                    //     array(
                    //         "rule" => "required",
                    //         "value" => TRUE,
                    //         "message" => "firebase_id_required",
                    //     )
                    //     )
                );
            } else {
                $valid_arr = array(
                    "message_status" => array(
                        array(
                            "rule" => "required",
                            "value" => TRUE,
                            "message" => "message_status_required",
                        )
                    ),
                    "message_id" => array(
                        array(
                            "rule" => "required",
                            "value" => TRUE,
                            "message" => "message_id_required",
                        )
                    )
                );
            }
        } else {
            $valid_arr = array(
                "user_id" => array(
                    array(
                        "rule" => "required",
                        "value" => TRUE,
                        "message" => "user_id_required",
                    )
                ),
                "receiver_id" => array(
                    array(
                        "rule" => "required",
                        "value" => TRUE,
                        "message" => "receiver_id_required",
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


        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "send_message");

        return $valid_res;
    }


    /**
     * send_message method is used to send message
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function delete_message($request_arr)
    {

        try {
            $validation_res = $this->rules_delete_message($request_arr);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();
            
            $input_params = $this->check_valid_message($input_params);
            
            $condition_res = $this->is_message_id($input_params);
           
            // if message id is present then call if condition
            if ($condition_res["success"]) {
                
                $input_params = $this->delete_chat_message($input_params);
               
                if($input_params["affected_rows"])
                {
                    $output_response = $this->messages_delete_finish_success($input_params);
                    return $output_response;
                }
                else{
                    $output_response = $this->messages_delete_finish_success_1($input_params);
                    return $output_response;
                }
                            
            } else {

                $output_response = $this->invalid_message_id_success($input_params);

                return $output_response;

            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }


    /**
     * rules_send_message method is used to validate api input params.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_delete_message($request_arr = array())
    {
            $valid_arr = array(
                "user_id" => array(
                    array(
                        "rule" => "required",
                        "value" => TRUE,
                        "message" => "user_id_required",
                    )
                ),
                "message_id" => array(
                    array(
                        "rule" => "required",
                        "value" => TRUE,
                        "message" => "message_id_required",
                    )
                )
            );
      
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "delete_message");

        return $valid_res;
    }




    public function get_message_list($request_arr = array())
    {


        try {
            $validation_res = $this->rules_get_message_list($request_arr);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->get_message($input_params);

            $condition_res = $this->check_for_message($input_params);
            // print_r($input_params);exit;
            if ($condition_res["success"] && $input_params["page_code"] != "" && count($input_params['get_message']) > 0) {
                // print_r('gg');exit;
                $input_params = $this->start_loop($input_params);
                // print_r($input_params);exit;
                $output_response = $this->messages_list_finish_success_1($input_params);
                return $output_response;
            } else {

                $output_response = $this->messages_list_finish_success($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * rules_get_message_list method is used to validate api input params.
     * @created priyanka chillakuru | 09.05.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_message_list($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
            "page_code" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "page_code_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_message_list");

        return $valid_res;
    }

    /**
     * start_loop method is used to process loop flow.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function start_loop($input_params = array())
    {
        $this->iterate_start_loop($input_params["get_message"], $input_params);
        return $input_params;
    }

    /**
     * iterate_start_loop method is used to iterate loop.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $get_message_lp_arr get_message_lp_arr array to iterate loop.
     * @param array $input_params_addr $input_params_addr array to address original input params.
     */
    public function iterate_start_loop(&$get_message_lp_arr = array(), &$input_params_addr = array())
    {

        $input_params_loc = $input_params_addr;
        $_loop_params_loc = $get_message_lp_arr;
        $_lp_ini = 0;
        $_lp_end = count($_loop_params_loc);
        for ($i = $_lp_ini; $i < $_lp_end; $i += 1) {
            $get_message_lp_pms = $input_params_loc;

            unset($get_message_lp_pms["get_message"]);
            if (is_array($_loop_params_loc[$i])) {
                $get_message_lp_pms = $_loop_params_loc[$i] + $input_params_loc;
            } else {
                $get_message_lp_pms["get_message"] = $_loop_params_loc[$i];
                $_loop_params_loc[$i] = array();
                $_loop_params_loc[$i]["get_message"] = $get_message_lp_pms["get_message"];
            }

            $get_message_lp_pms["i"] = $i;
            $input_params = $get_message_lp_pms;

            $input_params = $this->get_send_image($input_params);

            $input_params = $this->get_receiver_images($input_params);

            $input_params = $this->custom_function_1($input_params);

            $get_message_lp_arr[$i] = $this->wsresponse->filterLoopParams($input_params, $_loop_params_loc[$i], $get_message_lp_pms);
        }
    }
    /**
     * get_send_image method is used to process query block.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_send_image($input_params = array())
    {

        $this->block_result = array();
        try {

            $sender_id = isset($input_params["sender_id"]) ? $input_params["sender_id"] : "";
            $this->block_result = $this->send_message_model->get_send_image($sender_id);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            // print_r($result_arr);exit;
            if (is_array($result_arr) && count($result_arr) > 0) {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr) {

                    $data = $data_arr["u_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $p_key = ($data_arr["u_users_id"] != "") ? $data_arr["u_users_id"] : $data_arr["u_users_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["path"] = "pet_find/user_profile";
                    // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["u_image"] = $data;

                    $i++;
                }

                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_send_image"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_receiver_images method is used to process query block.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_receiver_images($input_params = array())
    {

        $this->block_result = array();
        try {

            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $this->block_result = $this->send_message_model->get_receiver_images($receiver_id);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

            if (is_array($result_arr) && count($result_arr) > 0) {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr) {

                    $data = $data_arr["ui_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $p_key = ($data_arr["ui_users_id"] != "") ? $data_arr["ui_users_id"] : $data_arr["ui_users_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["path"] = "pet_find/user_profile";
                    // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["ui_image"] = $data;

                    $i++;
                }
                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_receiver_images"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * custom_function_1 method is used to process custom function.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function_1($input_params = array())
    {
        if (!method_exists($this, "format_images")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->format_images($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function_1"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }


    /**
     * get_message method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_message($input_params = array())
    {

        $this->block_result = array();
        try {
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $page_code = isset($input_params["page_code"]) ? $input_params["page_code"] : "";

            $this->block_result = $this->send_message_model->get_message($user_id, $page_code);
            //    print_r($this->block_result);exit;
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result['data'];
            if (is_array($result_arr) && count($result_arr) > 0) {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr) {


                    if ($data_arr["sender_id"] == $user_id) {
                        $data_arr["sender_id"] = $data_arr["receiver_id"];
                    } else {
                        $data_arr["sender_id"] = $data_arr["sender_id"];
                    }
                    $arrBlockStatus = $this->get_users_block_details($user_id, $data_arr["sender_id"], $data_arr["receiver_id"]);
                    $result_arr[$data_key]["block_status"] =  $arrBlockStatus['0']['block_status'];
                    $i++;
                }
                //   print_r($result_arr);exit;
                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        //print_r($input_params);exit;
        return $input_params;
    }

    /**
     * get_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_users_block_details($user_id = '', $connection_id = '', $other_user_id = '')
    {

        $this->block_result = array();
        try {

            $this->block_result = $this->send_message_model->get_users_block_details($user_id, $connection_id, $other_user_id);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        return $result_arr;
    }



    /**
     * check_for_message method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_message($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["get_message"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Not getting messages list.");
            }
            $success = 1;
            $message = "Fetched message list.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }



    /**
     * if_blocked method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function if_blocked($input_params = array())
    {

        $this->block_result = array();
        try {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            if ($input_params["receiver_id"] != "") {
                $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            } else {
                $message_id = $input_params["message_id"];

                $receiver_details = $this->send_message_model->get_user_id($message_id, $user_id);

                if (count($receiver_details) > 0) {

                    $receiver_id = $receiver_details[0]['s_users_id'];
                    $input_params["receiver_id"] = $receiver_details[0]['s_users_id'];
                    $input_params["missing_pet_id"] = $receiver_details[0]['missing_pet_id'];
                } else {
                    throw new Exception("Failed to fetch receiver details.");
                }
            }

            if ($receiver_id != '') {

                $this->block_result = $this->block_user_model->if_blocked($user_id, $receiver_id);
            } else {

                throw new Exception("Failed to fetch receiver details.");
            }

            // print_r($this->block_result);exit; 
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["if_blocked"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_blocked method is used to process conditions.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_blocked($input_params = array())
    {
        //print_r($input_params);exit;
        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["if_blocked"]) ? 0 : 1);
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * check_chat_intiated_or_not method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_chat_intiated_or_not($input_params = array())
    {
        //print_r($input_params);exit;
        $this->block_result = array();
        try {
            if (isset($input_params["user_id"])) {
                $param_array["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["receiver_id"])) {
                $param_array["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["firebase_id"])) {
                $param_array["firebase_id"] = $input_params["firebase_id"];
            }
            if (isset($input_params["missing_pet_id"])) {
                $param_array["missing_pet_id"] = $input_params["missing_pet_id"];
            }
            if (isset($input_params["message_id"])) {
                $param_array["message_id"] = $input_params["message_id"];
            }
            // $param_array['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            // $param_array['receiver_id'] = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            // $param_array['firebase_id'] = isset($input_params["firebase_id"]) ? $input_params["firebase_id"] : "";
            // $param_array['missing_pet_id'] = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            // $param_array['message_id'] = isset($input_params["message_id"]) ? $input_params["message_id"] : "";

            $this->block_result = $this->send_message_model->check_chat_intiated_or_not($param_array);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["check_chat_intiated_or_not"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_intiated method is used to process conditions.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_intiated($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["check_chat_intiated_or_not"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Chat is not initiated yet.");
            }
            $success = 1;
            $message = "Chat initiated";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    
     /**
     * This method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_valid_message($input_params = array())
    {
        //print_r($input_params);exit;
        $this->block_result = array();
        try {
            if (isset($input_params["user_id"])) {
                $param_array["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["message_id"])) {
                $param_array["message_id"] = $input_params["message_id"];
            }

            $this->block_result = $this->send_message_model->check_valid_message($param_array);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["check_valid_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_intiated method is used to process conditions.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_message_id($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["check_valid_message"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Message Id is not available");
            }
            $success = 1;
            $message = "Message Id is present";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }   

    /**
     * update_message method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_message($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["message_id"])) {
                $where_arr["message_id"] = $input_params["message_id"];
            }

            if (isset($input_params["firebase_id"])) {
                $params_arr["firebase_id"] = $input_params["firebase_id"];
            }
            if (isset($input_params["message_status"])) {
                $params_arr["message_status"] = $input_params["message_status"];
            }
            if (isset($input_params["receiver_id"])) {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            $params_arr["_dtmodifieddate"] = "NOW()";
            $params_arr["is_requested"] = "1";

            $this->block_result = $this->send_message_model->update_message($params_arr, $where_arr);
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
    
        /**
     * This method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_chat_message($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr =  array();
            if (isset($input_params["message_id"])) {
                $params_arr["message_id"] = $input_params["message_id"];
            }
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            $this->block_result = $this->send_message_model->delete_message($params_arr);
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * add_message method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function add_message($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = array();
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["receiver_id"])) {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["missing_pet_id"])) {
                $params_arr["missing_pet_id"] = $input_params["missing_pet_id"];
            }
            if (isset($input_params["firebase_id"])) {
                $params_arr["firebase_id"] = $input_params["firebase_id"];
            }

            $params_arr["_dtaddeddate"] = "NOW()";
            $params_arr["eMessageStatus"] = "Pending";
            $params_arr["_dtmodifieddate"] = "NOW()";
            //  print_r($params_arr);exit;
            $this->block_result = $this->send_message_model->add_message($params_arr);
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["add_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_user_details_for_send_notifi method is used to process query block.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_for_send_notifi($input_params = array())
    {

        $this->block_result = array();
        try {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $this->block_result = $this->send_message_model->get_user_details_for_send_notifi($user_id, $receiver_id);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * custom_function method is used to process custom function.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "PrepareHelperMessage")) {
            $result_arr["data"] = array();
        } else {
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
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_notification($input_params = array())
    {


        $this->block_result = array();
        try {

            $params_arr = array();
            if (isset($input_params["notification_message"])) {
                $params_arr["notification_message"] = $input_params["notification_message"];
            }
            if (isset($input_params["receiver_id"])) {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["notification_type"])) {
                $params_arr["_enotificationtype"] =  $input_params["notification_type"];
            }

            $params_arr["eNotifyType"] = "User";
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Unread";
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["missing_pet_id"])) {
                $params_arr["missing_pet_id"] = $input_params["missing_pet_id"];
            }

            //check if same notification exists:
            $params_arr["check_notification_exists"] = $this->check_notification_exists($params_arr);

            $this->block_result = $this->send_message_model->post_notification($params_arr);
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_notification"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * check_notification_exists method is used to check notification is already exist for that paricular user and post.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */

    public function check_notification_exists($params_arr = array())
    {
        if (!method_exists($this, "checkNotificationExists")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkNotificationExists($params_arr);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checknotificationexists"] = $format_arr;

        $params_arr = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $params_arr;
    }

    /**
     * check_receiver_device_token method is used to process conditions.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receiver_device_token($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["r_device_token"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }

            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * push_notification method is used to process mobile push notification.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {

        // print_r($input_params);exit;

        $this->block_result = array();
        try {

            $device_id = $input_params["r_device_token"];
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
                    "value" => $input_params["r_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_id",
                    "value" => $input_params["s_users_id"],
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
                ),
                array(
                    "key" => "missing_pet_id",
                    "value" => $input_params["missing_pet_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "notify_type",
                    "value" => 'User',
                    "send" => "Yes",
                ),
                array(
                    "key" => "dog_name",
                    "value" => $input_params["dog_name"],
                    "send" => "Yes",
                )
            );
            $push_msg = "" . $input_params["notification_message"] . "";
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

            if (!$uni_id) {
                $success = 0;
                $message = "Failure in Push notification.";
                throw new Exception('Failure in insertion of push notification batch entry.');
            } else {
                $success = 1;
                $message = "Push notification send succesfully.";
            }
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * messages_finish_success_1 method is used to process finish flow.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success_1",
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
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success",
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
     * blocked_user_finish_success method is used to process finish flow.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "3",
            "message" => "blocked_user_finish_success",
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
     * messages_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_list_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success_1",
        );
        $output_fields = array(
            'sender_id',
            'receiver_id',
            'firebase_id',
            'missing_pet_id',
            'message_status',
            'pet_status',
            'block_status',
            'dog_name',
            'sender_name',
            'message_id',
            'receiver_name',
            'added_at',
            'updated_at',
            'sender_image',
            'receiver_image'
        );
        $output_keys = array(
            'get_message',
        );


        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_message_list";
        $func_array["function"]["output_keys"] = $output_keys;
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
    public function messages_list_finish_success($input_params = array())
    {

        if (count($input_params['get_message']) == 0) {

            $setting_fields = array(
                "success" => "1",
                "message" => "empty_messages_finish_success_2",
            );
        } else {
            $setting_fields = array(
                "success" => "0",
                "message" => "messages_finish_success_2",
            );
        }

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_message_list";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

        /**
     * This method is used to process finish flow.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_delete_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_delete_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

/**
     * This method is used to process finish flow.
     * @created Suresh Nakate
     * @modified Snehal Shinde | 14-04-2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function invalid_message_id_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "invalid_message_id_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
      /**
     * This method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_delete_finish_success_1($input_params = array())
    {

            $setting_fields = array(
                "success" => "0",
                "message" => "messages_delete_finish_success_1",
            );
       
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_message";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


}
