<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Delete Account Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Delete Account
 *
 * @class Delete_account_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Delete_account_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 01.10.2019
 */

class Delete_review_comment_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * delete_user_account method is used to execute database queries for Delete Account API.
     * @created priyanka chillakuru | 01.10.2019
     * @modified priyanka chillakuru | 01.10.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_review_comment($params_arr = array(), $where_arr = array())
    {
        try
        {
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "")
            {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($where_arr["review_id"]) && $where_arr["review_id"] != "")
            {
                $this->db->where("iUserReviewId =", $where_arr["review_id"]);
            }

            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->set($this->db->protect("dtDeletedAt"), $params_arr["_dtdeletedat"], FALSE);
            $res = $this->db->update("user_store_review");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
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
    /**
     * delete_user_account method is used to execute database queries for Delete Account API.
     * @created priyanka chillakuru | 01.10.2019
     * @modified priyanka chillakuru | 01.10.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_review_images($params_arr = array(), $where_arr = array())
    {
        try
        {
            $result_arr = array();
            
            if (isset($where_arr["review_id"]) && $where_arr["review_id"] != "")
            {
                $this->db->where("iUserReviewId =", $where_arr["review_id"]);
            }

            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->set($this->db->protect("dtDeletedAt"), $params_arr["_dtdeletedat"], FALSE);
            $res = $this->db->update("user_review_images");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
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
