<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Reset Password Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Reset Password
 *
 * @class Reset_password.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Reset_password.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class Reset_password extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
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
            "reset_password",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('reset_password_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * rules_reset_password method is used to validate api input params.
     * @created priyanka chillakuru | 17.09.2019
     * @modified priyanka chillakuru | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_reset_password($request_arr = array())
    {
        $valid_arr = array(
            "new_password" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "new_password_required",
                )
            ),
            "reset_key" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "reset_key_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "reset_password");

        return $valid_res;
    }

    /**
     * start_reset_password method is used to initiate api execution flow.
     * @created priyanka chillakuru | 17.09.2019
     * @modified priyanka chillakuru | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_reset_password($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_reset_password($request_arr);
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

            $input_params = $this->reset_password($input_params);

            $condition_res = $this->condition($input_params);
            if ($condition_res["success"])
            {

                $output_response = $this->users_finish_success_1($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->users_finish_success($input_params);
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
     * reset_password method is used to process query block.
     * @created priyanka chillakuru | 17.09.2019
     * @modified priyanka chillakuru | 17.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function reset_password($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["reset_key"]))
            {
                $where_arr["reset_key"] = $input_params["reset_key"];
            }
            if (isset($input_params["new_password"]))
            {
                $params_arr["new_password"] = $input_params["new_password"];
            }
            if (method_exists($this->general, "encryptCustomerPassword"))
            {
                $params_arr["new_password"] = $this->general->encryptCustomerPassword($params_arr["new_password"], $input_params);
            }
            $params_arr["_vresetpasswordcode"] = "''";
            $params_arr["_dtupdatedat"] = "NOW()";
            $this->block_result = $this->users_model->reset_password($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["reset_password"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * condition method is used to process conditions.
     * @created priyanka chillakuru | 17.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["affected_rows"];
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
     * users_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 17.09.2019
     * @modified priyanka chillakuru | 12.02.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "reset_password";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 17.09.2019
     * @modified priyanka chillakuru | 17.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "reset_password";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
