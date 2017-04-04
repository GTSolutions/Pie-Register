<?php
global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls;
$_POST['act'] = !empty($_POST['act']) ? $_POST['act'] : (!empty($_GET['act']) && !isset($_POST['pagen']) ? $_GET['act'] : false );

if($_POST['act'] == "edit")
{
	if(isset($_GET['pie_id']) && !empty($wp_registered_widgets[$_GET['pie_id']])){
		if(empty($this->piereg_rw_widgets[$_GET['pie_id']]))
			$this->piereg_rw_widgets[$_GET['pie_id']] = $this->info['blank'];
		if(!empty($_POST['pr_ristrict_widget'])){
			unset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget']);
			foreach($_POST['pr_ristrict_widget'] as $keys=>$val){
				$title = explode('=', $val);
				$id = array_shift($title);
				$title = $title[0];
				if($title == "")
				{
					unset($this->piereg_rw_widgets[$_GET['pie_id']]);
					break;
				}
				elseif($id == "visibility_status" && $title == "Before Login"){
					unset($this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget']);
					$this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget'][$id] = $title;
					break;
				}
				else
				{
					$this->piereg_rw_widgets[$_GET['pie_id']]['pr_ristrict_widget'][$id] = $title;
				}
			}
		}
		update_option(PIEREGISTER_RW_OPTIONS, $this->piereg_rw_widgets);
		echo '<div class="updated"><p><strong>'.(__("Saved Settings","piereg")).'</strong></p></div>';
	}
}
?>