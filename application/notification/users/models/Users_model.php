<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Users Model
 *
 * @category notification
 *
 * @package users
 *
 * @subpackage models
 *
 * @module Users
 *
 * @class Users_model.php
 *
 * @path application\notification\users\models\Users_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 27.04.2020
 */

class Users_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
    }


    public function get_archived_users()
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS u");
            $this->db->join("missing_pets AS mi","mi.iUserId = u.iUserId","LEFT");
            $this->db->join("missing_pet_images AS mimg","mimg.iUserId = mi.iUserId","LEFT");
            $this->db->join("user_query AS uq","uq.iUserId = u.iUserId","LEFT");
            $this->db->join("user_query_images AS uqimg","uqimg.iUserQueryId = uq.iUserQueryId","LEFT");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("GROUP_CONCAT(mi.iMissingPetId)  AS missing_pet_id");
            $this->db->select("u.vEmail AS email");
            $this->db->select("u.vFirstName AS email_user_name");
            $this->db->select("u.vProfileImage AS profile_image");
            $this->db->select("GROUP_CONCAT( mimg.vImage ) as 'pet_images'");
            $this->db->select("GROUP_CONCAT( uqimg.vQueryImage ) as 'query_images'");
            $this->db->group_by("mimg.iUserId");
            $this->db->where_in("u.eStatus", array('Pending_delete'));

            $result_obj = $this->db->get();

            // user_profile
        //    echo "--".$this->db->last_query(); exit;

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


    public function delete_records_frm_connection_tables($request_arr = array())
    {
         try
        {
          $users_data = array();

            if(count($request_arr) > 0)
            {
                foreach ($request_arr as $key => $value) 
                {

                //  echo $value['u_user_id']."----";

                    $this->db->query("DELETE FROM abusive_reports WHERE iReportedBy='".$value['u_user_id']."' OR iReportedOn='".$value['u_user_id']."' ");

                   // echo $this->db->last_query();

                    $this->db->query("DELETE FROM notification WHERE iReceiverId='".$value['u_user_id']."' OR iSenderId='".$value['u_user_id']."'");

                    //echo $this->db->last_query();

                      $this->db->query("DELETE FROM tag_people WHERE iTagFrom='".$value['u_user_id']."' OR iTagTo='".$value['u_user_id']."' ");

                    //echo $this->db->last_query();

                    
                    $this->db->query("DELETE FROM messages WHERE iMessageFrom='".$value['u_user_id']."' OR iMessageTo='".$value['u_user_id']."' ");

                    //echo $this->db->last_query();
                    
                    $this->db->query("DELETE FROM comments WHERE iCommentFrom='".$value['u_user_id']."' ");

                    //echo $this->db->last_query();

                    $this->db->query("DELETE FROM blocked_user WHERE iBlockedFrom='".$value['u_user_id']."'  OR iBlockedTo='".$value['u_user_id']."' ");

                    //echo $this->db->last_query();

                    $this->db->query("DELETE FROM missing_pets WHERE iUserId='".$value['u_user_id']."' ");

                    //echo $this->db->last_query();

                    $this->db->query("DELETE FROM missing_pet_images WHERE iUserId='".$value['u_user_id']."' ");

                    //echo $this->db->last_query();
                                      
                    // $this->db->query("DELETE FROM users_profile_images WHERE iUserId='".$value['u_user_id']."'");

                    //echo $this->db->last_query();

                      $this->db->query("DELETE FROM user_query WHERE iUserId='".$value['u_user_id']."'");

                    //echo $this->db->last_query();

                    //  $this->db->query("UPDATE message SET iDelete_user_id='".$value['u_user_id']."' WHERE iSenderId = '".$value['u_user_id']."' OR iReceiverId= '".$value['u_user_id']."' ");

                    //echo $this->db->last_query();

                    $this->db->query("DELETE FROM users WHERE iUserId='".$value['u_user_id']."' AND eStatus='Pending_delete' ");

                   // echo $this->db->last_query();

                     $users_data[$key]['email'] = $value['email'];
                     $users_data[$key]['email_user_name'] = $value['email_user_name'];

                }


                if($this->db->affected_rows() > 0 )
                {
                     $success = 1;

                }
                else
                {
                     $success = 0;

                }
            }
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $users_data;

        return $return_arr;
    }
}
