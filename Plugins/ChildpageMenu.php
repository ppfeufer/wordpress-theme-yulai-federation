<?php

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

class ChildpageMenu {
    /**
     * constructor
     */
    public function __construct() {
        $this->initPlugin();
    }

    /**
     * Initialize the plugin
     */
    private function initPlugin() {
        $this->initWidget();
    }

    /**
     * Initialize the widget
     */
    public function initWidget() {
        \add_action('widgets_init', [$this, 'registerWidget']);
    }

    public function registerWidget() {
        register_widget("WordPress\Themes\YulaiFederation\Plugins\Widgets\ChildpageMenuWidget");
    }
}
