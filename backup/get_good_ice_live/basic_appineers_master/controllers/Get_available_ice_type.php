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

class Get_available_ice_type extends Cit_Controller
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
            "get_available_ice_type",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('get_available_ice_type_model');
    }

    /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_available_ice_type($request_arr = array())
    {
     $valid_arr = array(            
            "store_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "store_id_required",
                )
            )
            );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_available_ice_type");

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
    public function start_get_available_ice_type($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_available_ice_type($request_arr);
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

            $input_params = $this->get_available_ice_type($input_params);
            $condition_res = $this->is_posted($input_params);
            if ($condition_res["success"])
            {
                $output_response = $this->get_available_ice_type_finish_success($input_params);
                return $output_response;
            }
            else
            {
                $output_response = $this->get_available_ice_type_finish_success_1($input_params);
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
              
            $cc_lo_0 = count($input_params["get_available_ice_type"]);
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
     * get_review_details method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_available_ice_type($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $store_id = isset($input_params["store_id"]) ? $input_params["store_id"] : "";
            $this->block_result = $this->get_available_ice_type_model->get_available_ice_type_details($store_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
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
        $input_params["get_available_ice_type"] = $this->block_result["data"];
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
    public function get_available_ice_type_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "get_available_ice_type",
        );
        $output_fields = array(
            "usr_ice_type",
            "usr_ice_quantity",
            "usr_store_id",
            "usr_status",
            "usr_added_at",
            "usr_updated_at"
        );
        $output_keys = array(
            'get_available_ice_type',
        );
        $ouput_aliases = array(
            "usr_ice_type"=> "Ice_type_Id",
             "usr_ice_quantity"=>"Ice_quantity_Id",
            "usr_store_id"=>"Store_Id",
            "usr_status" => "Status",
            "usr_added_at"=>"AddedAt",
            "usr_updated_at" => "UpdatedAt"
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"]["get_available_ice_type"] = $input_params;


        $func_array["function"]["name"] = "get_available_ice_type";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;
        //$func_array["function"]["custom_keys"] = 'get_available_ice_type';
        $this->wsresponse->setResponseStatus(200);
        //$responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        $responce_arr['settings']= $output_array["settings"];
        $responce_arr['data'] = $input_params['get_available_ice_type'];
        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_available_ice_type_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_available_ice_type_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_available_ice_type";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
