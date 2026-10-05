<?php
if (!defined('ABSPATH')) exit;

trait Block_Functions
{
    function register_gutenberg_block()
    {
        register_block_type(
            plugin_dir_path(__FILE__) . '../blocks/login-form',
            array(
                'render_callback' => array($this, 'render_login_block'),
            )
        );
    }

    function render_login_block($attributes)
    {
        return $this->shortcode($attributes);
    }
}