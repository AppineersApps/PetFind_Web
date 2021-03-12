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
 * @since 18.09.2019
 */

class Missing_pet extends Cit_Controller
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
            "set_missing_pet",
            "get_missing_pet_details",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('missing_pet_model');
         $this->load->model("send_notification_model");
    }

    /**
     * rules_set_store_item method is used to validate api input params.
     * @created Snehal Shinde | 01.03.2021
     * @modified Snehal Shinde | 01.03.2021
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_add_missing_pet($request_arr = array())
    {        
        $valid_arr = array(
            "dog_name" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "dog_name_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => 1,
                    "message" => "dog_name_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => 80,
                    "message" => "dog_name_maxlength",
                )
            ),
             "last_seen_date" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_date_required",
                )
            ), 
             "date_of_birth" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "date_of_birth_required",
                )
            ), 
             "last_seen_street" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_street_required",
                )
            ),
             "last_seen_city" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_city_required",
                )
            ),
             "last_seen_state" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_state_required",
                )
            ), 
              "last_seen_zip_code" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_zip_code_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => 6,
                    "message" => "last_seen_zip_code_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => 6,
                    "message" => "last_seen_zip_code_maxlength",
                )
            ),
             "hair_color" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "hair_color_required",
                )
            ),
             "eye_color" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "eye_color_required",
                )
            ),
             "height" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "height_required",
                )
            ),
            "weight" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "weight_required",
                )
            ),
            "gender" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "gender_required",
                )
            ),
            "breed" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "breed_required",
                )
            ),
             "body_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "body_type_required",
                )
            )
        );
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_missing_pet");

        return $valid_res;
    }

    /**
     * start_missing_pet method is used to initiate api execution flow.
     * @created Snehal Shinde | 01.03.2021
     * @modified Snehal Shinde | 01.03.2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_missing_pet($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
          case 'GET':
            if(true == isset($request_arr['page_code']) && 'search'==$request_arr['page_code'])
            {
                $output_response =  $this->search_item_serial_number($request_arr);
            }
            else
            {
               $output_response =  $this->get_item($request_arr);
            }

            return  $output_response;
            break;
          case 'POST':
               if(true == isset($request_arr['page_code']) && 'edit_pet'==$request_arr['page_code'] || 'edit_pet_status' ==$request_arr['page_code'])
              {
                 // echo'<pre>';print_r($request_arr);exit;
                $output_response =  $this->update_missing_pet($request_arr);
              }
              else
              {

                $output_response =  $this->add_missing_pet($request_arr);
              }
           return  $output_response;
             break;
          case 'DELETE':
             // print_r($request_arr);exit;
            $output_response = $this->delete_missing_pet($request_arr);
            return  $output_response;
             break;
        }
    }
     /**
     * rules_set_store_item method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_update_missing_pet($request_arr = array())
    {
       if(true == empty($request_arr['page_code'])){
            $valid_arr = array(
                "page_code" => array(
                    array(
                        "rule" => "required",
                        "value" => TRUE,
                        "message" => "page_code_required",
                    )
                )
            );

        }
        if(false == empty($request_arr['page_code']) && 'edit_pet_status' == $request_arr['page_code']){
             $valid_arr = array(
            "missing_pet_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "missing_pet_id_required",
                )
            )
             );
        }

        if(false == empty($request_arr['page_code']) && 'edit_pet' == $request_arr['page_code']){
         $valid_arr = array(            
            "dog_name" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "dog_name_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => 1,
                    "message" => "dog_name_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => 80,
                    "message" => "dog_name_maxlength",
                )
            ),
             "last_seen_date" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_date_required",
                )
            ), 
             "date_of_birth" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "date_of_birth_required",
                )
            ), 
             "last_seen_street" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_street_required",
                )
            ),
             "last_seen_city" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_city_required",
                )
            ),
             "last_seen_state" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_state_required",
                )
            ), 
              "last_seen_zip_code" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "last_seen_zip_code_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => 6,
                    "message" => "last_seen_zip_code_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => 6,
                    "message" => "last_seen_zip_code_maxlength",
                )
            ),
             "hair_color" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "hair_color_required",
                )
            ),
             "eye_color" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "eye_color_required",
                )
            ),
             "height" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "height_required",
                )
            ),
            "weight" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "weight_required",
                )
            ),
            "gender" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "gender_required",
                )
            ),
            "breed" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "breed_required",
                )
            ),
             "body_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "body_type_required",
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
        
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "update_missing_pet");

        return $valid_res;
    }
    /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_item($request_arr = array())
    {
        
       
           $valid_arr = array(            
            
           "item_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "item_id_number"
                    )
                ),
           "user_id" => array(
                    array(
                        "rule" => "number",
                        "value" => true,
                        "message" => "user_id_number"
                    )
                ),
            "page_code" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "item_page_code_required"
                )
            )
            );

        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_item");

        return $valid_res;
    }

    /**
     * start_set_store_item method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function get_item($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_item($request_arr);
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
            $input_params = $this->get_all_items($input_params);

            $condition_res = $this->is_posted($input_params);
            if ($condition_res["success"])
            {
                
                $output_response = $this->get_item_finish_success($input_params);
                return $output_response;
            }

            else
            {
 
                $output_response = $this->get_item_finish_success_1($input_params);
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
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
   public function rules_search_item_serial_number($request_arr = array())
    {
       $valid_arr = array( 
       
       "item_serial_number" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "item_serial_number_required",
                )
            ),
        
       "page_code" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "item_page_code_required",
                )
            )
        );

        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "search_item_serial_number");

        return $valid_res;
    }



/**
     * start_set_store_item method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function search_item_serial_number($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_search_item_serial_number($request_arr);
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

             $check_params = $this->check_serial_number_exist($input_params);
            if ($check_params["checkserialnumberexist"]["status"])
            {
                $output_response = $this->user_item_finish_success_serial_error($input_params);
                return $output_response;
                
            }else{

                $output_response = array();
                $input_params = $validation_res['input_params'];
                $output_array = $func_array = array();
               // print_r($input_params); exit;

                $input_params = $this->get_all_items_by_serial_number($input_params);

                $condition_res = $this->is_posted($input_params);
                if ($condition_res["success"])
                {
                   
                    $output_response = $this->get_item_by_serial_number_finish_success($input_params);
                    return $output_response;
                }

                else
                {
     
                    $output_response = $this->get_item_by_serial_number_finish_success_1($input_params);
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
     * get_review_details method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_items_by_serial_number($input_params = array())
        {
            $this->block_result = array();
            try
            {
                $arrResult = array();
                          
                $arrResult['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
                $arrResult['item_serial_number'] = isset($input_params["item_serial_number"]) ? $input_params["item_serial_number"] : "";
                $arrResult['page_code'] = isset($input_params["page_code"]) ? $input_params["page_code"] : "";

                $this->block_result = $this->item_model->get_item_details_by_serial_number($arrResult);
                if (!$this->block_result["success"])
                {
                    throw new Exception("No records found.");
                }
                $result_arr = $this->block_result["data"];

                   if (is_array($result_arr) && count($result_arr) > 0)
                    {
                        foreach ($result_arr as $data_key => $data_arr)
                        {
                            $selected = array();
                            $data =array();
                            $i = 1;
                            $upper_limit=5;
                            for($i=1; $i<=$upper_limit; $i++)
                            {
                                if(false == empty($data_arr["image_".$i]))
                                {
                                    $data = $data_arr["image_".$i];
                                    $image_arr = array();
                                    $image_arr["image_name"] = $data;
                                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                                    $p_key = ($data_arr["item_id"] != "") ? $data_arr["item_id"] : $input_params["item_id"];
                                    $image_arr["pk"] = $p_key;
                                    $image_arr["color"] = "FFFFFF";
                                    $image_arr["no_img"] = FALSE;
                                    $dest_path = "serialize_it/item_image";
                                    $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                                    $data = $this->general->get_image_aws($image_arr);
                                    array_push($selected, $data);
                                    if(false == empty($selected)){
                                        $result_arr[$data_key]["images"] = $selected; 
                                    }
                                }else{
                                    $result_arr[$data_key]["images"] = $selected;
                                }
                               
                            }
                            $data = $data_arr["item_owner_image"];
                            $image_arr = array();
                            $image_arr["image_name"] = $data;
                            $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                            $image_arr["color"] = "FFFFFF";
                            $image_arr["no_img"] = FALSE;
                            $dest_path = "serialize_it/user_profile";
                            $image_arr["path"] = "serialize_it/user_profile";
                            $data = $this->general->get_image_aws($image_arr);

                            $result_arr[$data_key]["item_owner_image"] = $data;
                        }
                        $this->block_result["data"] = $result_arr;
                    }
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
            $input_params["get_all_items"] = $this->block_result["data"];
            
            $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
           return $input_params;
        }


     /**
     * start_edit_profile method is used to initiate api execution flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function update_missing_pet($request_arr = array(), $inner_api = FALSE)
    {

        try
        {
            $validation_res = $this->rules_update_missing_pet($request_arr);

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

            $input_params = $this->check_missing_pet_exist($input_params);
           
            if ($input_params["checkMissingPetExist"]["status"])
            {   
                if(false == empty($input_params['image_1']) || false == empty($input_params['image_2']) || false == empty($input_params['image_3']) || false == empty($input_params['image_4']) || false == empty($input_params['image_5'])){
                  $input_params = $this->get_missing_pet_image($input_params); 
                  
                  $input_params = $this->custom_image_function($input_params);
                  $input_params = $this->get_image_details($input_params); 
                   
                 
                }
                $input_params = $this->update_exist_missing_pet($input_params);
               // echo '<pre>'; print_r($input_params);exit;
                if ($input_params["affected_rows"])
                {
                   
                    
                    $output_response = $this->get_update_finish_success($input_params);
                        return $output_response;
                }else{
                    $output_response = $this->get_update_finish_success_1($input_params);
                    return $output_response;
                }
            }
            else
            {

                $output_response = $this->get_update_finish_success_1($input_params);
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
     * get_updated_details method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_image_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $arrResult['missing_pet_id'] = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $this->block_result = $this->missing_pet_model->get_missing_pet_details($arrResult);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            
            $arrImageArray =array();
             if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data_1 = $data_arr["image_1"];
                    $arrImageArray[$data_key]["image_1"] = (false == empty($data_1))?$data_1:'';
                    $data_2 = $data_arr["image_2"];                  

                    $arrImageArray[$data_key]["image_2"] = (false == empty($data_2))?$data_2:'';
                    $data_3 = $data_arr["image_3"];                  

                    $arrImageArray[$data_key]["image_3"] = (false == empty($data_3))?$data_3:'';
                    $data_4 = $data_arr["image_4"];                  

                    $arrImageArray[$data_key]["image_4"] = (false == empty($data_4))?$data_4:'';
                    $data_5 = $data_arr["image_5"];                  

                    $arrImageArray[$data_key]["image_4"] = (false == empty($data_5))?$data_5:'';

                    $i++;
                }
                $this->block_result["data"] = $arrImageArray;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_image_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }
     /**
     * get_updated_details method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_missing_pet_image($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $arrResult['missing_pet_id'] = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $this->block_result = $this->missing_pet_model->get_missing_pet_details($arrResult);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
        
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $selected = array();
                $data =array();
                $upper_limit=5;
                $img_name="image_";
                $folder_name= $aws_folder_name. "/missing_pet_image/".$arrResult['missing_pet_id']."/";
                $insert_arr = array();
                $temp_var   = 0;
                for($i=1; $i<=$upper_limit; $i++)
                {
                      $new_file_name=$img_name.$i;

                      if(false == empty($_FILES[$new_file_name]['name']))
                      {
                        if(false == empty($result_arr['0'][$new_file_name]))
                        {
                            $file_name = $result_arr['0'][$new_file_name];
                            $res = $this->general->deleteAWSFileData($folder_name,$file_name);
                            if($res)
                            {
                              $insert_arr['vImageId_'.$i.''] = '';
                              $temp_var++;
                            }
                        }
                      
                      }                       
                }
                if(is_array($insert_arr) && false == empty($insert_arr))
                {
                  $this->db->where('iMissingPetId ', $arrResult['missing_pet_id']);
                  $this->db->update("missing_pets",$insert_arr);
                }
                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
     
        return $input_params;
    }

     /**
     * update_profile method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_exist_missing_pet($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            if (isset($input_params["missing_pet_id"]))
            {
                $where_arr["missing_pet_id"] = $input_params["missing_pet_id"];
            }
            
            $params_arr["_dtupdatedat"] = "NOW()";            
            $params_arr["ePetStatus"] = "missing";
            
        
             if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["dog_name"]))
            {
                $params_arr["dog_name"] = $input_params["dog_name"];
            }
            if (isset($input_params["last_seen_date"]))
            {
                $params_arr["last_seen_date"] = $input_params["last_seen_date"];
            }
            if (isset($input_params["date_of_birth"]))
            {
                $params_arr["date_of_birth"] = $input_params["date_of_birth"];
            }
            if (isset($input_params["last_seen_street"]))
            {
                $params_arr["last_seen_street"] = $input_params["last_seen_street"];
            }
            if (isset($input_params["last_seen_city"]))
            {
                $params_arr["last_seen_city"] = $input_params["last_seen_city"];
            }
            if (isset($input_params["last_seen_state"]))
            {
                $params_arr["last_seen_state"] = $input_params["last_seen_state"];
            }
            if (isset($input_params["last_seen_zip_code"]))
            {
                $params_arr["last_seen_zip_code"] = $input_params["last_seen_zip_code"];
            }
            if (isset($input_params["last_seen_lattitude"]))
            {
                $params_arr["last_seen_lattitude"] = $input_params["last_seen_lattitude"];
            }
            if (isset($input_params["last_seen_longitude"]))
            {
                $params_arr["last_seen_longitude"] = $input_params["last_seen_longitude"];
            }
            if (isset($input_params["hair_color"]))
            {
                $params_arr["hair_color"] = $input_params["hair_color"];
            }
            if (isset($input_params["eye_color"]))
            {
                $params_arr["eye_color"] = $input_params["eye_color"];
            } 
            if (isset($input_params["height"]))
            {
                $params_arr["height"] = $input_params["height"];
            }
             if (isset($input_params["weight"]))
            {
                $params_arr["weight"] = $input_params["weight"];
            } 
            if (isset($input_params["gender"]))
            {
                $params_arr["gender"] = $input_params["gender"];
            }
             if (isset($input_params["breed"]))
            {
                $params_arr["breed"] = $input_params["breed"];
            } 
            if (isset($input_params["body_type"]))
            {
                $params_arr["body_type"] = $input_params["body_type"];
            }
            if (isset($input_params["identity_mark"]))
            {
                $params_arr["identity_mark"] = $input_params["identity_mark"];
            }
             if (isset($input_params["dog_details"]))
            {
                $params_arr["dog_details"] = $input_params["dog_details"];
            }
            if (isset($input_params["image_1"]))
            {
                $params_arr["image_1"] = $input_params["image_1"];
            }
            if (isset($input_params["image_2"]))
            {
                $params_arr["image_2"] = $input_params["image_2"];
            }
            if (isset($images_arr["image_3"]["name"]))
            {
                $params_arr["image_3"] = $images_arr["image_3"]["name"];
            }
            if (isset($images_arr["image_4"]["name"]))
            {
                $params_arr["image_4"] = $images_arr["image_4"]["name"];
            }
            if (isset($images_arr["image_5"]["name"]))
            {
                $params_arr["image_5"] = $images_arr["image_5"]["name"];
            }
            $this->block_result = $this->missing_pet_model->update_missing_pet($params_arr, $where_arr);
            if (!$this->block_result["success"])
            {
                throw new Exception("updation failed.");
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
        $input_params["update_missing_pet"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * checkuniqueusername method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified saikumar anantham | 08.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_missing_pet_exist($input_params = array())
    {

        if (!method_exists($this, "checkMissingPetExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkMissingPetExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkMissingPetExist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        //print_r($input_params);
        return $input_params;
    }

    /**
     * get_all_items method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_items($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $arrResult = array();
                      
            $arrResult['item_status'] = isset($input_params["item_status"]) ? $input_params["item_status"] : "";
            $arrResult['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $arrResult['item_id'] = isset($input_params["item_id"]) ? $input_params["item_id"] : "";
            $arrResult['page_code'] = isset($input_params["page_code"]) ? $input_params["page_code"] : "";

            $this->block_result = $this->missing_pet_model->get_missing_pet_details($arrResult);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

               if (is_array($result_arr) && count($result_arr) > 0)
                {
                    
                    foreach ($result_arr as $data_key => $data_arr)
                    {
                        $selected = array();
                        $data =array();
                        $upper_limit=5;
                        
                        for($i=1; $i<=$upper_limit; $i++)
                        {
                            if(false == empty($data_arr["image_".$i]))
                            {
                                $data = $data_arr["image_".$i];
                                $image_arr = array();
                                $image_arr["image_name"] = $data;
                                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                                $p_key = ($data_arr["item_id"] != "") ? $data_arr["item_id"] : $input_params["item_id"];
                                $image_arr["pk"] = $p_key;
                                $image_arr["color"] = "FFFFFF";
                                $image_arr["no_img"] = FALSE;
                                $image_arr["path"] = "serialize_it/item_image";
                                $data = $this->general->get_image_aws($image_arr);
                                array_push($selected, $data);
                                if(false == empty($selected)){
                                    $result_arr[$data_key]["images"] = $selected; 
                                }
                            }
                            else
                            {
                                    $result_arr[$data_key]["images"] = $selected;
                            }
                           
                        }
                    }
                    $this->block_result["data"] = $result_arr;
                }
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
        $input_params["get_all_items"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }

    public function add_missing_pet($input){
        try
        {
            
            $validation_res = $this->rules_add_missing_pet($input);
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
               $input_params = $validation_res['input_params'];
                
                $output_response = array();
               
                $output_array = $func_array = array();
               
                $input_params = $this->set_missing_pet($input_params);

                $condition_res = $this->is_posted($input_params);

                if ($condition_res["success"])
                {
                    $input_params = $this->custom_image_function($input_params);  
                    $input_params = $this->get_missing_pet_details($input_params); 
                      // get notification and send notifications to tagged peoples
                     $input_params = $this->custom_function($input_params);
                       
                     $input_params = $this->get_user_details_for_send_notifi($input_params);
                    
                     $input_params = $this->start_loop_1($input_params); 
                    $output_response = $this->missing_pet_finish_success($input_params);
                    return $output_response;
                }

                else
                {
                    $output_response = $this->missing_pet_finish_success_1($input_params);
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
    public function set_missing_pet($input_params = array())
    {
       
        $this->block_result = array();

        try
        {
            $params_arr = array();
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtaddedat"] = $input_params["timestamp"];
            }else{
               // $params_arr["_dtaddedat"] = date("Y-m-d H:i:s"); 
               $params_arr["_dtaddedat"] = "NOW()"; 
            }
            $params_arr["ePetStatus"] = "missing";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["dog_name"]))
            {
                $params_arr["dog_name"] = $input_params["dog_name"];
            }
            if (isset($input_params["last_seen_date"]))
            {
                $params_arr["last_seen_date"] = $input_params["last_seen_date"];
            }
            if (isset($input_params["date_of_birth"]))
            {
                $params_arr["date_of_birth"] = $input_params["date_of_birth"];
            }
            if (isset($input_params["last_seen_street"]))
            {
                $params_arr["last_seen_street"] = $input_params["last_seen_street"];
            }
            if (isset($input_params["last_seen_city"]))
            {
                $params_arr["last_seen_city"] = $input_params["last_seen_city"];
            }
            if (isset($input_params["last_seen_state"]))
            {
                $params_arr["last_seen_state"] = $input_params["last_seen_state"];
            }
            if (isset($input_params["last_seen_zip_code"]))
            {
                $params_arr["last_seen_zip_code"] = $input_params["last_seen_zip_code"];
            }
            if (isset($input_params["last_seen_lattitude"]))
            {
                $params_arr["last_seen_lattitude"] = $input_params["last_seen_lattitude"];
            }
            if (isset($input_params["last_seen_longitude"]))
            {
                $params_arr["last_seen_longitude"] = $input_params["last_seen_longitude"];
            }
            if (isset($input_params["hair_color"]))
            {
                $params_arr["hair_color"] = $input_params["hair_color"];
            }
            if (isset($input_params["eye_color"]))
            {
                $params_arr["eye_color"] = $input_params["eye_color"];
            } 
            if (isset($input_params["height"]))
            {
                $params_arr["height"] = $input_params["height"];
            }
             if (isset($input_params["weight"]))
            {
                $params_arr["weight"] = $input_params["weight"];
            } 
            if (isset($input_params["gender"]))
            {
                $params_arr["gender"] = $input_params["gender"];
            }
             if (isset($input_params["breed"]))
            {
                $params_arr["breed"] = $input_params["breed"];
            } 
            if (isset($input_params["body_type"]))
            {
                $params_arr["body_type"] = $input_params["body_type"];
            }
            if (isset($input_params["identity_mark"]))
            {
                $params_arr["identity_mark"] = $input_params["identity_mark"];
            }
             if (isset($input_params["dog_details"]))
            {
                $params_arr["dog_details"] = $input_params["dog_details"];
            }
           
            $this->block_result = $this->missing_pet_model->set_missing_pet($params_arr);
            
             if ($this->block_result["success"])
            {
                $inserted_missing_pet_id=$this->block_result["data"][0]["missing_pet_id"];
                $tagged_people_result = $this->missing_pet_model->set_tagged_people($input_params,$inserted_missing_pet_id);

            }
           
            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_missing_pet"] = $this->block_result["data"];
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
            $cc_lo_0 = (is_array($input_params["missing_pet_id"])) ? count($input_params["missing_pet_id"]):$input_params["missing_pet_id"];
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
     * is_posted method is used to process conditions.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_fetched($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $cc_lo_0 = $input_params["item_id"];
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
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function missing_pet_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "Missing pet added successfully";
        $output_arr['data'] = '';
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "add_missing_pet");

        return $responce_arr;



//         $setting_fields = array(
//             "success" => "1",
//             "message" => "Missing pet added successfully",
//         );
//         $output_fields = array(
//             'missing_pet_id',
//             'user_id',
//             'dog_name',
//             'last_seen_date',
//             'date_of_birth',
//             'last_seen_street',
//             'last_seen_city',
//             'last_seen_state',
//             'last_seen_zip_code',
//             'last_seen_latitude',
//             'last_seen_longitude',
//             'hair_color',
//             'eye_color',
//             'height',
//             'weight',
//             'gender',
//             'breed',
//             'identity_mark',
//             'body_type',
//             'image_1',
//             'image_2',
//             'image_3',
//             'image_4',
//             'image_5',
//             'dog_details',
//             'pet_status',
//             'found_user_id',
//             'pet_found_street_address',
//             'pet_found_city',
//             'pet_found_state',
//             'pet_found_latitude',
//             'pet_found_longitude',
//             'pet_found_date',
//             'added_at',
//             'updated_at',
//             'tagged_people',
//         );
//         $output_keys = array(
//             'get_missing_pet_details',
//         );

//         $ouput_aliases = array(
//             "get_missing_pet_details" => "get_missing_pet_details",
//             "missing_pet_id" => "missing_pet_id",
//             "user_id" => "user_id",
//             "dog_name" => "dog_name",
//             "last_seen_date" => "last_seen_date",
//             "date_of_birth" => "date_of_birth",
//             "last_seen_city" => "last_seen_city",
//             "last_seen_state" => "last_seen_state",
//             "last_seen_zip_code" => "last_seen_zip_code",
//             "last_seen_latitude" => "last_seen_latitude",
//             "last_seen_longitude" => "last_seen_longitude",
//             "hair_color" => "hair_color",
//             "eye_color" => "eye_color",
//             "height" => "height",
//             "weight" => "weight",
//             "gender" => "gender",
//             "breed" => "breed",
//             "identity_mark" => "identity_mark",
//             "body_type" => "body_type",
//             "image_1" => "image_1",
//             "image_2" => "image_2",
//             "image_3" => "image_3",
//             "image_4" => "image_4",
//             "image_5" => "image_5",
//             "dog_details" => "dog_details",
//             "pet_status" => "pet_status",
//             "found_user_id" =>"found_user_id",
//             "pet_found_street_address"=>"pet_found_street_address",
//             "pet_found_city" =>"pet_found_city",
//             "pet_found_state" =>"pet_found_state",
//             "pet_found_latitude" =>"pet_found_latitude",
//             "pet_found_longitude" =>"pet_found_longitude",
//             "pet_found_date"=> "pet_found_date",
//             "added_at" => "added_at",
//             "updated_at" => "updated_at",
//             "tagged_people" => "tagged_people",
//         );

// //print_r($ouput_aliases);exit;
//         $output_array["settings"] = $setting_fields;
//         $output_array["settings"]["fields"] = $output_fields;
//         $output_array["data"] = $input_params;

//         $func_array["function"]["name"] = "add_missing_pet";
//         $func_array["function"]["output_keys"] = $output_keys;
//         $func_array["function"]["output_alias"] = $ouput_aliases;
//         $func_array["function"]["single_keys"] = $this->single_keys;
//         $func_array["function"]["multiple_keys"] = $this->multiple_keys;

//         $this->wsresponse->setResponseStatus(200);

//         $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

//         return $responce_arr;

    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function missing_pet_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "missing_pet_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_missing_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_item_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "get_item_finish_success"
        );
       
        $output_fields = array(
            "item_id",
            "item_description",
            "item_name",
            "item_status",
            "item_report_status",
            "item_serial_number",
            "store_name",
            "item_purchase_street_address",
            "item_purchase_date",
            "item_purchase_city",
            "item_purchase_state",
            "item_purchase_zip_code",
            "item_lost_date",
            "item_lost_street_address",
            "item_lost_state",
            "item_lost_city",
            "item_lost_zip_code",
            "item_lost_latitude",
            "item_lost_longitude",
            "item_found_date",
            "item_found_street_address",
            "item_found_state",
            "item_found_city",
            "item_found_zip_code",
            "item_found_latitude",
            "item_found_longitude",
            "item_store_name",
            "images"

        );
        $output_keys = array(
            'get_all_items',
        );
        $ouput_aliases = array(
           "item_id" => "item_id",
            "item_description" => "item_description",
            "item_name" => "item_name",
            "item_status" => "item_status",
            "item_report_status" => "item_report_status",
            "item_serial_number" => "item_serial_number",
            "store_name" => "store_name",
            "item_purchase_street_address" => "item_purchase_street_address",
            "item_purchase_date" => "item_purchase_date",
            "item_purchase_city" => "item_purchase_city",
            "item_purchase_state" => "item_purchase_state",
            "item_purchase_zip_code" => "item_purchase_zipcode",
            "item_lost_date" => "item_lost_date",
            "item_lost_street_address" => "item_lost_street_address",
            "item_lost_state" => "item_lost_state",
            "item_lost_city" => "item_lost_city",
            "item_lost_zip" => "item_lost_zip",
            "item_lost_latitude" => "item_lost_latitude",
            "item_lost_longitude" =>"item_lost_longitude",
            "item_found_date" => "item_found_date",
            "item_found_street_address" => "item_found_street_address",
            "item_found_state" => "item_found_state",
            "item_found_city" => "item_found_city",
            "item_found_zip_code" => "item_found_zip_code",
            "item_found_latitude" => "item_found_latitude",
            "item_found_longitude" => "item_found_longitude",
            "item_store_name" => "item_store_name"
        );
        
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "add_missing_pet";
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
    public function get_item_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_item_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_missing_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_item_by_serial_number_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "get_item_by_serial_number_finish_success"
        );
       
        $output_fields = array(
            "item_id",
            "item_description",
            "item_name",
            "item_status",
            "item_report_status",
            "item_serial_number",
            "store_name",
            "item_purchase_street_address",
            "item_purchase_date",
            "item_purchase_city",
            "item_purchase_state",
            "item_purchase_zip_code",
            "item_lost_date",
            "item_lost_street_address",
            "item_lost_state",
            "item_lost_city",
            "item_lost_zip_code",
            "item_lost_latitude",
            "item_lost_longitude",
            "item_found_date",
            "item_found_street_address",
            "item_found_state",
            "item_found_city",
            "item_found_zip_code",
            "item_found_latitude",
            "item_found_longitude",
            "item_store_name",
            "images",
            "item_owner_name",
            "item_owner_email",
            "item_owner_phone",
            "item_owner_userId",
            "item_owner_image"

        );
        $output_keys = array(
            'get_all_items',
        );
        $ouput_aliases = array(
            "item_id" => "item_id",
            "item_description" => "item_description",
            "item_name" => "item_name",
            "item_status" => "item_status",
            "item_report_status" => "item_report_status",
            "item_serial_number" => "item_serial_number",
            "store_name" => "store_name",
            "item_purchase_street_address" => "item_purchase_street_address",
            "item_purchase_date" => "item_purchase_date",
            "item_purchase_city" => "item_purchase_city",
            "item_purchase_state" => "item_purchase_state",
            "item_purchase_zip_code" => "item_purchase_zipcode",
            "item_lost_date" => "item_lost_date",
            "item_lost_street_address" => "item_lost_street_address",
            "item_lost_state" => "item_lost_state",
            "item_lost_city" => "item_lost_city",
            "item_lost_zip" => "item_lost_zip",
            "item_lost_latitude" => "item_lost_latitude",
            "item_lost_longitude" =>"item_lost_longitude",
            "item_found_date" => "item_found_date",
            "item_found_street_address" => "item_found_street_address",
            "item_found_state" => "item_found_state",
            "item_found_city" => "item_found_city",
            "item_found_zip_code" => "item_found_zip_code",
            "item_found_latitude" => "item_found_latitude",
            "item_found_longitude" => "item_found_longitude",
            "item_store_name" => "item_store_name",
            "item_owner_name" => "item_owner_name",
            "item_owner_email" => "item_owner_email",
            "item_owner_phone" => "item_owner_phone",
            "item_owner_userId" => "item_owner_userId",
            "item_owner_image" => "item_owner_image"
        );
        
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "add_missing_pet";
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
    public function get_item_by_serial_number_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_item_by_serial_number_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_missing_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_update_finish_success($input_params = array())
    {
       
         $setting_fields = array(
            "success" => "1",
            "message" => "get_update_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "update_missing_pet";
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
    public function get_update_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_update_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "update_missing_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * get_deleted_review method is used to initiate api execution flow.
     * @created aditi billore | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function delete_missing_pet($request_arr = array())
    {
        // print_r($request_arr);exit;
      try
        {
           
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $request_arr;

            $input_params = $this->check_missing_pet_exist($input_params);
            
            if ($input_params["checkMissingPetExist"]["status"])
            {

               $input_params = $this->delete_pet($input_params);

               if ($input_params["affected_rows"])
                {
                    
                        $output_response = $this->delete_missing_pet_finish_success($input_params);
                        return $output_response;
                }

                else
                {
                    $output_response = $this->delete_missing_pet_finish_success_1($input_params);
                    return $output_response;
                }
            }else{
                $output_response = $this->delete_missing_pet_finish_success_1($input_params);
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
     * delete review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_pet($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
           
            $arrResult['missing_pet_id']  = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $arrResult['dtDeletedAt']  = "NOW()";
            $arrResult['ePostStatus']  = "Inactive";
            
            $this->block_result = $this->missing_pet_model->delete_missing_pet($arrResult);
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
        $input_params["delete_pet"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }

     /**
     * delete_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_missing_pet_finish_success($input_params = array())
    {
     $setting_fields = array(
            "success" => "1",
            "message" => "delete_missing_pet_finish_success"
        );
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_item";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
    /**
     * delete_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_missing_pet_finish_success_1($input_params = array())
    {
     $setting_fields = array(
            "success" => "0",
            "message" => "delete_missing_pet_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_item";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
/**
     * format_email_v1 method is used to process custom function.
     * @created priyanka chillakuru | 07.11.2019
     * @modified saikumar anantham | 07.11.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function format_email_v1($input_params = array())
    {
        if (!method_exists($this->general, "format_email"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->general->format_email($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["format_email_v1"] = $format_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }
    /**
     * custom_function method is used to process custom function.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 31.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_image_function($input_params = array())
    {
       if (!method_exists($this, "uploadQueryImages"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->uploadQueryImages($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_image_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
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
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_item_finish_success_serial_error($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "user_item_finish_success_serial_error",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_missing_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    /**
     * checkuniqueusername method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified saikumar anantham | 08.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_serial_number_exist($input_params = array())
    {
        if (!method_exists($this, "checkSerialNumberExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkSerialNumberExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkserialnumberexist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        //print_r($input_params);
        return $input_params;
    }

    //  get missing_pet_details
        public function get_missing_pet_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $where_clause = isset($input_params["missing_pet_id"]) ? $input_params["missing_pet_id"] : "";
            $this->block_result = $this->missing_pet_model->get_missing_pet_details($input_params);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            print_r($result_arr);exit;
             if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "serialize_it/user_profile";
                    $image_arr["path"] = "serialize_it/user_profile";
                    $data = $this->general->get_image_aws($image_arr);

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
        $input_params["get_missing_pet_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;

    }

    /**
     * get_user_details_for_send_notifi method is used to get details of user for notification.
     * @created CIT Dev Team
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_for_send_notifi($input_params = array())
    {


        $this->block_result = array();
        try
        {
            // print_r($input_params);exit;
            $this->block_result = $this->send_notification_model->get_user_details_for_send_notifi($input_params);
            // print_r( $this->block_result);exit;
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
        $input_params["get_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
            /**
     * start_loop_1 method is used to process loop flow.
     * @created CIT Dev Team
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function start_loop_1($input_params = array())
    {
         // print_r('hi');exit;
        $this->iterate_start_loop_1($input_params["get_user_details_for_send_notifi"], $input_params);
        return $input_params;
    }
    /**
     * iterate_start_loop_1 method is used to iterate loop.
     * @created CIT Dev Team
     * @param array $get_near_by_users_lp_arr array to iterate loop.
     * @param array $input_params_addr $input_params_addr array to address original input params.
     */
    public function iterate_start_loop_1(&$get_near_by_users_lp_arr = array(), &$input_params_addr = array())
    {
            // print_r($input_params_addr);exit;
        $input_params_loc = $input_params_addr;
        $_loop_params_loc = $get_near_by_users_lp_arr;
        $_lp_ini = 0;
        $_lp_end = count($_loop_params_loc);
        for ($i = $_lp_ini; $i < $_lp_end; $i += 1)
        {
            $get_near_by_users_lp_pms = $input_params_loc;

            unset($get_near_by_users_lp_pms["get_user_details_for_send_notifi"]);
            if (is_array($_loop_params_loc[$i]))
            {
                $get_near_by_users_lp_pms = $_loop_params_loc[$i]+$input_params_loc;
            }
            else
            {
                $get_near_by_users_lp_pms["get_user_details_for_send_notifi"] = $_loop_params_loc[$i];
                $_loop_params_loc[$i] = array();
                $_loop_params_loc[$i]["get_user_details_for_send_notifi"] = $get_near_by_users_lp_pms["get_user_details_for_send_notifi"];
            }

            $get_near_by_users_lp_pms["i"] = $i;
            $input_params = $get_near_by_users_lp_pms;

            $condition_res = $this->check_receiver_device_token($input_params);
            if ($condition_res["success"])
            {

                $input_params = $this->post_notification($input_params);
                $input_params = $this->push_notification($input_params);
            }

            $get_near_by_users_lp_arr[$i] = $this->wsresponse->filterLoopParams($input_params, $_loop_params_loc[$i], $get_near_by_users_lp_pms);
        }
    }
     /**
     * check_receiver_device_token method is used to process conditions.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receiver_device_token($input_params = array())
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
     * post_notification method is used to process query block.
     * @created CIT Dev Team
     * @modified ---
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
            
            $params_arr = array();
            if (isset($input_params["notification_message"]))
            {
                $params_arr["notification_message"] = $input_params["notification_message"];
            }
            if (isset($input_params["receiver_id"]))
            {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["user_id"]))
            {
                $params_arr["sender_id"] = $input_params["user_id"];
            }
            if (isset($input_params["missing_pet_id"]))
            {
                $params_arr["missing_pet_id"] = $input_params["missing_pet_id"];
            }
            
            
                $params_arr["_enotificationtype"] = "New missing pet post created";
           
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Unread";
            $this->block_result = $this->send_notification_model->post_notification($params_arr);
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
     * push_notification method is used to process mobile push notification.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {
       // print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            $device_id = $input_params["u_device_token"];
            //echo $device_id;exit;
            $code = "USER";
            $sound = "default";
            $badge = "";
            $title = "";
            $type = "new_post_added";
            $send_vars = array(
                array(
                    "key" => "type",
                    "value" => $type,
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
                    "key" => "user_type",
                    "value" => "provider",
                    "send" => "Yes",
                )
            );
            $push_msg = "".$input_params["notification_message"]."";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "runtime";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
             $send_arr['type'] = $type;
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





}
