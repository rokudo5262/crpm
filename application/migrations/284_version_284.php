<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_284 extends CI_Migration {
    public function up() {
        add_option('ip_address',$this->input->ip_address());
    }
    public function down() {
        delete_option('ip_address');
    }
}
?>
