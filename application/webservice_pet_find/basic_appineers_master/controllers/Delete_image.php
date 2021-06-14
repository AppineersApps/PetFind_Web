<?php  
            
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of delete image Controller
 * 
 * @category webservice
 *            
 * @package basic_appineers_master
 * 
 * @subpackage controllers 
 * 
 * @module delete image
 * 
 * @class Delete_image.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Delete_image.php
 * 
 * @version 4.4
 *
 * @author CIT Dev Team
 * 
 * @since 23-04-2021
 */ 
 
class Delete_image extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;
      
    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct() {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array();
        $this->multiple_keys = array();
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('delete_image_model');
    }
      
    /**
     * rules_delete_api_log method is used to validate api input params.
     * @created Devangi Nirmal | 29.09.2020
     * @modified Devangi Nirmal | 29.09.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_delete_image($request_arr = array()){
        $valid_arr = array(
             "image_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "image_id_required"
                )
            )
            );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "delete_image");
        
        return $valid_res;
    }
    
    /**
     * start_delete_api_log method is used to initiate api execution flow.
     * @created Devangi Nirmal | 29.09.2020
     * @modified Devangi Nirmal | 29.09.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_delete_image($request_arr  = array(), $inner_api = FALSE) {
        try {
            $validation_res = $this->rules_delete_image($request_arr);
            if ($validation_res["success"] == "-5") {
                if($inner_api === TRUE){
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];

             $input_params = $this->check_image_status($input_params);
             

            if ($input_params["checkImageStatus"]["status"]==1)
            {
                
                  $get_deleted_image=$input_params["checkImageStatus"][0]["image"];
                  $missing_pet_id=$input_params["checkImageStatus"][0]["missing_pet_id"];

                  $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                   $folder_name= $aws_folder_name. "/missing_pet_image/".$missing_pet_id."/";
                    $file_name = $get_deleted_image;

                    $res = $this->general->deleteAWSFileData($folder_name,$file_name);

                   $delete_image_result = $this->delete_image_model->delete_image($input_params);
                   
                     if(isset($delete_image_result["data"]) && !empty($delete_image_result["data"]))
                    {
                        $output_response = $this->delete_image_finish_success($input_params);
                    }
                    else
                    {
                        $output_response = $this->delete_image_finish_failure($input_params);
                    }
                 
            }
            else
            {
                if($input_params["checkPostStatus"]["status"]==0)
                {
                    $output_response = $this->delete_image_not_available($input_params);
                }
                else
                {
                    $output_response = $this->delete_image_finish_failure($input_params);
                }
                 
            }

        return $output_response;
        
        
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

     /**
     * check_image_status method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified Snehal Shinde | 01.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_image_status($input_params = array())
    {
        if (!method_exists($this, "checkImageStatus"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkImageStatus($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkImageStatus"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }  
    

 /**
     * delete_image_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_image_finish_success($input_params = array())
    {
            $setting_fields = array(
                "success" => "1",
                "message" => "delete_image_finish_success",
           );

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_image";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    } 
      /**
     * delete_image_finish_failure method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_image_finish_failure($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "delete_image_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_image";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


     /**
     * delete_image_not_available method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_image_not_available($input_params = array())
    {
        
            $setting_fields = array(
                "success" => "1",
                "message" => "delete_image_not_available",
            );

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_image";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    
}