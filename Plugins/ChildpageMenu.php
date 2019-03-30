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
     * initialze the plugin
     */
    private function initPlugin() {
        $this->initWidget();
    }

    /**
     * initialze the widget
     */
    public function initWidget() {
        \add_action('widgets_init', [$this, 'registerWidget']);
    }

    public function registerWidget() {
        return \register_widget("WordPress\Themes\YulaiFederation\Plugins\Widgets\ChildpageMenuWidget");
    }
}
