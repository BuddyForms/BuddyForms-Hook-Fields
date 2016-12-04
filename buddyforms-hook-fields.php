<?php
/*
 Plugin Name: BuddyForms Hook Fields
 Plugin URI: http://buddyforms.com/downloads/buddyforms-hook-fields/
 Description: BuddyForms Hook Fields
 Version: 1.1.9.1
 Author: svenl77, buddyforms
 Author URI: https://profiles.wordpress.org/svenl77
 Licence: GPLv3
 Network: false

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

add_filter( 'buddyforms_formbuilder_fields_options', 'buddyforms_hook_options_into_formfields', 2, 3 );
function buddyforms_hook_options_into_formfields( $form_fields, $field_type, $field_id ) {
	global $post;

	$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );

	$hook_field_types = array(
		'text',
		'textarea',
		'link',
		'mail',
		'dropdown',
		'radiobutton',
		'checkbox',
		'taxonomy',
		'number',
		'date'
	);

	if ( ! in_array( $field_type, $hook_field_types ) ) {
		return $form_fields;
	}

	$hooks = array( 'no', 'before_the_title', 'after_the_title', 'before_the_content', 'after_the_content' );
	$hooks = apply_filters( 'buddyforms_form_element_hooks', $hooks );

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

function buddyforms_form_display_element_frontend() {
	global $buddyforms, $post, $bf_hooked;

	if ( is_admin() ) {
		return;
	}

	if(!isset($post->ID)) {
		return;
	}

	if ( $bf_hooked ) {
		return;
	}

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	if ( ! isset( $form_slug ) ) {
		return;
	}

	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	if ( ! isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		return;
	}

	$before_the_title   = false;
	$after_the_title    = false;
	$before_the_content = false;
	$after_the_content  = false;

	foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $customfield ) :

		if ( ! empty( $customfield['slug'] ) && ( ! empty( $customfield['hook'] ) || is_single() ) ) :

			$customfield_value = get_post_meta( $post->ID, $customfield['slug'], true );

			if ( ! empty( $customfield_value ) ) {
				$post_meta_tmp = '<div class="post_meta ' . $customfield['slug'] . '">';

				if ( isset( $customfield['display_name'] ) ) {
					$post_meta_tmp .= '<label>' . $customfield['name'] . '</label>';
				}


				if ( is_array( $customfield_value ) ) {
					$meta_tmp = "<p>" . implode( ',', $customfield_value ) . "</p>";
				} else {
					$meta_tmp = "<p>" . $customfield_value . "</p>";
				}


				switch ( $customfield['type'] ) {
					case 'taxonomy':
							$meta_tmp = get_the_term_list( $post->ID, $customfield['taxonomy'], "<p>", ' - ', "</p>" );
						break;
					case 'link':
						$meta_tmp = "<p><a href='" . $customfield_value . "' " . $customfield['name'] . ">" . $customfield_value . " </a></p>";
						break;
					default:
						apply_filters( 'buddyforms_form_element_display_frontend', $customfield );
						break;
				}

				if (  $meta_tmp  ) {
					$post_meta_tmp .= $meta_tmp;
				}

				$post_meta_tmp .= '</div>';

				$post_meta_tmp = apply_filters( 'buddyforms_form_element_display_frontend_before_hook', $post_meta_tmp );


				if ( isset( $customfield['hook'] ) && ! empty( $customfield['hook'] ) ) {
					add_action( $customfield['hook'], create_function( '', 'echo  "' . addcslashes( $post_meta_tmp, '"' ) . '";' ) );
				}

				if ( is_single() && isset( $customfield['display'] ) ) {
					switch ( $customfield['display'] ) {
						case 'before_the_title':
							$before_the_title .= $post_meta_tmp;
							break;
						case 'after_the_title':
							$after_the_title .= $post_meta_tmp;
							break;
						case 'before_the_content':
							$before_the_content .= $post_meta_tmp;
							break;
						case 'after_the_content':
							$after_the_content .= $post_meta_tmp;
							break;
					}
				}

			}

		endif;

	endforeach;

	if ( is_single() ) {

		if ( $before_the_title ) {
			add_filter( 'the_title', create_function( '$content,$id', 'if(is_single() && $id == get_the_ID()) { return "' . addcslashes( $before_the_title, '"' ) . '$content"; } return $content;' ), 10, 2 );
		}

		if ( $after_the_title ) {
			add_filter( 'the_title', create_function( '$content,$id', 'if(is_single() && $id == get_the_ID()) { return "$content' . addcslashes( $after_the_title, '"' ) . '"; } return $content;' ), 10, 2 );
		}

		if ( $before_the_content ) {
			add_filter( 'the_content', create_function( '', 'return "' . addcslashes( $before_the_content . $post->post_content, '"' ) . '";' ) );
		}

		if ( $after_the_content ) {
			add_filter( 'the_content', create_function( '', 'return "' . addcslashes( $post->post_content . $after_the_content, '"' ) . '";' ) );
		}

	}
	$bf_hooked = true;

}

add_action( 'the_post', 'buddyforms_form_display_element_frontend' );

//
// Check the plugin dependencies
//
add_action('init', function(){

	// Only Check for requirements in the admin
	if(!is_admin()){
		return;
	}

	// Require TGM
	require ( dirname(__FILE__) . '/includes/resources/tgm/class-tgm-plugin-activation.php' );

	// Hook required plugins function to the tgmpa_register action
	add_action( 'tgmpa_register', function(){

		// Create the required plugins array
		if ( ! defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
			$plugins['buddyforms'] = array(
				'name'      => 'BuddyForms',
				'slug'      => 'buddyforms',
				'required'  => true,
			);
		}

		$config = array(
			'id'           => 'buddyforms-tgmpa',  // Unique ID for hashing notices for multiple instances of TGMPA.
			'parent_slug'  => 'plugins.php',       // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                // Show admin notices or not.
			'dismissable'  => false,               // If false, a user cannot dismiss the nag message.
			'is_automatic' => true,                // Automatically activate plugins after installation or not.
		);

		// Call the tgmpa function to register the required plugins
		tgmpa( $plugins, $config );

	} );
}, 1, 1);
