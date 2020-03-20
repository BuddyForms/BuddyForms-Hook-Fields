<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the option metabox
 */
function buddyforms_list_all_post_fields_admin_settings_sidebar_metabox() {
	add_meta_box( 'buddyforms_list_all_post_fields', __( "Display Form Elements on the Single View ", 'buddyforms' ), 'buddyforms_list_all_post_fields_admin_settings_sidebar_metabox_html', 'buddyforms', 'normal', 'low' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_class' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_hide_if_form_type_register' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_show_if_attached_page' );
}

add_filter( 'add_meta_boxes', 'buddyforms_list_all_post_fields_admin_settings_sidebar_metabox' );

/**
 * Form options
 */
function buddyforms_list_all_post_fields_admin_settings_sidebar_metabox_html() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	$form_setup = array();

	//Add option to select the page to use as template
	// Get all allowed pages
	$form_attached_page = array();
	if ( ! empty( $buddyform['attached_page'] ) ) {
		$form_attached_page[] = $buddyform['attached_page'];
	}
	$all_pages = buddyforms_get_all_pages( 'id', 'form_builder', true, $form_attached_page, __( '-No override-', 'buddyforms' ) );
	$attached_page                    = isset( $buddyform['hook_fields_template_page'] ) ? $buddyform['hook_fields_template_page'] : '';
	$form_setup[] = new Element_Select( '<b>' . __( "Template page", 'buddyforms' ) . '</b>', "buddyforms_options[hook_fields_template_page]", $all_pages, array(
		'value'     => $attached_page,
		'shortDesc' => sprintf( '%s <a href="https://docs.buddyforms.com/article/641-page-template?utm_source=plugin" target="_blank">%s</a>', __( 'This is a template page to override the output of a single post.', 'buddyforms' ), __( 'Read more in the documentation.', 'buddyforms' ) ),
		'id'        => 'attached_page',
	) );
	//Add option to hide the title
	$hide_title   = isset( $buddyform['hook_fields_hide_title'] ) ? $buddyform['hook_fields_hide_title'] : '';
	$form_setup[] = new Element_Checkbox( '<b>' . __( 'Hide the title ', 'buddyforms' ) . '</b>', "buddyforms_options[hook_fields_hide_title]", array( 'yes' => __( 'Disable the post title', 'buddyforms' ) ), array( 'value' => $hide_title, 'shortDesc' => __( 'Use this option if you override the Title with a template shortcode.', 'buddyforms' ) ) );
	//Add field data as table
	$hook_fields_list_on_single = isset( $buddyform['hook_fields_list_on_single'] ) ? $buddyform['hook_fields_list_on_single'] : '';
	$form_setup[]               = new Element_Checkbox( "<b>" . __( 'Add Form Elements as Table', 'buddyforms' ) . "</b>", "buddyforms_options[hook_fields_list_on_single]", array( "integrate" => "Integrate this Form" ), array( 'value' => $hook_fields_list_on_single, 'shortDesc' => __( 'This option will not work if you have a Template page selected.', 'buddyforms' ) ) );

	buddyforms_display_field_group_table( $form_setup );
}

/**
 * Add option inside each field
 *
 * @param $form_fields
 * @param $field_type
 * @param $field_id
 *
 * @return mixed
 */
function buddyforms_hook_options_into_formfields( $form_fields, $field_type, $field_id ) {
	global $post;

	if ( empty( $post ) || empty( $post->ID ) ) {
		return $form_fields;
	}

	$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );

	if ( empty( $buddyform ) ) {
		return $form_fields;
	}

	$hook_field_types = array(
		'text',
		'textarea',
		'link',
		'mail',
		'dropdown',
		'radiobutton',
		'checkbox',
		'taxonomy',
		'category',
		'number',
		'date',
		'upload',
		'file',
		'user_website'
	);

	$hook_field_types = apply_filters( 'buddyforms_hook_field_allowed_types', $hook_field_types );

	if ( ! in_array( $field_type, $hook_field_types ) ) {
		return $form_fields;
	}

	$hooks = array( 'no', 'before_the_title', 'after_the_title', 'before_the_content', 'after_the_content' );
	$hooks = apply_filters( 'buddyforms_hook_field_form_element_position', $hooks );

	$form_fields['hooks']['html_display'] = new Element_HTML( '<div class="bf_element_display">' );

	$display = 'false';
	if ( isset( $buddyform['form_fields'][ $field_id ]['display'] ) ) {
		$display = $buddyform['form_fields'][ $field_id ]['display'];
	}

	$form_fields['hooks']['display'] = new Element_Select( "Display? <i>This only works for the single view</i>", "buddyforms_options[form_fields][" . $field_id . "][display]", $hooks, array( 'value' => $display ) );

	$hook = '';
	if ( isset( $buddyform['form_fields'][ $field_id ]['hook'] ) ) {
		$hook = $buddyform['form_fields'][ $field_id ]['hook'];
	}

	$form_fields['hooks']['hook'] = new Element_Textbox( "Hook: <i>Add hook name works global</i>", "buddyforms_options[form_fields][" . $field_id . "][hook]", array( 'value' => $hook ) );

	$display_name = 'false';
	if ( isset( $buddyform['form_fields'][ $field_id ]['display_name'] ) ) {
		$display_name = $buddyform['form_fields'][ $field_id ]['display_name'];
	}
	$form_fields['hooks']['display_name'] = new Element_Checkbox( "Display name?", "buddyforms_options[form_fields][" . $field_id . "][display_name]", array( '' ), array(
		'value' => $display_name,
		'id'    => "buddyforms_options[form_fields][" . $field_id . "][display_name]"
	) );

	$form_fields['hooks']['html_display_end'] = new Element_HTML( '</div>' );

	return $form_fields;
}

add_filter( 'buddyforms_formbuilder_fields_options', 'buddyforms_hook_options_into_formfields', 2, 3 );