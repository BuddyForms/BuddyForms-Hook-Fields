<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define who the form data will be show in a single post view.
 *
 * @param $content
 *
 * @return string
 */
function buddyforms_list_all_post_fields( $content ) {
	global $buddyforms, $post;

	if ( ! is_single() ) {
		return $content;
	}

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	if ( ! $form_slug ) {
		return $content;
	}

	if ( empty( $buddyforms ) || empty( $buddyforms[ $form_slug ] ) ) {
		return $content;
	}

	$add_table_content        = ( ! empty( $buddyforms[ $form_slug ]['hook_fields_list_on_single'] ) ) ? $buddyforms[ $form_slug ]['hook_fields_list_on_single'] : '';
	$post_template_id         = ( ! empty( $buddyforms[ $form_slug ]['hook_fields_template_page'] ) ) ? (int) $buddyforms[ $form_slug ]['hook_fields_template_page'] : 'none';
	$is_post_template_enabled = ( ! empty( $post_template_id ) && $post_template_id !== 'none' );

	$hide_title = ( ! empty( $buddyforms[ $form_slug ]['hook_fields_hide_title'] ) ) ? $buddyforms[ $form_slug ]['hook_fields_hide_title'] : '';
	if ( ! isset( $buddyforms[ $form_slug ]['hook_fields_list_on_single'] ) ) {
		return $content;
	}

	remove_filter( 'the_content', 'buddyforms_list_all_post_fields', 999 );

	$exist_title_in_post = buddyforms_exist_field_type_in_form( $form_slug, 'title' );
	if ( ! empty( $hide_title ) && $exist_title_in_post ) {
		echo '<style>.entry-header{display: none;}</style>';
	}
	if ( $is_post_template_enabled ) {
		if ( class_exists( 'Elementor\Plugin' ) ) {
			$template_content = Elementor\Plugin::instance()->frontend->get_builder_content( $post_template_id, true );
		}

		if ( empty( $template_content ) ) {
			$template_content = get_the_content( null, false, $post_template_id );
			$template_content = apply_filters( 'the_content', $template_content );
			$template_content = str_replace( ']]>', ']]&gt;', $template_content );
		}
		$template_content = buddyforms_get_field_value_from_string( $template_content, $post->ID, $form_slug, true );
		if ( ! empty( $template_content ) ) {
			$content = $template_content;
		}
	} else if ( $add_table_content ) {


		$striped_c   = 0;
		$new_content = '<table rules="all" class="hook-field-container" cellpadding="10">';
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $field ) {

				if ( in_array( $field['slug'], buddyforms_get_exclude_field_slugs() ) || $field['slug'] == 'buddyforms_form_content' || $field['slug'] == 'buddyforms_form_title' || $field['slug'] == 'featured_image' || $field['type'] == 'hidden' ) {
					continue;
				}

				$field = buddyforms_get_field_with_meta( $form_slug, $post->ID, $field['slug'] );

				$field_value = ! empty( $field['value'] ) ? $field['value'] : apply_filters( 'buddyforms_field_shortcode_empty_value', '', $field, $form_slug, $post->ID, $field['slug'] );

				$striped = ( $striped_c ++ % 2 == 1 ) ? "style='background: #eee;'" : '';

				if ( isset( $field['slug'] ) ) {
				    if ( $field['type'] === 'upload' || $field['type'] === 'file' ){
				        $upload_field_val = get_post_meta( $post->ID, $field['slug'] , true);
                        $media_items = explode( ',', $upload_field_val );
                        $result = "";
                        foreach ( $media_items as $attachment_item ){
                            if(!empty($attachment_item)){
                                $attachment_full_url 	  = wp_get_attachment_url( $attachment_item );
                                $default_thumbnail 		  = plugin_dir_url (__FILE__ ).'/assets/images/multimedia.png';
                                $attachment_thumbnail_url = wp_get_attachment_thumb_url( $attachment_item ) === false ? $default_thumbnail : wp_get_attachment_thumb_url( $attachment_item );

                                $result .= "<a href='".$attachment_full_url."' target='_blank'> <img src='" . $attachment_thumbnail_url . "' /></a>";
                            }
                        }
                        $new_content .= "<tr " . $striped . "><td><strong>" . $field['name'] . "</strong> </td><td>" .trim( $result ). "</td></tr>";
                    }
				    else{
                        $new_content .= "<tr " . $striped . "><td><strong>" . $field['name'] . "</strong> </td><td>" . $field_value . "</td></tr>";
				    }

				}
			}
		}

		// Table end
		$new_content .= "</table>";
		$content     .= $new_content;
	}

	add_filter( 'the_content', 'buddyforms_list_all_post_fields', 999, 1 );

	// Let us return the form elements table
	return $content;
}

add_filter( 'the_content', 'buddyforms_list_all_post_fields', 999, 1 );

/**
 * Define how to display each field
 */
function buddyforms_form_display_element_frontend() {
	global $buddyforms, $post, $bf_hooked;

	if ( is_admin() ) {
		return;
	}

	if ( ! isset( $post->ID ) ) {
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

	foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_id => $customfield ) {

		if ( ! empty( $customfield['slug'] ) && ( ! empty( $customfield['hook'] ) || is_single() ) ) {

			$field             = buddyforms_get_field_with_meta( $form_slug, $post->ID, $customfield['slug'] );
			$customfield_value = ! empty( $field['value'] ) ? $field['value'] : apply_filters( 'buddyforms_field_shortcode_empty_value', '', $field, $form_slug, $post->ID, $field['slug'] );

			if ( ! empty( $customfield_value ) ) {
				$post_meta_tmp = '<div class="post_meta ' . $customfield['slug'] . '">';

				if ( isset( $customfield['display_name'] ) ) {
					$post_meta_tmp .= '<label>' . $customfield['name'] . '</label>';
				}

                if ( $field['type'] === 'upload' || $field['type'] === 'file' ){
                    $upload_field_val = get_post_meta( $post->ID, $field['slug'], true );
                    $media_items 	  = explode( ',', $upload_field_val );
                    $result           = array();
					$thumbnail_size   = 'thumbnail';

					if ( isset( $buddyforms[ $form_slug ]['form_fields'][$field_id]['thumbnail_size'] ) ) {
						$thumbnail_size = $buddyforms[ $form_slug ]['form_fields'][$field_id]['thumbnail_size'];
					}

                    foreach ( $media_items as $attachment_item ){
                        $attachment_full_url      = wp_get_attachment_url( $attachment_item );
						$attachment_thumbnail_url = wp_get_attachment_image_src( $attachment_item, $thumbnail_size );

						if ( ! $attachment_thumbnail_url  ) {
							$attachment_thumbnail_url = array( plugin_dir_url( __FILE__ ) . '/assets/images/multimedia.png' );
						}

                        $result[] = "<a href='".$attachment_full_url."' target='_blank'> <img src='" . $attachment_thumbnail_url[0] . "' /></a>";
                    }
                    $meta_tmp = implode( '', $result );

                } else{
                    if ( is_array( $customfield_value ) ) {
                        $meta_tmp = "<p>" . implode( ',', $customfield_value ) . "</p>";
                    } else {
                        $meta_tmp = "<p>" . $customfield_value . "</p>";
                    }
                }



				if ( $meta_tmp ) {
					$post_meta_tmp .= apply_filters( 'buddyforms_form_element_display_frontend', $meta_tmp, $customfield );
				}

				$post_meta_tmp .= '</div>';

				$post_meta_tmp = apply_filters( 'buddyforms_form_element_display_frontend_before_hook', $post_meta_tmp );


				if ( isset( $customfield['hook'] ) && ! empty( $customfield['hook'] ) ) {
					add_action( $customfield['hook'], function () use ( $post_meta_tmp ) {
						echo addcslashes( $post_meta_tmp, '"' );
					} );
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
		}
	}

	if ( is_single() ) {

		if ( $before_the_title ) {
			add_filter( 'the_title', function ( $content, $id ) use ( $before_the_title ) {
				if ( is_single() && $id == get_the_ID() ) {
					return $before_the_title . $content;
				}

				return $content;
			}, 9999, 2 );
		}

		if ( $after_the_title ) {
			add_filter( 'the_title', function ( $content, $id ) use ( $after_the_title ) {
				if ( is_single() && $id == get_the_ID() ) {
					return $content . $after_the_title;
				}

				return $content;
			}, 9999, 2 );
		}

		if ( $before_the_content ) {
			add_filter( 'the_content', function ( $content ) use ( $before_the_content ) {
				return $before_the_content . $content;
			}, 9999 );
		}

		if ( $after_the_content ) {

			add_filter( 'the_content', function ( $content ) use ( $after_the_content ) {
				return $content . $after_the_content;
			}, 9999 );
		}

	}
	$bf_hooked = true;

}

add_action( 'the_post', 'buddyforms_form_display_element_frontend' );

