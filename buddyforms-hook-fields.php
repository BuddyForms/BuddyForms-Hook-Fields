<?php
/**
 * Created by PhpStorm.
 * User: svenl77
 * Date: 19.03.14
 * Time: 10:41
 */

/**
 * If single and if the post type is selected for BuddyPress and if there is post meta to display.
 * Hook the post meta to the right places.
 *
 * This function is an example how you can hook fields into templates in your BuddyForms extension
 * of course you can also use get_post_meta(sanitize_title('name'))
 *
 * @package BuddyForms
 * @since 0.2-beta
 */
function buddyforms_form_display_element_frontend(){
    global $buddyforms, $post, $bp;

    if(is_archive())
        return;

    if(is_admin())
        return;

    if (!isset($buddyforms['buddyforms']))
        return;

    $post_id = '';
    $post_id = apply_filters('buddyforms_hook_fields_from_post_id',$post_id);

    echo '$post_id '.$post_id.'<br>';
    if(isset($post_id)){
        $post = get_post($post_id);
    }

    $post_type = get_post_type($post);

    echo '$post_type '.$post_type.'<br>';

    foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
        if(isset($buddyform['post_type']) && $buddyform['post_type'] != 'none' &&  $buddyform['post_type'] == $post_type){
            $form = $buddyform['slug'];
        }

    }

    if(!isset($form))
        return;

    echo '$post->ID '.$post->ID.'<br>';

    $bf_form_slug = get_post_meta($post->ID, '_bf_form_slug', true);

    echo '$bf_form_slug '.$bf_form_slug.'<br>';

    if(!isset($bf_form_slug) || $bf_form_slug == 'none' )
        return;

    if (!empty($buddyforms['buddyforms'][$form]['form_fields'])) {

        foreach ($buddyforms['buddyforms'][$form]['form_fields'] as $key => $customfield) :

            if(isset($customfield['slug']) && !empty($customfield['slug'])){
                $slug = $customfield['slug'];
            } else {
                $slug = sanitize_title($customfield['name']);
            }

            $customfield_value = get_post_meta($post->ID, $slug, true);

            if (isset($customfield_value) && $customfield['display'] != 'no') :

                $post_meta_tmp = '<div class="post_meta ' . $slug . '">';

                if(isset($customfield['display_name']))
                    $post_meta_tmp .= '<label>' . $customfield['name'] . '</label>';


                $meta_tmp = "<p>". $customfield_value ."</p>";

                if(is_array($customfield_value))
                    $meta_tmp = "<p>". implode(',' , $customfield_value)."</p>";

                switch ($customfield['type']) {
                    case 'Taxonomy':
                        $meta_tmp = get_the_term_list( $post->ID, $customfield['taxonomy'], "<p>", ' - ', "</p>" );
                        break;
                    case 'Link':
                        $meta_tmp = "<p><a href='" . $customfield_value . "' " . $customfield['name'] . ">" . $customfield_value . " </a></p>";
                        break;
                    default:
                        apply_filters('buddyforms_form_element_display_frontend',$customfield,$post_type);
                        break;
                }

                $post_meta_tmp .= $meta_tmp;

                $post_meta_tmp .= '</div>';
                apply_filters('buddyforms_form_element_display_frontend_before_hook',$post_meta_tmp);

                switch ($customfield['display']) {
                    case 'before_the_title':
                        add_filter( 'the_title', create_function('', 'return "' . addcslashes($post_meta_tmp.$post->post_title, '"') . '";') );
                        break;
                    case 'after_the_title':
                        add_filter( 'the_title', create_function('', 'return "' . addcslashes($post->post_title.$post_meta_tmp, '"') . '";') );
                        break;
                    case 'before_the_content':
                        add_filter( 'the_content', create_function('', 'return "' . addcslashes($post_meta_tmp.$post->post_content, '"') . '";') );
                        break;
                    case 'after_the_content':
                        add_filter( 'the_content', create_function('', 'return "' . addcslashes($post->post_content.$post_meta_tmp, '"') . '";') );
                        break;

                    default:
                        add_action($customfield['display'], create_function('', 'echo "' . addcslashes($post_meta_tmp, '"') . '";'));
                        break;
                }


            endif;
        endforeach;
    }
}

// This function needs to be completely rewritten and I will leave it out for now
add_action('the_post','buddyforms_form_display_element_frontend');
