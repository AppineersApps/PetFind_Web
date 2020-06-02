<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Post a Feedback Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Post a Feedback
 *
 * @class Post_a_feedback_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Post_a_feedback_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Get_available_ice_type_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }
     /**
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_available_ice_type_details($store_id)
    {
        try
        {
            $result_arr = array();
            $strWhere = '';           
            if (isset($store_id) && $store_id != "")
            {
                if(strpos($store_id, ',') !== false){
                    $strWhere = "usr.iStoreId IN ('" . str_replace(",", "','", $store_id) . "')";            
                }
                else{
                    $strWhere = "usr.iStoreId='$store_id'";
                }                
            }
            $strSql="SELECT 
                        usr.iIcetypeId AS usr_ice_type,
                        usr.iIceQuantityId AS usr_ice_quantity,
                        usr.iStoreId AS usr_store_id,
                        usr.eStatus AS usr_status,
                        usr.dtAddedAt AS usr_added_at,
                        usr.dtUpdatedAt AS usr_updated_at
                        FROM user_store_review AS usr
                        LEFT JOIN icetype AS icetype on (icetype.iIcetypeId = usr.iIcetypeId)
                        WHERE usr.iUserReviewId IN
                        (SELECT max(usr.iUserReviewId)
                            FROM user_store_review AS usr 
                         WHERE $strWhere AND usr.eStatus='Active' 
                         GROUP BY usr.iIcetypeId,usr.iStoreId) AND icetype.eStatus='Active'";

            $result_obj =  $this->db->query($strSql);
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $arrStoreId = array();           
            foreach($result_arr as $row)
            {
                $arrStoreId[$row['usr_store_id']][] = $row; // add each user id to the array
            }

            if (!is_array($arrStoreId) || count($arrStoreId) == 0)
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
        #echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $arrStoreId;
        return $return_arr;
    }
}
