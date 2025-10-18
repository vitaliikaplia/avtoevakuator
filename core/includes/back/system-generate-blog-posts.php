<?php

if(!defined('ABSPATH')){exit;}

// Додаємо пункт меню в секцію інструментів
add_action('admin_menu', 'rp_generator_menu');

function rp_generator_menu() {
    add_management_page(
        __('Generate Posts', TEXTDOMAIN), // title в браузері
        __('Generate Posts', TEXTDOMAIN), // пункт меню
        'manage_options', // права доступу
        'random-posts-generator', // slug
        'rp_generator_page' // функція відображення сторінки
    );
}

// Функція для генерації заголовку
function rp_generate_title_from_content($content) {
    $clean_content = strip_tags($content);
    $words = preg_split('/\s+/', $clean_content);
    $words = array_filter($words, function($word) {
        return strlen($word) > 3 && !preg_match('/[.,!?;:]/', $word);
    });
    shuffle($words);
    $word_count = rand(3, 5);
    $selected_words = array_slice($words, 0, $word_count);
    $title = implode(' ', $selected_words);
    $title = mb_strtolower($title);
    $title = mb_strtoupper(mb_substr($title, 0, 1)) . mb_substr($title, 1);
    return $title;
}

// Функція генерації постів
function generate_random_posts($count, $paragraphs, $with_image) {
    $generated = 0;
    $errors = array();

    for ($i = 1; $i <= $count; $i++) {
        try {
            $lorem_ipsum = file_get_contents("https://loripsum.net/api/{$paragraphs}");

            if($with_image) {
                $lorem_ipsum_image = file_get_contents('https://picsum.photos/1024/768');
            }

            $post_title = rp_generate_title_from_content($lorem_ipsum);

            $new_post = array(
                'post_type' => 'post',
                'post_title' => $post_title,
                'post_content' => $lorem_ipsum,
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
            );

            $new_posted_item_id = wp_insert_post($new_post);

            if($with_image && $lorem_ipsum_image) {
                $fileName = 'lorem_ipsum_' . $i . '_' . wp_rand() . '.jpg';
                $upload_file = wp_upload_bits($fileName, null, $lorem_ipsum_image);
                $wp_filetype = wp_check_filetype($fileName, null);

                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_parent' => $new_posted_item_id,
                    'post_title' => preg_replace('/\.[^.]+$/', '', $fileName),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $new_posted_item_id);

                if (!is_wp_error($attachment_id)) {
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                    wp_update_attachment_metadata($attachment_id, $attachment_data);
                    set_post_thumbnail($new_posted_item_id, $attachment_id);
                }
            }

            $generated++;
        } catch (Exception $e) {
            $errors[] = sprintf(
                __('Error generating post %d: %s', TEXTDOMAIN),
                $i,
                $e->getMessage()
            );
        }
    }

    return array(
        'generated' => $generated,
        'errors' => $errors
    );
}

// Сторінка генератора
function rp_generator_page() {
    $message = '';

    if(isset($_POST['generate_posts'])) {
        $count = intval($_POST['post_count']);
        $paragraphs = intval($_POST['paragraphs']);
        $with_image = isset($_POST['with_image']) ? true : false;

        if($count > 0 && $paragraphs > 0) {
            $result = generate_random_posts($count, $paragraphs, $with_image);
            $message = sprintf(
                '<div class="notice notice-success"><p>%s</p>',
                sprintf(
                    __('Successfully generated %d posts!', TEXTDOMAIN),
                    $result['generated']
                )
            );
            if(!empty($result['errors'])) {
                $message .= sprintf(
                    '<p>%s<br>%s</p>',
                    __('Errors occurred:', TEXTDOMAIN),
                    implode("<br>", $result['errors'])
                );
            }
            $message .= '</div>';
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e('Generate Random Posts', TEXTDOMAIN); ?></h1>
        <?php echo $message; ?>

        <form method="post" class="form-table">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="post_count"><?php _e('Number of posts to generate', TEXTDOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="number" id="post_count" name="post_count" min="1" max="100" value="1" required>
                        <p class="description"><?php _e('How many posts should be generated (max 100)', TEXTDOMAIN); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="paragraphs"><?php _e('Paragraphs per post', TEXTDOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="number" id="paragraphs" name="paragraphs" min="1" max="50" value="5" required>
                        <p class="description"><?php _e('How many paragraphs should be in each post (max 50)', TEXTDOMAIN); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="with_image"><?php _e('Include featured image', TEXTDOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="with_image" name="with_image" value="1" checked>
                        <p class="description"><?php _e('Generate and attach a random featured image for each post', TEXTDOMAIN); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Generate Posts', TEXTDOMAIN), 'primary', 'generate_posts'); ?>
        </form>
    </div>
    <?php
}
