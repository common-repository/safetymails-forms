<?php

if (! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SafetyMail_Form_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'form', 'sp' ),
            'plural'   => __( 'forms', 'sp' ),
            'ajax'     => false
        ] );
    }

    /**
     * Retrieve form’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_forms( $per_page = 5, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}safety_forms";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
     * Retrieve form data from the database
     *
     * @param int $id
     *
     * @return mixed
     */
    public static function get_form( $id ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}safety_forms where id=$id LIMIT 1";

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return empty($result) ? $result : $result[0];
    }

    /**
     * Delete a form record.
     *
     * @param int $id form ID
     */
    public static function delete_form( $id ) {
        global $wpdb;
        $table = "{$wpdb->prefix}safety_forms";

        $wpdb->delete(
            $table,
            ["id" => $id]
        );
    }

    /**
    * Returns the count of records in the database.
    *
    * @return null|string
    */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}safety_forms";

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no form data is available */
    public function no_items() {
        echo 'Nenhum formulário encontrado.';
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'sp_delete_form' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'delete' => sprintf(
                '<a href="?page=%s&action=%s&form=%s&_wpnonce=%s">%s</a>',
                esc_attr( $_REQUEST['page'] ),
                'delete',
                absint( $item['id'] ),
                $delete_nonce,
                __('Delete', 'safetymail-form')
            ),
            'edit' => sprintf(
                '<a href="?page=%s&id=%s">%s</a>',
                'safetymail-form-edit',
                absint( $item['id'] ),
                __('Edit', 'safetymail-form')
            )
        ];

        return $title . $this->row_actions( $actions );
    }

    /**
     * Method for code column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_code( $item ) {

        $title = "<span id=\"shortcode{$item['id']}\">{$item['code']}</span>";
        $title .= "<a href=\"#\" title=\"Copiar código\"class=\"code-copy\" data-target=\"shortcode{$item['id']}\"><i class=\"far fa-copy\"></i></a>";

        return $title;
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item['id']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'name'    => __('Name', 'safetymail-form'),
            'code' => __('Shortcode', 'safetymail-form'),
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
            'code' => array( 'code', false ),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __('Delete', 'safetymail-form')
        ];

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'forms_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page
        ] );


        $this->items = self::get_forms( $per_page, $current_page );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'sp_delete_form' ) ) {
                die( 'Go get a life script kiddies' );
            } else {
                self::delete_form( absint( $_GET['form'] ) );
                wp_redirect( esc_url( add_query_arg() ) );
            }
        }

        // If the delete bulk action is triggered
        if (( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {
            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_form( $id );
            }
            wp_redirect( esc_url( add_query_arg() ) );
        }
    }
}
