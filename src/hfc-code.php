<?php

/**
 * Plugin name: Head and Footer Code
 * Version: 0.0.1
 * Author: Andrei Surdu
 * Author URL: https://zerowp.com
 * Description: Add custom code in the head and/or before closing the body.
 */

class HeadFooterCodePlugin
{
    public $pageId = 'hfc-code';

    public $nonceAction = 'hfc_custom_code';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'process']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue'], 5);
        add_action('wp_head', [$this, 'head'], 99);
        add_action('wp_footer', [$this, 'footer'], 999);
    }

    public function enqueue()
    {
        if (!empty($_GET['page']) && $_GET['page'] === $this->pageId) {
            wp_enqueue_code_editor(['type' => 'text/html']);

            wp_enqueue_style(
                'hfc-styles',
                plugin_dir_url(__FILE__) . 'style.css',
                []
            );

            wp_enqueue_script(
                'hfc-scripts',
                plugin_dir_url(__FILE__) . 'scripts.js',
                ['jquery'],
                false,
                true
            );
        }
    }

    public function head()
    {
        $code = get_option('hfc_head_code', '');

        if (!empty($code)) {
            echo wp_unslash($code);
        }
    }

    public function footer()
    {
        $code = get_option('hfc_footer_code', '');

        if (!empty($code)) {
            echo wp_unslash($code);
        }
    }

    public function process()
    {
        if (empty($_POST['_wpnonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], $this->nonceAction)) {
            return;
        }

        if (!empty($_GET['page']) && $_GET['page'] === $this->pageId) {
            update_option('hfc_head_code', $_POST['hfc_head_code']);
            update_option('hfc_footer_code', $_POST['hfc_footer_code']);
        }
    }

    public function menu()
    {
        add_options_page(
            'Head & Footer Code',
            'Head & Footer Code',
            'manage_options',
            $this->pageId,
            [$this, 'page']
        );
    }

    public function page()
    {
        ?>
        <div class="wrap">
            <h1>General Settings</h1>
            <form method="post">
                <?php wp_nonce_field($this->nonceAction) ?>
                <table class="form-table hfc-form">
                    <tr>
                        <th scope="row"><label>Head Code</label></th>
                        <td>
                            <textarea name="hfc_head_code" id="hfc_head_code" rows="10"><?php
                                echo wp_unslash(esc_textarea(get_option('hfc_head_code', '')))
                                ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label>Footer Code</label></th>
                        <td>
                            <textarea name="hfc_footer_code" id="hfc_footer_code" rows="10"><?php
                                echo wp_unslash(esc_textarea(get_option('hfc_footer_code', '')))
                                ?></textarea>
                        </td>
                    </tr>
                </table>

                <?php submit_button() ?>
            </form>
        </div>
        <?php
    }
}

new HeadFooterCodePlugin();
