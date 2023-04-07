<?php
    function add_store_meta_boxes() {
        add_meta_box(
            'store_name_meta_box',
            'Store Name',
            'store_name_meta_box_callback',
            'store',
            'normal',
            'default'
        );

        add_meta_box(
            'store_location_meta_box',
            'Store Address',
            'store_location_meta_box_callback',
            'store',
            'normal',
            'default'
        );
        

        add_meta_box(
            'store_email_meta_box',
            'Email',
            'store_email_meta_box_callback',
            'store',
            'normal',
            'default'
        );

        add_meta_box(
            'store_phone_meta_box',
            'Phone No',
            'store_phone_meta_box_callback',
            'store',
            'normal',
            'default'
        );
    }

    function store_location_meta_box_callback( $post ) {
        global $post;
        wp_nonce_field( 'store_meta_box_location', 'store_location_nonce' );
        $store_location = get_post_meta( $post->ID, 'store_location', true );
        echo '<input type="text" id="store_location" name="store_location" value="' . esc_attr( $store_location ) . '" size="25"  required/>';
    }
    
    // Callback for store name meta box
    function store_name_meta_box_callback( $post ) {
        global $post;
        wp_nonce_field( 'store_meta_box_name', 'store_name_nonce' );
        $store_name = get_post_meta( $post->ID, 'store_name', true );
        echo '<input type="text" id="store_name" name="store_name" value="' . esc_attr( $store_name ) . '" size="25"  required/>';
    }

    function store_email_meta_box_callback($post) {
        global $post;
        wp_nonce_field('store_meta_box_email', 'store_email_nonce');
        $store_email = get_post_meta($post->ID, 'store_email', true);
        echo '<input type="email" id="store_email" name="store_email" value="' .esc_attr($store_email) . ' " size="25" required/> ';
    }

    function store_phone_meta_box_callback($post) {
        global $post;
        wp_nonce_field('store_meta_box_phone', 'store_phone_nonce');
        $store_phone = get_post_meta($post->ID, 'store_phone', true);
        echo '<input type="tel" id="store_phone" name="store_phone" value="' .esc_attr($store_phone) . ' " size="25" required/> ';
    }

    
    
    
?>