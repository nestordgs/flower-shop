<?php


if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_Customize_Control')) {
    return null;
}

class Multiple_Image_Control extends WP_Customize_Control
{
    public function render_content()
    { ?>
              <label>
                <span class='customize-control-title'>Image</span>
              </label>
              <div>
                <ul class='images'></ul>
              </div>
              <div class='actions'>
                <a class="button-secondary upload">Add</a>
              </div>

              <input class="wp-editor-area" id="images-input" type="hidden" <?php $this->link(); ?>>

        <?php

    }
}
