<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * { For digital products : is_digital column added in product_master table }
 	 * @return bool
     */
    public function up()
    {
        
    	$CI =& get_instance();
    	$table_name = db_prefix().'tblfeedback';

    	$get_table = $CI->db->get($table_name);

    	if ($get_table) {
            if (!$CI->db->field_exists('is_digital', $table_name)) {
                $alter_qry = $CI->db->query("ALTER TABLE tblfeedback
                    DROP COLUMN coding,
					DROP COLUMN communication,
					DROP COLUMN services,
					DROP COLUMN recommendation,
					DROP COLUMN message,
					DROP COLUMN customerid,
					DROP COLUMN projectid,
					ADD `customer_id` varchar(40) NULL,
					ADD `project_id` varchar(40) NULL,
					ADD `type` varchar(40) NULL,
					ADD `comments` varchar(225) NULL
                    AFTER `feedback_submitted`");
                 $query = $CI->db->query($alter_qry);
                if ($results) {
                    return true;
                }else{
                    return false;
                }
            }
    	}
    }
}

