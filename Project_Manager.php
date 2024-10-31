<?php
/*
Plugin Name: Project Manager
Plugin URI: https://wordpress.org/plugins/project-manager
Description: "Project Manager" is a project management plugin for WordPress that makes it easy to plan, manage and track projects. It provides a complete overview, assigned tasks, team members and due dates for efficient project management.
Version: 1.0
Author: Alexis Grolot
Author URI: https://wp-pro.epizy.com/
License: GPLv3
*/

class Project_Manager {

    function __construct() {
        // Register custom post type for projects
        add_action( 'init', array( $this, 'register_project_post_type' ) );

        // Register custom taxonomy for project categories
        add_action( 'init', array( $this, 'register_project_taxonomy' ) );

        // Add meta boxes for project details
        add_action( 'add_meta_boxes', array( $this, 'add_project_meta_boxes' ) );

        // Save project details when the post is saved
        add_action( 'save_post', array( $this, 'save_project_details' ) );

        // Add custom columns to project list in admin
        add_filter( 'manage_project_posts_columns', array( $this, 'add_project_list_columns' ) );
        add_action( 'manage_project_posts_custom_column', array( $this, 'add_project_list_column_data' ), 10, 2 );
    }

    // Register custom post type for projects
    function register_project_post_type() {
        $args = array(
            'label' => 'Projects',
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'projects'),
            'query_var' => true,
            'menu_icon' => 'dashicons-portfolio',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'comments',
                'thumbnail',
                'author',
                'custom-fields'
            )
        );
        register_post_type( 'project', $args );
    }

    // Register custom taxonomy for project categories
    function register_project_taxonomy() {
        $args = array(
            'label' => 'Project Categories',
            'public' => true,
            'show_ui' => true,
            'hierarchical' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'project-category')
        );
        register_taxonomy( 'project_category', 'project', $args );
    }

    // Add meta boxes for project details
    function add_project_meta_boxes() {
        add_meta_box(
            'project_details',
            'Project Details',
            array( $this, 'render_project_details_meta_box' ),
            'project',
            'normal',
            'default'
        );
    }

    // Render the project details meta box
    function render_project_details_meta_box( $post ) {
        // Retrieve current values for the project details
        $project_start_date = get_post_meta( $post->ID, 'project_start_date', true );
        $project_end_date = get_post_meta( $post->ID, 'project_end_date', true );
        $project_status = get_post_meta( $post->ID, 'project_status', true );
        $project_members = get_post_meta( $post->ID, 'project_members', true );

        // Output the form fields for the project details
        ?>
        <table>
            <tr>
                <td>Start Date:</td>
                <td>
                    <input type="text" name="project_start_date" value="<?php echo esc_attr( $project_start_date ); ?>" />
                </td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>
                    <input type="text" name="project_end_date" value="<?php echo esc_attr( $project_end_date ); ?>" />
                </td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <select name="project_status">
                        <option value="not_started" <?php selected( $project_status, 'not_started' ); ?>>Not Started</option>
                        <option value="in_progress" <?php selected( $project_status, 'in_progress' ); ?>>In Progress</option>
                        <option value="completed" <?php selected( $project_status, 'completed' ); ?>>Completed</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Members:</td>
                <td>
                    <input type="text" name="project_members" value="<?php echo esc_attr( $project_members ); ?>" />
                </td>
            </tr>
        </table>
        <?php
    }

    // Save project details when the post is saved
    function save_project_details( $post_id ) {
        // Save the project start date
        if ( isset( $_POST['project_start_date'] ) ) {
            update_post_meta( $post_id, 'project_start_date', sanitize_text_field( $_POST['project_start_date'] ) );
        }

        // Save the project end date
        if ( isset( $_POST['project_end_date'] ) ) {
            update_post_meta( $post_id, 'project_end_date', sanitize_text_field( $_POST['project_end_date'] ) );
        }

        // Save the project status
        if ( isset( $_POST['project_status'] ) ) {
            update_post_meta( $post_id, 'project_status', sanitize_text_field( $_POST['project_status'] ) );
        }

        // Save the project members
        if ( isset( $_POST['project_members'] ) ) {
            update_post_meta( $post_id, 'project_members', sanitize_text_field( $_POST['project_members'] ) );
        }
    }

    // Add custom columns to project list in admin
    function add_project_list_columns( $columns ) {
        $columns['project_start_date'] = 'Start Date';
        $columns['project_end_date'] = 'End Date';
        $columns['project_status'] = 'Status';
        $columns['project_members'] = 'Members';
        return $columns;
    }

    // Add data to custom columns in project list in admin
    function add_project_list_column_data( $column, $post_id ) {
        switch ( $column ) {
            case 'project_start_date':
                echo esc_html(get_post_meta( $post_id, 'project_start_date', true ));
                break;
            case 'project_end_date':
                echo esc_html(get_post_meta( $post_id, 'project_end_date', true ));
                break;
            case 'project_status':
                echo esc_html(get_post_meta( $post_id, 'project_status', true ));
                break;
            case 'project_members':
                echo esc_html(get_post_meta( $post_id, 'project_members', true ));
                break;
        }
    }
}

new Project_Manager();
