<?php
if( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	
class PieRedirectSettings extends WP_List_Table
{
    private $order;
    private $orderby;
    public function __construct()
    {
        parent :: __construct( array(
            'singular' => 'pieregister-redirect',
            'ajax'     => true
        ) );
    }
    public function get_sql_results($fields = "")
    {
        global $wpdb;
		$prefix=$wpdb->prefix."pieregister_";
		$codetable=$prefix."redirect_settings";
		if(empty($fields))
			$fields = "`id`, `user_role`, `logged_in_url`, `logged_in_page_id`, `log_out_url`, `log_out_page_id`, `status`";
		
		$sql_results = $wpdb->get_results("SELECT {$fields}
                FROM `$codetable`
                ORDER BY `$this->orderby` $this->order");
		
        return $sql_results;
    }
    public function set_order()
    {
        $order = 'ASC';
        if ( isset( $_GET['order'] ) && $_GET['order'] )
            $order = $_GET['order'];
        $this->order = esc_sql( $order );
    }
    public function set_orderby()
    {
        $orderby = 'id';
        if ( isset( $_GET['orderby'] ) && $_GET['orderby'] )
            $orderby = $_GET['orderby'];
        $this->orderby = esc_sql( $orderby );
    }
    public function ajax_user_can() 
    {
        return current_user_can( 'edit_posts' );
    }
    public function no_items() 
    {
         _e( 'Record Not Found', "piereg" );
    }
    public function get_views()
    {
        return array();
    }
    public function get_columns()
    {
        $columns = array(
			'id'         => __( '#' ),
            'user_role' => __( 'User Role',"piereg" ),
            'logged_in_page_id'  => __( 'After Login Page' ,"piereg"),
            'log_out_page_id'  => __( 'After Logout Page' ,"piereg"),
            'status'  => __( '',"piereg" )
        );
        return $columns;        
    }
    public function get_sortable_columns()
    {
        $sortable = array(
            'id'         => array( 'id', true ),
            'user_role' => array( 'user_role', true )
        );
        return $sortable;
    }
    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( 
            $columns,
            $hidden,
            $sortable 
        );
        // SQL results
        $posts = $this->get_sql_results();
        empty( $posts ) AND $posts = array();
        # >>>> Pagination
		
        $per_page_item = (isset($_POST['invitation_code_per_page_items']))? intval($_POST['invitation_code_per_page_items']) : 10;
		$per_page     = $per_page_item;
        $current_page = $this->get_pagenum();
        $total_items  = count( $posts );
        $this->set_pagination_args( array (
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ) );
        $last_post = $current_page * $per_page;
        $first_post = $last_post - $per_page + 1;
        $last_post > $total_items AND $last_post = $total_items;
        // Setup the range of keys/indizes that contain 
        // the posts on the currently displayed page(d).
        // Flip keys with values as the range outputs the range in the values.
        $range = array_flip( range( $first_post - 1, $last_post - 1, 1 ) );
        // Filter out the posts we're not displaying on the current page.
        $posts_array = array_intersect_key( $posts, $range );
        # <<<< Pagination
        // Prepare the data
        $permalink = __( 'Edit:' );
		$id = 1;
        foreach ( $posts_array as $key => $post )
        {
            $link     = "#";
            $no_title = __( 'No title set',"piereg" );
            $title    = !$post->user_role ? "<em>{$no_title}</em>" : $post->user_role;
			$post_name = $post->user_role;
			global $wp_roles;
			$user_role_value = $wp_roles->roles[$post->user_role]['name'];
			/*code name*/
			$e_title = __( 'Click Here To Edit',"piereg" );
			//Page Id
			$page_id = ((isset($_GET['paged']) && !empty($_GET['paged']))?"&paged=".intval($_GET['paged']):"");
			//Order By
			$orderby = ((isset($_GET['orderby']) && !empty($_GET['orderby']))?"&orderby=".$_GET['orderby']:"");
			//Ordered
			$order = ((isset($_GET['order']) && !empty($_GET['order']))?"&order=".$_GET['order']:"");
			//User Role
			$posts[ $key ]->user_role = '<span><a href="'.admin_url("admin.php?page=pie-settings&tab=pages&subtab=role-based&action=edit&pie_id=".$posts[ $key ]->id.$page_id.$orderby.$order).'" title="'.__("Click here to edit","piereg").'">'.$user_role_value.'</a></span>';
			//Logged In URL
			$posts[ $key ]->logged_in_url = '<span>'.urldecode($posts[ $key ]->logged_in_url).'</span>';
			
			//Log In Page Tiitle
			if( $posts[ $key ]->logged_in_page_id == 0 ) {
				$posts[ $key ]->logged_in_page_id = '<span>'.urldecode($posts[ $key ]->logged_in_url).'</span>';
			} else {
				$posts[ $key ]->logged_in_page_id = '<span>'.get_the_title($posts[ $key ]->logged_in_page_id).'</span>';
			}
			//Log Out URL
			$posts[ $key ]->log_out_url = '<span>'.urldecode($posts[ $key ]->log_out_url).'</span>';
			//Log Out Page Title
			if( $posts[ $key ]->log_out_page_id == 0 ) {
				$posts[ $key ]->log_out_page_id = '<span>'.urldecode($posts[ $key ]->log_out_url).'</span>';
			} else {
				$posts[ $key ]->log_out_page_id = '<span>'.get_the_title($posts[ $key ]->log_out_page_id).'</span>';
			}
			
			$class = (isset($post->status) && intval($post->status) == 1) ? "active"  : "inactive";
			$title = ($post->status==0)? __("Enable","piereg") : __("Disable","piereg");
			//Status
			$posts[ $key ]->status = '<a herf="javascript:;" onclick="changeStatus(\''.$post->id.'\',\''.$user_role_value.'\',\''.$title.'\');" title="'.__($title,"piereg").'" class="'.$class.'"></a>';
			$posts[ $key ]->status = '<a class="delete" href="javascript:;" onclick="confirmDel(\''.$post->id.'\',\''.$user_role_value.'\');" title="'.__("Delete","piereg").'"></a>';
			
			$id++;
        }
        $this->items = $posts_array;
    }
    public function column_default( $item, $column_name )
    {
        return $item->$column_name;
    }
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php //Bulk option here ?>
            </div>
             
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="piereg_clear" />
        </div>
        <?php 
    }
    public function extra_tablenav( $which )
    {
        global $wp_meta_boxes;
        $views = $this->get_views();
        if ( empty( $views ) )
            return;
        $this->views();
    }
}