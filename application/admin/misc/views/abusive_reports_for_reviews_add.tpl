<%if $this->input->is_ajax_request()%>
<%$this->js->clean_js()%>
<%/if%>
<div class="module-form-container">
<%include file="abusive_reports_for_reviews_add_strip.tpl"%>
<div class="<%$module_name%>" data-form-name="<%$module_name%>">
<div id="ajax_content_div" class="ajax-content-div top-frm-spacing" >
<input type="hidden" id="projmod" name="projmod" value="abusive_reports_for_posts" />
<!-- Page Loader -->
<div id="ajax_qLoverlay"></div>
<div id="ajax_qLbar"></div>
<!-- Module Tabs & Top Detail View -->
<div class="top-frm-tab-layout" id="top_frm_tab_layout">
</div>
<!-- Middle Content -->
<div id="scrollable_content" class="scrollable-content popup-content top-block-spacing ">
    <div id="abusive_reports_for_posts" class="frm-module-block frm-elem-block frm-stand-view">
        <!-- Module Form Block -->
        <form name="frmaddupdate" id="frmaddupdate" action="<%$admin_url%><%$mod_enc_url['add_action']%>?<%$extra_qstr%>" method="post"  enctype="multipart/form-data">
            <!-- Form Hidden Fields Unit -->
            <input type="hidden" id="id" name="id" value="<%$enc_id%>" />
            <input type="hidden" id="mode" name="mode" value="<%$mod_enc_mode[$mode]%>" />
            <input type="hidden" id="ctrl_prev_id" name="ctrl_prev_id" value="<%$next_prev_records['prev']['id']%>" />
            <input type="hidden" id="ctrl_next_id" name="ctrl_next_id" value="<%$next_prev_records['next']['id']%>" />
            <input type="hidden" id="draft_uniq_id" name="draft_uniq_id" value="<%$draft_uniq_id%>" />
            <input type="hidden" id="extra_hstr" name="extra_hstr" value="<%$extra_hstr%>" />
            <!-- Form Dispaly Fields Unit -->
            <div class="main-content-block " id="main_content_block">
                <div style="width:98%" class="frm-block-layout pad-calc-container">
                    <div class="box gradient <%$rl_theme_arr['frm_stand_content_row']%> <%$rl_theme_arr['frm_stand_border_view']%>">
                        <div class="title <%$rl_theme_arr['frm_stand_titles_bar']%>"><h4><%$this->lang->line('ABUSIVE_REPORTS_FOR_REVIEWS_ABUSIVE_REPORTS_FOR_REVIEWS')%></h4></div>
                        <div class="content <%$rl_theme_arr['frm_stand_label_align']%>">
                            <div class="form-row row-fluid " id="cc_sh_arfp_reported_by">
                                <label class="form-label span3 ">
                                    <%$form_config['arfp_reported_by']['label_lang']%>
                                </label> 
                                <div class="form-right-div  <%if $mode eq 'Update'%>frm-elements-div<%/if%> ">
                                    <%assign var="opt_selected" value=$data['arfp_reported_by']%>
                                    <%if $mode eq "Update"%>
                                        <input type="hidden" name="arfp_reported_by" id="arfp_reported_by" value="<%$data['arfp_reported_by']%>" class="ignore-valid"/>
                                        <%assign var="combo_arr" value=$opt_arr["arfp_reported_by"]%>
                                        <%assign var="opt_display" value=$this->general->displayKeyValueData($opt_selected, $combo_arr)%>
                                        <span class="frm-data-label">
                                            <strong>
                                                <%if $opt_display neq ""%>
                                                    <%$opt_display%>
                                                <%else%>
                                                <%/if%>
                                            </strong></span>
                                        <%else%>
                                            <%$this->dropdown->display("arfp_reported_by","arfp_reported_by","  title='<%$this->lang->line('ABUSIVE_REPORTS_FOR_MISSING_PETS_REPORTED_BY')%>'  aria-chosen-valid='Yes'  class='chosen-select frm-size-medium'  data-placeholder='<%$this->general->parseLabelMessage('GENERIC_PLEASE_SELECT__C35FIELD_C35' ,'#FIELD#', 'ABUSIVE_REPORTS_FOR_MISSING_PETS_REPORTED_BY')%>'  ", "|||", "", $opt_selected,"arfp_reported_by")%>
                                        <%/if%>
                                    </div>
                                    <div class="error-msg-form "><label class='error' id='arfp_reported_byErr'></label></div>
                                </div>
                                <div class="form-row row-fluid " id="cc_sh_owner_first_name">
                                     <label class="form-label span3 ">
                                        <%$form_config['owner_first_name']['label_lang']%>
                                    </label> 
                                    <div class="form-right-div  <%if $mode eq 'Update'%>frm-elements-div<%/if%> ">
                                        <%if $mode eq "Update"%>
                                            <input type="hidden" class="ignore-valid" name="owner_first_name" id="owner_first_name" value="<%$data['owner_first_name']|@htmlentities%>" />
                                             <span class="frm-data-label">
                                                <strong>
                                                    <%if $data['owner_first_name'] neq ""%>
                                                        <%$data['owner_first_name']%>
                                                    <%else%>
                                                    <%/if%>
                                                </strong></span> 
                                            <%else%>
                                                <input type="text" placeholder="" value="<%$data['owner_first_name']|@htmlentities%>" name="owner_first_name" id="owner_first_name" title="<%$this->lang->line('ABUSIVE_REPORTS_FOR_MISSING_OWNER')%>"  data-ctrl-type='textbox'  class='frm-size-medium'  />
                                            <%/if%>
                                        </div>
                                        <div class="error-msg-form "><label class='error' id='arfp_abusive_reports_for_missing_pets_idErr'></label></div>
                                </div>
                                <div class="form-row row-fluid " id="cc_sh_arfp_reviews_id">
                                     <label class="form-label span3 ">
                                        <%$form_config['arfp_reviews_id']['label_lang']%>
                                    </label> 
                                    <div class="form-right-div  <%if $mode eq 'Update'%>frm-elements-div<%/if%> ">
                                        <%if $mode eq "Update"%>
                                            <input type="hidden" class="ignore-valid" name="arfp_reviews_id" id="arfp_reviews_id" value="<%$data['arfp_reviews_id']|@htmlentities%>" />
                                             <span class="frm-data-label">
                                                <strong>
                                                    <%if $data['arfp_reviews_id'] neq ""%>
                                                        <%$data['arfp_reviews_id']%>
                                                    <%else%>
                                                    <%/if%>
                                                </strong></span> 
                                            <%else%>
                                                <input type="hidden" placeholder="" value="<%$data['arfp_reviews_id']|@htmlentities%>" name="arfp_reviews_id" id="arfp_reviews_id" title="<%$this->lang->line('ABUSIVE_REPORTS_FOR_MISSING_PET_ID')%>"  data-ctrl-type='textbox'  class='frm-size-medium'  />
                                            <%/if%>
                                        </div>
                                        <div class="error-msg-form "><label class='error' id='arfp_abusive_reports_for_missing_pets_idErr'></label></div>
                                </div>    
                                    
                                                            
                                   
                                    <div class="form-row row-fluid " id="cc_sh_arfp_post_id">
                                    <label class="form-label span3 ">
                                        <%$form_config['p_post_title']['label_lang']%>
                                    </label> 
                                    <div class="form-right-div  <%if $mode eq 'Update'%>frm-elements-div<%/if%> ">
                                       <input type="text" placeholder="" value="<%$data['p_post_title']|@htmlentities%>" name="p_post_title" id="p_post_title" title="<%$this->lang->line('ABUSIVE_REPORTS_FOR_REVIEWS_MESSAGE')%>"  data-ctrl-type='textbox'  class='frm-size-medium'  />
                                         
                                    </div>
                                        <div class="error-msg-form "><label class='error' id='arfp_abusive_reports_for_review_idErr'></label></div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_arfp_message">
                                        <label class="form-label span3 ">
                                            <%$form_config['arfp_message']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div  <%if $mode eq 'Update'%>frm-elements-div<%/if%> ">
                                            <%if $mode eq "Update"%>
                                                <input type="hidden" class="ignore-valid" name="arfp_message" id="arfp_message" value="<%$data['arfp_message']|@htmlentities%>" />
                                                <span class="frm-data-label">
                                                    <strong>
                                                        <%if $data['arfp_message'] neq ""%>
                                                            <%$data['arfp_message']%>
                                                        <%else%>
                                                        <%/if%>
                                                    </strong></span>
                                                <%else%>
                                                    <input type="text" placeholder="" value="<%$data['arfp_message']|@htmlentities%>" name="arfp_message" id="arfp_message" title="<%$this->lang->line('ABUSIVE_REPORTS_FOR_REVIEW_MESSAGE')%>"  data-ctrl-type='textbox'  class='frm-size-medium'  />
                                                <%/if%>
                                            </div>
                                            <div class="error-msg-form "><label class='error' id='arfp_messageErr'></label></div>
                                        </div>
                                        <div class="form-row row-fluid " id="cc_sh_arfp_added_at">
                                            <label class="form-label span3 ">
                                                <%$form_config['arfp_added_at']['label_lang']%>
                                            </label> 
                                            <div class="form-right-div  <%if $mode eq 'Update'%>frm-elements-div<%else%>input-append text-append-prepend<%/if%> ">
                                                <%if $mode eq "Update"%>
                                                    <input type="hidden" name="arfp_added_at" id="arfp_added_at" value="<%$this->general->dateSystemFormat($data['arfp_added_at'])%>" class="ignore-valid view-label-only"  data-ctrl-type='date'  class='frm-datepicker ctrl-append-prepend frm-size-medium'  aria-date-format='<%$this->general->getAdminJSFormats('date', 'dateFormat')%>'  aria-format-type='date' />
                                                    <%assign var="display_date" value=$this->general->dateSystemFormat($data['arfp_added_at'])%>
                                                    <span class="frm-data-label">
                                                        <strong>
                                                            <%if $display_date neq ""%>
                                                                <%$display_date%>
                                                            <%else%>
                                                            <%/if%>
                                                        </strong></span>
                                                    <%else%>
                                                        <input type="text" value="<%$this->general->dateSystemFormat($data['arfp_added_at'])%>" placeholder="" name="arfp_added_at" id="arfp_added_at" title="<%$this->lang->line('ABUSIVE_REPORTS_FOR_REVIEW_REPORTED_ON')%>"  data-ctrl-type='date'  class='frm-datepicker ctrl-append-prepend frm-size-medium'  aria-date-format='<%$this->general->getAdminJSFormats('date', 'dateFormat')%>'  aria-format-type='date'  />
                                                        <span class='add-on text-addon date-append-class icomoon-icon-calendar'></span>
                                                    <%/if%>
                                                </div>
                                                <div class="error-msg-form "><label class='error' id='arfp_added_atErr'></label></div>
                                            </div>
                                            <div class="form-row row-fluid " id="cc_sh_status">
                                                <label class="form-label span3 ">
                                                    <%$form_config['status']['label_lang']%>
                                                </label> 
                                                <div class="form-right-div  ">
                                                    <%assign var="opt_selected" value=$data['status']%>
                                                    <%$this->dropdown->display("status","status","  title='<%$this->lang->line('ABUSIVE_REPORTS_FOR_REVIEWS_STATUS')%>'  aria-chosen-valid='Yes'  class='chosen-select frm-size-medium'  data-placeholder='<%$this->general->parseLabelMessage('GENERIC_PLEASE_SELECT__C35FIELD_C35' ,'#FIELD#', 'ABUSIVE_REPORTS_FOR_REVIEWS_STATUS')%>'  ", "|||", "", $opt_selected,"status")%>
                                                </div>
                                                <div class="error-msg-form "><label class='error' id='statusErr'></label></div>
                                            </div>

                                             </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div class="frm-bot-btn <%$rl_theme_arr['frm_stand_action_bar']%> <%$rl_theme_arr['frm_stand_action_btn']%> popup-footer">
                                    <%if $rl_theme_arr['frm_stand_ctrls_view'] eq 'No'%>
                                        <%assign var='rm_ctrl_directions' value=true%>
                                    <%/if%>
                                    <%include file="abusive_reports_for_reviews_add_buttons.tpl"%>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Module Form Javascript -->
<%javascript%>

var el_form_settings = {}, elements_uni_arr = {}, child_rules_arr = {}, google_map_json = {}, pre_cond_code_arr = [];
el_form_settings['module_name'] = '<%$module_name%>'; 
el_form_settings['extra_hstr'] = '<%$extra_hstr%>';
el_form_settings['extra_qstr'] = '<%$extra_qstr%>';
el_form_settings['upload_form_file_url'] = admin_url+"<%$mod_enc_url['upload_form_file']%>?<%$extra_qstr%>";
el_form_settings['get_chosen_auto_complete_url'] = admin_url+"<%$mod_enc_url['get_chosen_auto_complete']%>?<%$extra_qstr%>";
el_form_settings['token_auto_complete_url'] = admin_url+"<%$mod_enc_url['get_token_auto_complete']%>?<%$extra_qstr%>";
el_form_settings['tab_wise_block_url'] = admin_url+"<%$mod_enc_url['get_tab_wise_block']%>?<%$extra_qstr%>";
el_form_settings['parent_source_options_url'] = "<%$mod_enc_url['parent_source_options']%>?<%$extra_qstr%>";
el_form_settings['jself_switchto_url'] =  admin_url+'<%$switch_cit["url"]%>';
el_form_settings['callbacks'] = [];

google_map_json = $.parseJSON('<%$google_map_arr|@json_encode%>');
child_rules_arr = {};

<%if $auto_arr|@is_array && $auto_arr|@count gt 0%>
setTimeout(function(){
<%foreach name=i from=$auto_arr item=v key=k%>
    if($("#<%$k%>").is("select")){
        $("#<%$k%>").ajaxChosen({
            dataType: "json",
            type: "POST",
            url: el_form_settings.get_chosen_auto_complete_url+"&unique_name=<%$k%>&mode=<%$mod_enc_mode[$mode]%>&id=<%$enc_id%>"
            },{
            loadingImg: admin_image_url+"chosen-loading.gif"
        });
    }
<%/foreach%>
}, 500);
<%/if%>        
el_form_settings['jajax_submit_func'] = '';
el_form_settings['jajax_submit_back'] = '';
el_form_settings['jajax_action_url'] = '<%$admin_url%><%$mod_enc_url["add_action"]%>?<%$extra_qstr%>';
el_form_settings['save_as_draft'] = 'No';
el_form_settings['buttons_arr'] = [];
el_form_settings['message_arr'] = {
"delete_message" : "<%$this->general->processMessageLabel('ACTION_ARE_YOU_SURE_WANT_TO_DELETE_THIS_RECORD_C63')%>"
};

callSwitchToSelf();
<%/javascript%>
<%$this->js->add_js('admin/abusive_reports_for_posts_add_js.js')%>

<%$this->js->add_js("admin/custom/hide_form_buttons.js")%>
<%if $this->input->is_ajax_request()%>
<%$this->js->js_src()%>
<%/if%> 
<%if $this->input->is_ajax_request()%>
<%$this->css->css_src()%>
<%/if%> 
<%javascript%>
Project.modules.abusive_reports_for_posts.callEvents();
<%/javascript%>