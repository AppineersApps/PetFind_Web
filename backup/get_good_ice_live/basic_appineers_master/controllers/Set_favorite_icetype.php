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

class Set_favorite_icetype extends Cit_Controller
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
            "set_favorite_icetype"
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('set_favorite_icetype_model');
    }

    /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_set_favorite_icetype($request_arr = array())
    {
        $valid_arr = array(
            
            "icetype_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "icetype_id_required",
                )
            ),
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "set_favorite_store");

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
    public function start_set_favorite_icetype($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_set_favorite_icetype($request_arr);
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
            $condition_res = $this->get_favorite_icetype_details($input_params);
            if ($condition_res["success"])
            {
                $input_params = $this->update_favorite_icetype($input_params);

                $condition_res = $this->is_posted($input_params);
                if ($condition_res["success"])
                {
                    $output_response = $this->set_favorite_icetype_finish_success($input_params);
                    return $output_response;
                }
                else
                {
                    $output_response = $this->set_favorite_icetype_finish_success_1($input_params);
                    return $output_response;

                }
            }

            else
            {
                $input_params = $this->set_favorite_icetype($input_params);

                $condition_res = $this->is_posted($input_params);
                if ($condition_res["success"])
                {
                    $output_response = $this->set_favorite_icetype_finish_success($input_params);
                    return $output_response;
                }
                else
                {
                    $output_response = $this->set_favorite_icetype_finish_success_1($input_params);
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
     * update_notification method is used to process query block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 17.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_favorite_icetype($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"]))
            {
                $where_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["icetype_id"]))
            {
                $where_arr["icetype_id"] = $input_params["icetype_id"];
            }
            if (isset($input_params["status"]))
            {
                $params_arr["status"] = $input_params["status"];
            }
            $params_arr["_dtupdatedat"] = "NOW()";
            $this->block_result = $this->set_favorite_icetype_model->update_favorite_icetype($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_notification"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
    /**
     * get_review_details method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_favorite_icetype_details($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $icetype_id = isset($input_params["icetype_id"]) ? $input_params["icetype_id"] : "";
            $this->block_result = $this->set_favorite_icetype_model->get_favorite_store_details($user_id,$icetype_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result;
       
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        return $result_arr;
    }

    /**
     * set_store_review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function set_favorite_icetype($input_params = array())
    {
        $this->block_result = array();
        try
        {   
            $params_arr = array();
            $params_arr["_dtaddedat"] = "NOW()";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["icetype_id"]))
            {
                $params_arr["icetype_id"] = $input_params["icetype_id"];
            }
            if (isset($input_params["status"]))
            {
                $params_arr["status"] = $input_params["status"];
            }
            $params_arr["_dtupdatedat"] = "''";
            $this->block_result = $this->set_favorite_icetype_model->set_favorite_icetype($params_arr);

        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["set_favorite_icetype"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }    

    /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function set_favorite_icetype_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "set_favorite_icetype_finish_success",
        );
        $output_fields = array();
        $output_keys = array(
            'set_favorite_icetype',
        );
        $ouput_aliases = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "set_favorite_icetype";
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
    public function set_favorite_icetype_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "set_favorite_icetype_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "set_favorite_icetype";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
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

            $cc_lo_0 = $input_params["user_id"];
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
}
