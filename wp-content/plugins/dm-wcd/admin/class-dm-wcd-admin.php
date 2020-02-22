<?php

class DM_wcd_Admin {

    private $plugin_name;

    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {

    }

    public function enqueue_scripts() {

    }

    function init_sessions() {
        if (!session_id()) {
            session_start();
        }
    }
}
