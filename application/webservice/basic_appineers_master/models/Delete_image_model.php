<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Notification Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Notification
 *
 * @class Delete_image_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Delete_image_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Delete_image_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }
   /**
     * delete_image method is used to execute database queries for delete missing pet  post.
     * @created Snehal Shinde | 01-03-2021
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_image($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["image_id"]))
            {
                $this->db->where("iImageId =", $params_arr["image_id"]);
            }
            $res = $this->db->delete("missing_pet_images");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

   
}
