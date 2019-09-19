<?php

add_filter( 'the_content', 'buddyforms_list_all_post_fields', 50, 1 );
function buddyforms_list_all_post_fields($content) {
	global $buddyforms, $post;

	if( ! is_single() ){
		return $content;
	}

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	if( ! $form_slug ){
		return $content;
	}

	if( ! isset( $buddyforms[ $form_slug ]['hook_fields_list_on_single'] ) ){
		return $content;
	}

	$striped_c = 0;
	// Table start
	$new_content = '<table rules="all" style="border-color: #666;" cellpadding="10">';

	if( isset($buddyforms[ $form_slug ]['form_fields'] ) ){
		foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $field ) {

				if( $field['slug'] == 'buddyforms_form_content' || $field['slug'] == 'buddyforms_form_title' || $field['slug'] == 'featured_image' || $field['type'] == 'hidden'  ){
				continue;
			}

			$value = get_post_meta($post->ID, $field['slug'], true);

			// Check if is array
			if ( is_array( $value ) ) {
				$field_value = implode( ',', $value );
			} else {
				$field_value = $value;
			}

			switch ( $field['type'] ) {
                case 'file':
                    $attachments = array_filter( explode( ',', $field_value  ) );
                    if ( $attachments ){
                        $field_value ='';
                        foreach ( $attachments as $attachment_id ){

                            $attachment_metadat = get_post( $attachment_id );
                            $field_value.= ' <div class="bf_attachment_img">
                                    ' . wp_get_attachment_image( $attachment_id, array( 64, 64 ), true ) . '
                                    </div>';
                        }
                    }
                    break;
				case 'taxonomy':
					if ( is_array( $value ) ) {
						foreach ( $value as $cat ) {
							$term    = get_term( $cat, $field['taxonomy'] );
							$terms[] = $term->name;
						}
						$field_value = implode( ',', $terms );
					} else {
						$term        = get_term( $value, $field['taxonomy'] );
						$field_value = $term->name;
					}
					break;
				case 'link':
					$field_value = "<p><a href='" . $value . "' " . $field['name'] . ">" . $value . " </a></p>";
					break;
				case 'user_website':
					$field_value = "<p><a href='" . $value . "' " . $field['name'] . ">" . $value . " </a></p>";
					break;
			}

			$striped = ( $striped_c ++ % 2 == 1 ) ? "style='background: #eee;'" : '';

			if ( isset( $field['slug']  ) ) {
				$new_content .= "<tr " . $striped . "><td><strong>" . $field['name'] . "</strong> </td><td>" . $field_value . "</td></tr>";
			}
		}
	}

	// Table end
	$new_content .= "</table>";

	// Let us return the form elements table
	return $content . $new_content;
}

add_filter( 'add_meta_boxes', 'buddyforms_list_all_post_fields_admin_settings_sidebar_metabox' );
function buddyforms_list_all_post_fields_admin_settings_sidebar_metabox() {
	add_meta_box( 'buddyforms_list_all_post_fields', __( "Display Form Elements on the Single View ", 'buddyforms' ), 'buddyforms_list_all_post_fields_admin_settings_sidebar_metabox_html', 'buddyforms', 'normal', 'low' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_class' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_hide_if_form_type_register' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_show_if_attached_page' );
}


function buddyforms_list_all_post_fields_admin_settings_sidebar_metabox_html() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );


	$form_setup = array();

	$hook_fields_list_on_single = isset( $buddyform['hook_fields_list_on_single'] ) ? $buddyform['hook_fields_list_on_single'] : '';
	$form_setup[] = new Element_Checkbox( "<b>" . __( 'Add Form Elements as Table', 'buddyforms' ) . "</b>", "buddyforms_options[hook_fields_list_on_single]", array( "integrate" => "Integrate this Form" ), array( 'value'     => $hook_fields_list_on_single, 'shortDesc' => __( '', 'buddyforms' ) ) );

	buddyforms_display_field_group_table( $form_setup );

}