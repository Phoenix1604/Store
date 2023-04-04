<?php
    function add_store_meta_boxes() {
        add_meta_box(
            'store_location_meta_box',
            'Store Location',
            'store_location_meta_box_callback',
            'store',
            'normal',
            'default'
        );
        add_meta_box(
            'store_name_meta_box',
            'Store Name',
            'store_name_meta_box_callback',
            'store',
            'normal',
            'default'
        );
    }

    function store_location_meta_box_callback( $post ) {
        global $post;
        wp_nonce_field( 'store_meta_box_location', 'store_location_nonce' );
        $store_location = get_post_meta( $post->ID, 'store_location', true );
        echo '<input type="text" id="store_location" name="store_location" value="' . esc_attr( $store_location ) . '" size="25" />';
    }
    
    // Callback for store name meta box
    function store_name_meta_box_callback( $post ) {
        global $post;
    wp_nonce_field( 'store_meta_box_name', 'store_name_nonce' );
    $store_name = get_post_meta( $post->ID, 'store_name', true );
    echo '<input type="text" id="store_name" name="store_name" value="' . esc_attr( $store_name ) . '" size="25" />';
    }

    
    
    
?>