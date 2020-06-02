<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Post a Feedback Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Set store review
 *
 * @class set_store_review.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Set_store_review.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Set_store_review extends Cit_Controller
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
            "set_store_review",
            "get_review_details",
        );
        $this->multiple_keys = array(
            "custom_function",
            "review_images",
            "formatting_images",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('set_store_review_model');
        $this->load->model("basic_appineers_master/user_store_review_model");
        $this->load->model("basic_appineers_master/user_review_images_model");
    }

    /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_set_store_review($request_arr = array())
    {
        $valid_arr = array(
            "ice_type_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "ice_type_required",
                )
            ),
            "ice_quantity_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "ice_quantity_required",
                )
            ),
            "store_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "store_id_required",
                )
            ),
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
             "images_count" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "images_count_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "set_store_review");

        return $valid_res;
    }

    /**
     * start_set_store_review method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_set_store_review($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_set_store_review($request_arr);
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

            $input_params = $this->set_store_review($input_params);

            $condition_res = $this->is_posted($input_params);

            if ($condition_res["success"])
            {

                $input_params = $this->custom_function($input_params);

                $input_params = $this->get_review_details($input_params);

                $input_params = $this->review_images($input_params);

                $input_params = $this->formatting_images($input_params);

                $output_response = $this->user_review_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->user_review_finish_success_1($input_params);
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
     * set_store_review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function set_store_review($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = array();
            $params_arr["_dtaddedat"] = $input_params["timestamp"];
            $params_arr["_estatus"] = "Active";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["comment"]))
            {
                $params_arr["comment"] = $input_params["comment"];
            }
            
            if (isset($input_params["ice_type_id"]))
            {
                $params_arr["ice_type_id"] = $input_params["ice_type_id"];
            }
            if (isset($input_params["ice_quantity_id"]))
            {
                $params_arr["ice_quantity_id"] = $input_params["ice_quantity_id"];
            }
            if (isset($input_params["store_id"]))
            {
                $params_arr["store_id"] = $input_params["store_id"];
            }
            $this->block_result = $this->user_store_review_model->set_store_review($params_arr);

        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["set_store_review"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * is_posted method is used to process conditions.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_posted($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $cc_lo_0 = $input_params["review_id"];
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
     * custom_function method is used to process custom function.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "uploadReviewImages"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->uploadReviewImages($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * get_review_details method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_review_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $review_id = isset($input_params["review_id"]) ? $input_params["review_id"] : "";
            $this->block_result = $this->user_store_review_model->get_review_details($review_id);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            $final_array=array();
           
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["comment"];
                    if (method_exists($this, "get_Limit_characters_feedback"))
                    {
                        $data = $this->get_Limit_characters_feedback($data, $result_arr[$data_key], $i, $input_params);
                    }
                    $result_arr[$data_key]["comment"] = $data;

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
        $input_params["get_review_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * review_images method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function review_images($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $review_id = isset($input_params["review_id"]) ? $input_params["review_id"] : "";
            $this->block_result = $this->user_review_images_model->review_images($review_id);
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

                    $data = $data_arr["usr_review_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $p_key = ($data_arr["usr_user_review_id"] != "") ? $data_arr["usr_user_review_id"] : $input_params["uri_user_review_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "review_images";
                    $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image($image_arr);

                    $result_arr[$data_key]["usr_review_image"] = $data;

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
        $input_params["review_images"] = $this->block_result["data"];

        return $input_params;
    }

    /**
     * formatting_images method is used to process custom function.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function formatting_images($input_params = array())
    {
        if (!method_exists($this->general, "add_review_format_output"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->general->add_review_format_output($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["formatting_images"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_review_finish_success($input_params = array())
    {
       // print_r($input_params);exit;
        $setting_fields = array(
            "success" => "1",
            "message" => "user_review_finish_success",
        );
        $output_fields = array(
            "usr_review_id",
            'usr_comment',
            'usr_user_id',
            'usr_ice_type',
            'usr_ice_quantity',
            'usr_store_id',
            'usr_status',
            'usr_updated_at',
            'usr_added_at',
            'images'

        );
        $output_keys = array(
            'get_review_details',
        );
        $ouput_aliases = array(
            "usr_review_id" => "Review_Id",
            'usr_comment'=>"Comments",
            'usr_user_id'=>"User_Id",
            'usr_ice_type'=>"Ice_type_Id",
            'usr_ice_quantity'=>"Ice_quantity_Id",
            'usr_store_id'=>"Store_Id",
            'usr_status'=>"Status",
            'usr_updated_at'=>"UpdatedAt",
            'usr_added_at'=>"AddedAt"

        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_review_details";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_review_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "user_review_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "set_store_review";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
