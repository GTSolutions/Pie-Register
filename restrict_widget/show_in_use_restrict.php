<?php
$url = $this->pr_rw_admin_url . '&amp;act=edit&amp;pie_id=' . $_GET['pie_id'].'&amp;option='. $_GET['option'] . '&amp;page_pag=' . ( $_GET['page_pag']  = ( !empty($_GET['page_pag']) ? $_GET['page_pag'] : 0 ) );
if(!empty($_GET['delete_page']) && isset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget'][$_GET['delete_page']])){
	unset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget'][$_GET['delete_page']]);
	update_option(PIEREGISTER_RW_OPTIONS, $this->piereg_rw_widgets);
}

echo '<table class="piereg_rw_main_table">';
	echo '<tr>';
		echo '<td valign="top">';
			echo '<form action="', $url ,'" method="post">';
			$visibility_status = ((isset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget']['visibility_status']))?$this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget']['visibility_status']:"");
			?>
            <div style="width:100%;display:inline-block;">
            	<fieldset  style="padding:5px 5px;">
                    <label><input <?php echo (($visibility_status == "After Login") ? 'checked="checked"' : ""); ?> type="checkbox" value="visibility_status=After Login" id="pr_ristrict_widget_after_login" /><?php _e("Show to Logged in Users","piereg"); ?></label>
                    &nbsp;&nbsp;&nbsp;
                    <label><input <?php echo (($visibility_status == "Before Login") ? 'checked="checked"' : ""); ?> type="checkbox" value="visibility_status=Before Login" id="pr_ristrict_widget_before_login" /><?php _e("Show to Non-Logged in Users","piereg"); ?></label>
                    &nbsp;&nbsp;&nbsp;
                    <label><input <?php echo (($visibility_status == "") ? 'checked="checked"' : ""); ?> type="checkbox" value="visibility_status=" id="pr_ristrict_widget_off" /><?php _e("Show All Users","piereg"); ?></label>
                    
                    <input name="pr_ristrict_widget[]" type="hidden" value="visibility_status=" id="pr_ristrict_widget_off_hidden" />
                </fieldset>
            </div>
			<?php
			echo '<div style="max-height:400px;overflow:auto;display:block;display:none;" id="piereg_user_roles_area">';
			echo '<table class="widefat piereg_user_roles" style="width:100%;">';
				echo '<tbody>';
					$posts = get_posts(array(
						'numberposts'	=> -1,
						'offset'		=> 0,
						'post_type' 	=> 'page'
					));
					$alternative = true;
					global $wp_roles;
					$roles_array = array();
					$roles_array = $wp_roles->roles;
					asort($roles_array);
					
					foreach($roles_array as $key=>$val){
						if($alternative)
							$alternative = false;
						else
							$alternative = true;
						
						echo '<tr class="', $alternative ? 'alternate':false ,' author-self status-publish">';
							
							echo '<th class="check-column"><input name="pr_ristrict_widget[]" '.(isset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget'][$key]) ? 'checked="checked"' : false).'    '.(isset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget'][$key]) ? 'data_pr_rw_select="checked"' : 'data-pr_rw_select="unchecked"').' type="checkbox" id="'.$key.'" value="'.$key.'='.$val['name'].'" class="pie_register_rw_checkboxs" /></th>';
							
							echo '<td><label for="'.$key.'">',$val['name'],'</label></td>';
						
						echo '</tr>';
						
					}
				echo '</tbody>';
				echo '</table>';
				echo '<div style="padding:5px;">';
				echo '<span><a id="pieregister_rw_select_all" title="Select All User Roles">'.(__("Select All","piereg")).'</a></span>';
				echo "&nbsp;/&nbsp;";
				echo '<span><a id="pieregister_rw_unselect_all" title="Unselect All User Roles">'.(__("Unselect All","piereg")).'</a></span>';
				echo '</div>';
			echo '</div>';
			echo '<p class="submit" style="clear:both;margin-top:25px;">';
				echo '<input type="submit" value="'.(__("Save Changes","piereg")).'" />';
			echo '</p>';
			echo '</form>';
		echo '</td>';
	echo '</tr>';
echo '</table>';