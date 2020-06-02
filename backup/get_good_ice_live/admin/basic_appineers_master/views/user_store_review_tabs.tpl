<ul class="nav nav-tabs module-tab-container">
    <li <%if $module_name eq "user_store_review"%> class="active" <%/if%>>
        <a class="tab-item item-user_store_review" 
        <%if $mode eq "Add" && $module_name eq "user_store_review"%>
            title="<%$this->lang->line('GENERIC_ADD')%> <%$this->lang->line('USER_STORE_REVIEW_USER_STORE_REVIEW')%>"
        <%else%>
            title="<%$this->lang->line('GENERIC_EDIT')%> <%$this->lang->line('USER_STORE_REVIEW_USER_STORE_REVIEW')%>"
        <%/if%>
        <%if $module_name eq "user_store_review"%> 
            href="javascript://"
        <%else%> 
            href="<%$admin_url%>#<%$this->general->getAdminEncodeURL('basic_appineers_master/user_store_review/add')%>|mode|<%$mod_enc_mode['Update']%>|id|<%$this->general->getAdminEncodeURL($parID)%>" 
        <%/if%>
        >
        <%if $mode eq "Add" && $module_name eq "user_store_review"%>
            <%$this->lang->line('GENERIC_ADD')%> <%$this->lang->line('USER_STORE_REVIEW_USER_STORE_REVIEW')%>
        <%else%>
            <%$this->lang->line('GENERIC_EDIT')%> <%$this->lang->line('USER_STORE_REVIEW_USER_STORE_REVIEW')%>
        <%/if%>
    </a>
</li>
<li <%if $module_name eq "user_review_images"%> class="active" <%/if%>>
    <a class="tab-item item-user_review_images"  title="<%$this->lang->line('USER_STORE_REVIEW_IMAGES')%> <%$this->lang->line('GENERIC_LIST')%>" 
        <%if $module_name eq "user_review_images"%> 
            href="javascript://"
        <%elseif $module_name eq "user_store_review"%> 
            <%if $mode eq "Update"%>
                href="<%$admin_url%>#<%$this->general->getAdminEncodeURL('basic_appineers_master/review_images/index')%>|parMod|<%$this->general->getAdminEncodeURL('user_store_review')%>|parID|<%$this->general->getAdminEncodeURL($data['iUserQueryId'])%>"
            <%else%>
                href="javascript://" aria-disabled="true" 
            <%/if%>                    
        <%else%> 
            href="<%$admin_url%>#<%$this->general->getAdminEncodeURL('basic_appineers_master/review_images/index')%>|parMod|<%$this->general->getAdminEncodeURL('user_store_review')%>|parID|<%$this->general->getAdminEncodeURL($parID)%>" 
        <%/if%>
        >
        <%$this->lang->line('USER_STORE_REVIEW_review_images')%>
    </a>
</li>
</ul>            