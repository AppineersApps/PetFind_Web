<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Page Settings Model
 *
 * @category webservice
 *
 * @package tools
 *
 * @subpackage models
 *
 * @module Page Settings
 *
 * @class Page_settings_model.php
 *
 * @path application\webservice\tools\models\Page_settings_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Get_ice_type_model extends CI_Model
{
    
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get_page_details method is used to execute database queries for Static Pages API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 09.09.2019
     * @param string $page_code page_code is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_ice_type_details()
    {
        try
        {
            $result_arr = array();

            $this->db->from("icetype AS it");
            $this->db->select("it.iIcetypeId AS it_id");
            $this->db->select("it.vIcetypeName AS it_name");
            $this->db->select("it.vIcetypeImage AS it_imagename");
            $this->db->select("it.eStatus AS it_status");
            
            //$this->db->where_in("it.eStatus", array('Active'));

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
