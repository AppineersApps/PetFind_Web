<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Comment Listing Controller
 *
 * @category webservice
 *
 * @package post
 *
 * @subpackage controllers
 *
 * @module Comment Listing
 *
 * @class Comment_listing.php
 *
 * @path application\webservice\post\controllers\Comment_listing.php
 *
 * @version 4.3
 *
 * @author CIT Dev Team
 *
 * @since 01.10.2019
 */

class Comment_listing extends Cit_Controller
{
    public $settings_params;
    public $output_params;
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
        $this->multiple_keys = array(
            "get_all_comments",
            "custom_function",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('comment_listing_model');
        $this->load->model("post/comments_model");
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
     * start_comment_listing method is used to initiate api execution flow.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 20.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_comment_listing($request_arr = array(), $inner_api = FALSE)
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

                $output_response = $this->comments_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->comments_finish_success_1($input_params);
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

        $this->block_result = array();
        try
        {

            $perpage_record = isset($input_params["perpage_record"]) ? $input_params["perpage_record"] : "";
            $comment_id = isset($input_params["comment_id"]) ? $input_params["comment_id"] : "";
            $page_index = isset($input_params["page_index"]) ? $input_params["page_index"] : 1;
            $this->block_result = $this->comments_model->get_all_comments($perpage_record, $comment_id, $page_index, $this->settings_params);
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

                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "user_images";
                    $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image($image_arr);

                    $result_arr[$data_key]["u_profile_image"] = $data;

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
    public function comments_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "comments_finish_success",
        );
        $output_fields = array(
            'c_comment',
            'c_added_at',
            'c_missing_pets_id',
            //'u_profile_image',
            'c_comment_id',
        );
        $output_keys = array(
            'get_all_comments',
        );
        $ouput_aliases = array(
            "c_comment" => "comments",
            "c_added_at" => "added_at",
            "c_missing_pets_id" => "missing_pets_id",
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
     * comments_finish_success_1 method is used to process finish flow.
     * @created Chetan Dvs | 16.09.2019
     * @modified Chetan Dvs | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function comments_finish_success_1($input_params = array())
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
