<?php

class Piereg_Menu_Items_Visibility_Control {

	private static $instance = null;

	public function get_instance() {
		return null == self::$instance ? self::$instance = new self : self::$instance;
	}

	function __construct() {
		if( is_admin() ) {
			if( file_exists(dirname( __FILE__ ) . '/classes/piereg_walker-nav-menu.php') )
				require_once( dirname( __FILE__ ) . '/classes/piereg_walker-nav-menu.php' );
			
			add_filter( 'wp_edit_nav_menu_walker', array( &$this, 'piereg_edit_nav_menu_walker' ) );
			add_action( 'wp_nav_menu_item_custom_fields', array( &$this, 'piereg_option' ), 12, 4 );
			add_action( 'wp_update_nav_menu_item', array( &$this, 'piereg_update_option' ), 10, 3 );
			add_action( 'delete_post', array( &$this, 'piereg_remove_visibility_meta' ), 1, 3);
		} else {
			add_filter( 'wp_get_nav_menu_items', array( &$this, 'piereg_visibility_check' ), 10, 3 );
			add_action( 'init', array( &$this, 'piereg_clear_gantry_menu_cache' ) );
		}
	}
	
	function piereg_edit_nav_menu_walker( $walker ) {
		return 'piereg_Walker_Nav_Menu_Edit';
	}
	
	function piereg_option( $item_id, $item, $depth, $args ) { ?>
		<p class="field-visibility description description-wide">
        	
            <div class="piereg_user_roles_area" style="display:none;">
	            <select class="widefat code" multiple="multiple">
	            <?php
				global $wp_roles;
				$role = $wp_roles->roles;
				
				foreach($role as $value)
				{ 
					?>
                    <option value="<?php echo strtolower(str_replace(" ","",$value['name']));?>"<?php echo ($result == strtolower($value['name'])) ? 'selected="selected"' : ''; ?>><?php _e("Show to","piereg");echo " ".$value['name']; ?></option>
                    <?php
				}
				?>
				</select>
            </div>
            
			<label for="piereg-menu-item-visibility-<?php echo $item_id; ?>">
				<?php _e('Visibility Status',"piereg") ?>
                <?php $result = esc_html( get_post_meta( $item_id, '_menu_item_visibility', true ) ); ?>
				<select class="widefat code" id="piereg-menu-item-visibility-<?php echo $item_id ?>" name="piereg-menu-item-visibility[<?php echo $item_id; ?>]">
                	<option value="default" <?php echo ($result == "default")? 'selected="selected"' : '' ?>><?php _e('Default',"piereg") ?></option>
                	<option value="after_login" <?php echo ($result == "after_login")? 'selected="selected"' : '' ?>><?php _e("Show to Logged in Users","piereg") ?></option>
                	<option value="before_login" <?php echo ($result == "before_login")? 'selected="selected"' : '' ?>><?php _e("Show to Non-Logged in Users","piereg") ?></option>
                	
                    <?php
					global $wp_roles;
					$role = $wp_roles->roles;
					
					foreach($role as $value)
					{ 
						?>
						<option value="<?php echo strtolower(str_replace(" ","",$value['name']));?>"<?php echo ($result == strtolower($value['name'])) ? 'selected="selected"' : ''; ?>><?php _e("Show Only","piereg");echo " ".$value['name']; ?></option>
                        <?php
					}
					?>
                </select>
			</label>
		</p>
	<?php }

	function piereg_update_option( $menu_id, $menu_item_id, $args ) {
		$meta_value = get_post_meta( $menu_item_id, '_menu_item_visibility', true );
		$new_meta_value = stripcslashes( $_POST['piereg-menu-item-visibility'][$menu_item_id] );

		if( $new_meta_value == '') {
			delete_post_meta( $menu_item_id, '_menu_item_visibility', $meta_value );
		}
		elseif( $meta_value !== $new_meta_value ) {
			update_post_meta( $menu_item_id, '_menu_item_visibility', $new_meta_value );
		}
	}

	function piereg_visibility_check( $items, $menu, $args ) {
		$hidden_items = array();
					
		foreach( $items as $key => $item ) {
			$item_parent = get_post_meta( $item->ID, '_menu_item_menu_item_parent', true );
			
			$logic = get_post_meta( $item->ID, '_menu_item_visibility', true );
			
			if($logic == "default"){
				$visible = true;
			}
			elseif($logic == "after_login"){
				eval( '$visible = is_user_logged_in();' );
			}
			elseif($logic == "before_login"){
				eval( '$visible = !is_user_logged_in();' );
			}
			elseif($logic != "" ){
				eval( '$visible = in_array("'.$logic.'", $GLOBALS["current_user"]->roles);' );
			} 
			else{
				$visible = true;
			}
			
			if( ! $visible || isset( $hidden_items[$item_parent] ) ) { // also hide the children of unvisible items
				unset( $items[$key] );
				$hidden_items[$item->ID] = '1';
			}
		}
		
		return $items;
	}

	function piereg_remove_visibility_meta( $post_id ) {
		if( is_nav_menu_item( $post_id ) ) {
			delete_post_meta( $post_id, '_menu_item_visibility' );
		}
	}

	function piereg_clear_gantry_menu_cache() {
		if( class_exists( 'GantryWidgetMenu' ) ) {
			GantryWidgetMenu::clearMenuCache();
		}
	}
}

//Piereg_Menu_Items_Visibility_Control::get_instance();
$classVisibilityControl = new Piereg_Menu_Items_Visibility_Control();
$classVisibilityControl->get_instance();

class piereg_Walker_Nav_Menu_Edit_delete extends Walker_Nav_Menu {

	//function start_el(&$output, $item, $depth, $args) 
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0 )
	{
		$output = '';
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)'), $item->title );
		}

		$title = empty( $item->label ) ? $title : $item->label;
        
		$output .= '<li id="menu-item-'.$item_id.'" class="'.implode(' ', $classes ).'">';
			$output .= '<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title">'.esc_html( $title ).'</span>
					<span class="item-controls">
						<span class="item-type">'.esc_html( $item->type_label ).'</span>';
						$output .= '<a class="item-edit" id="edit-'.$item_id.'" title="'.__('Edit Menu Item').'" href="'.(( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ) ).'">'.__( 'Edit Menu Item' ).'</a>
					</span>
				</dt>
			</dl>';

			$output .= '<div class="menu-item-settings" id="menu-item-settings-'.$item_id.'">';
				if( 'custom' == $item->type && $item->title !== 'Page List'  ) : 
					$output .= '<p class="field-url description description-wide">
						<label for="edit-menu-item-url-'.$item_id.'">'.__( 'URL' ).'<br />
							<input type="text" id="edit-menu-item-url-'.$item_id.'" class="widefat code edit-menu-item-url" name="menu-item-url['.$item_id.']" value="'.esc_attr( $item->url ).'" />
						</label>
					</p>';
				endif; ?>
				<?php if( $item->title !== 'Page List'  ) : // for advanced listers, we don't need any options
                    $output .= '<p class="description description-thin">
                        <label for="edit-menu-item-title-'.$item_id.'">'.__( 'Navigation Label' ).'<br />
                            <input type="text" id="edit-menu-item-title-'.$item_id.'" class="widefat edit-menu-item-title" name="menu-item-title['.$item_id.']" value="'.esc_attr( $item->title ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="description description-thin">
                        <label for="edit-menu-item-attr-title-'.$item_id.'">'.__( 'Title Attribute' ).'<br />
                            <input type="text" id="edit-menu-item-attr-title-'.$item_id.'" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title['.$item_id.']" value="'.esc_attr( $item->post_excerpt ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="field-link-target description description-thin">
                        <label for="edit-menu-item-target-'.$item_id.'">'.__( 'Link Target' ).'<br />
                            <select id="edit-menu-item-target-'.$item_id.'" class="widefat edit-menu-item-target" name="menu-item-target['.$item_id.']">
                                <option value="" '.selected( $item->target, '').'>'.__('Same window or tab').'</option>
                                <option value="_blank" '.selected( $item->target, '_blank').'>'.__('New window or tab').'</option>
                            </select>
                        </label>
                    </p>';
                    $output .= '<p class="field-css-classes description description-thin">
                        <label for="edit-menu-item-classes-'.$item_id.'">'.__( 'CSS Classes (optional)' ).'<br />
                            <input type="text" id="edit-menu-item-classes-'.$item_id.'" class="widefat code edit-menu-item-classes" name="menu-item-classes['.$item_id.']" value="'.esc_attr( implode(' ', $item->classes ) ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="field-xfn description description-thin">
                        <label for="edit-menu-item-xfn-'.$item_id.'">'.__( 'Link Relationship (XFN)' ).'<br />
                            <input type="text" id="edit-menu-item-xfn-'.$item_id.'" class="widefat code edit-menu-item-xfn" name="menu-item-xfn['.$item_id.']" value="'.esc_attr( $item->xfn ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="field-description description description-wide">
                        <label for="edit-menu-item-description-'.$item_id.'">'.__( 'Description' ).'<br />
                            <textarea id="edit-menu-item-description-'.$item_id.'" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description['.$item_id.']">'.esc_html( $item->description ).'</textarea>
                            <span class="description">'.__('The description will be displayed in the menu if the current theme supports it.').'</span>
                        </label>
                    </p>';
				endif; ?>
				<?php
				do_action('wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args);
                $output .= '<div class="menu-item-actions description-wide submitbox">';
					if( 'custom' != $item->type ) : 
                            $output .= '<p class="link-to-original">'.__('Original : ').'<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a></p>';
					endif;
					
					$output .= '<a class="item-delete submitdelete deletion" id="delete-'.$item_id.'" href="'.
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
						),
						'delete-menu_item_' . $item_id
					).'">'.__('Remove').'</a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-'.$item_id.'" href="'.add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ).'#menu-item-settings-'.$item_id.'">'.__('Cancel').'</a>
				</div>';

				$output .= '
				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id['.$item_id.']" value="'.$item_id.'" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id['.$item_id.']" value="'.esc_attr( $item->object_id ).'" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object['.$item_id.']" value="'.esc_attr( $item->object ).'" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id['.$item_id.']" value="'.esc_attr( $item->menu_item_parent ).'" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position['.$item_id.']" value="'.esc_attr( $item->menu_order ).'" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type['.$item_id.']" value="'.esc_attr( $item->type ).'" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>';
        return $output;
	}
}
