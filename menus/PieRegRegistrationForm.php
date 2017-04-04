<?php $piereg = get_option(OPTION_PIE_REGISTER); ?>
<div class="pieregister-admin" style="width:99%;overflow:hidden;">
  <div id="container">
    <div class="right_section">
      <div class="settings pie_wrap" style="padding-bottom:0px;">
        <h2>
          <?php _e("Pie-Register Registration Form",'piereg') ?>
        </h2>
        <?php
	   if( isset($_POST['error_message']) && !empty( $_POST['error_message'] ) )
			echo '<div style="clear: both;float: none;"><p class="error">' . $_POST['error_message']  . "</p></div>";
	   if(isset( $_POST['success_message'] ) && !empty( $_POST['success_message'] ))
			echo '<div style="clear: both;float: none;"><p class="success">' . $_POST['success_message']  . "</p></div>";
		?>
      </div>
      <div class="settings" style="padding-bottom:0px;">
        <div class="pieHelpTicket">
          	<?php 
			$newformurl 	= "javascript:;";
			$disableanchor 	= "button-disaled";
			$titlehover 	= 'title="With Pie Register Pro you can have an unlimited number of registration forms."';
			if( $this->piereg_pro_is_activate )
			{	
				$newformurl 	= "admin.php?page=pr_new_registration_form";
				$disableanchor 	= "button-primary";
				$titlehover 	= "";
			}
			?>
            	<a 
            	href="<?php echo $newformurl; ?>" 
                <?php echo $titlehover; ?> 
                class="button button-large <?php echo $disableanchor; ?>">
                &nbsp; <?php _e("Add New","piereg"); ?> &nbsp;
                </a>
		</div>
      </div>
      <div class="pieHelpTicket">
        <div class="settings" style="padding-bottom:0px;">
          <table cellspacing="0" class="piereg_form_table">
            <thead>
              <tr>
                <th></th>
                <th><?php _e("ID","piereg"); ?></th>
                <th><?php _e("Registration form Title","piereg"); ?></th>
                <th><?php _e("Views","piereg"); ?></th>
                <th><?php _e("Submissions","piereg"); ?></th>
                <th><?php _e("Shortcode","piereg"); ?></th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th>
                <th><?php _e("ID","piereg"); ?></th>
                <th><?php _e("Registration form Title","piereg"); ?></th>
                <th><?php _e("Views","piereg"); ?></th>
                <th><?php _e("Submissions","piereg"); ?></th>
                <th><?php _e("Shortcode","piereg"); ?></th>
              </tr>
            </tfoot>
            <tbody class="">
              <?php
if( $this->piereg_pro_is_activate )
{
	$option_asda = get_option("piereg_form_field_option_10");
	
}
$fields_id = get_option("piereg_form_fields_id");
$form_on_free	= get_option("piereg_form_free_id");

$count = 0;
for($a=1;$a<=$fields_id;$a++)
{
	$option = get_option("piereg_form_field_option_".$a);
	if( !empty($option) && is_array($option) && isset($option['Id']) && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
	{			
		?>
              <tr>
                <td><?php if(trim($option['Status']) != "" and $option['Status'] == "enable"): ?>
                  <a href="admin.php?page=pie-register&prfrmid=<?php echo $option['Id']; ?>&status=disenable"> <img title="Deactivate" alt="Deactivate" src="<?php echo plugins_url("../images/active1.png",__FILE__); ?>"> </a>
                  <?php else:  ?>
                  <a href="admin.php?page=pie-register&prfrmid=<?php echo $option['Id']; ?>&status=enable"> <img title="Activate" alt="Activate" src="<?php echo plugins_url("../images/active0.png",__FILE__); ?>"> </a>
                  <?php endif; ?></td>
                <td class="column-id"><?php echo $option['Id']; ?></td>
                <td class="column-title"><strong><?php echo $option['Title']; ?></strong>
                  <div class="piereg-actions"> <span class="edit"><a class="underlinenone" href="admin.php?page=pr_new_registration_form&form_id=<?php echo $option['Id']; ?>&form_name=<?php echo str_replace(" ","_",$option['Title']); ?>" title="Edit this form">Edit</a> | </span> <span class="edit"><a onclick="javascript:confrm_box('Are you sure you want to delete this form?','admin.php?page=pie-register&prfrmid=<?php echo $option['Id']; ?>&action=delete');" title="Delete this form">Delete</a> | </span> <span class="edit"><a class="underlinenone" href="<?php echo get_bloginfo("url")?>/registration/?pr_preview=1&form_id=<?php echo $option['Id']; ?>&prFormId=<?php echo $option['Id']; ?>&form_name=<?php echo str_replace(" ","_",$option['Title']); ?>" target="_blank" title="Preview this form">Preview</a></span> </div></td>
                <td class="column-date"><strong><?php echo $option['Views'] ?></strong></td>
                <td class="column-date"><strong><?php echo $option['Entries']; ?></strong></td>
                <td class="column-date" ><div class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_<?php echo $option['Id']; ?>')" id="piereg-select-all-text-onclick_<?php echo $option['Id']; ?>" readonly="readonly"><?php echo '[pie_register_form id="'.$option['Id'].'" title="true" description="true" ]' ?></div></td>
              </tr>
              <?php 
		$count++;
		
		if( $count == 1 && !$this->piereg_pro_is_activate )
		{
			if( !$form_on_free )
			{
				update_option('piereg_form_free_id', $option['Id']);
				$form_on_free .= $option['Id'];
			}
			break;
		}
	}
}
			if($count == 0){ ?>
              <tr>
                <td colspan="6"><h3>
                    <?php _e("No Registration Form Found","piereg"); ?>
                  </h3></td>
              </tr>
            <?php }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>