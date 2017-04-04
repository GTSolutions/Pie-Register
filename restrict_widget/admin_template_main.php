<div class="piereg_clear"></div>
<div class="pr_rw_breadcrums"><?php _e("Custom widgets","piereg"); ?></div>

<div class="restrict_widgets_Legend_area" style="width:100%;">
	<div class="restrict_widgets_Legend_area_left">
        <h3><?php _e("Widgets","piereg"); ?></h3>
        <div style="font-weight:normal;margin-bottom:20px;"><?php _e("Select a widget from the list that you want to customize","piereg"); ?></div>
        <?php if(!empty($this->piereg_rw_widgets)){ ?>
		    <a class="piereg_reset_all_widgets" onclick="if(!confirm('<?php _e("Are you sure you want to reset all widgets?","piereg"); ?>')) return false;" href="<?php echo $this->pr_rw_admin_url ?>&piereg_reset_widgets"><input type="button" value="<?php _e("Reset all widgets","piereg"); ?>" /></a>
        <?php } ?>
    </div>
    <div class="restrict_widgets_Legend_area_right">
        <h3><?php _e("Legend","piereg"); ?></h3>
        <div class="piereg_legend_area" style="font-weight:normal;margin-top:10px; display:inline-block;">
            <div class="pr_blue"></div> &nbsp; - <?php _e("Normal Widget","piereg"); ?>
            <div style="clear:both;height:10px;"></div>
            <div class="pr_red"></div> &nbsp; - <?php _e("Customized Widget","piereg"); ?>
        </div>
    </div>
</div>


<table class="form-table">
	<tr>
		<td>
			<?php
				global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls;
				$sidebars = wp_get_sidebars_widgets();
				if(!$sidebars)
					echo '<div class="piereg_rw_list">'.(__("No widgets registered. Please setup at least one widget via wordpress","piereg")).' <a href="widgets.php">'.(__("widgets control panel","piereg")).'</a>('.(__("found under the Design tab","piereg")).')</div>';
				else
					{
					echo '<ul class="piereg_rw_list">';
					
						foreach($sidebars as $sidebar_id => $widgets){
							
							if(empty($wp_registered_sidebars[$sidebar_id]['name']))
								continue;
							
							echo '<li class="widget-list-control-item">';							
								echo '<h4 class="widget-title ', !empty($wp_registered_sidebars[$sidebar_id]['name']) ? false : 'not_registered' ,'">',!empty($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : (__("Not registered","piereg")),'</h4>';								
								echo '<ul>';
									
									$texts = get_option('widget_text');
									if (is_array($widgets) && count($widgets) > 0){
										foreach($widgets as $widget_id){
										
										$has_limit = false;
										if(!empty($this->piereg_rw_widgets[$widget_id]))
											foreach($this->piereg_rw_widgets[$widget_id] as $group => $data )
												if(!empty($data))
													if($group != 'opts'){
														$has_limit = true;
														break;
														}
													else
														foreach($data as $opt)
															if(!empty($opt)){
																$has_limit = true;
																break;
																}
										
										echo '<li><a style="', $has_limit ? 'color:red;' : false ,'" href="',$this->pr_rw_admin_url,'&act=edit&pie_id=',$wp_registered_widgets[$widget_id]['id'],'&option=use_restrict">';
											
											if($wp_registered_widgets[$widget_id]['callback'] == 'wp_widget_text' && !empty($wp_registered_widgets[$widget_id]['params'][0]['number']) && !empty($texts[$wp_registered_widgets[$widget_id]['params'][0]['number']])){
												echo (__("Text widget","piereg")).': ', !empty($texts[$wp_registered_widgets[$widget_id]['params'][0]['number']]['title']) ? $texts[$wp_registered_widgets[$widget_id]['params'][0]['number']]['title'] : htmlentities(array_shift(explode(PHP_EOL, wordwrap($texts[$wp_registered_widgets[$widget_id]['params'][0]['number']]['text'], 15, " ...\r\n", true))));
												
												}
											else
												echo !empty($wp_registered_widgets[$widget_id]['name']) ? $wp_registered_widgets[$widget_id]['name'] : $wp_registered_widgets[$widget_id]['id'];
										
										echo '</a></li>';
									
										}
									}
								echo '</ul>';
								
							echo '</li>';						
							
							}
						
					echo '</ul>';
					}
			?>			
		</td>
	</tr>
</table>