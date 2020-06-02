<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User review Images Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User review Images
 *
 * @class User_review_images_model.php
 *
 * @path application\webservice\basic_appineers_master\models\User_review_images_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class User_review_images_model extends CI_Model
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
     * review_images method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function review_images($review_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_review_images AS uri");
            $this->db->select("uri.vReviewImage AS usr_review_image");
            $this->db->select("uri.iUserReviewId AS usr_user_review_id");
            if (isset($review_id) && $review_id != "")
            {
                $this->db->where("uri.iUserReviewId =", $review_id);
            }

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
        //echo $this->db->last_review();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
