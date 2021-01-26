<?php

defined('BASEPATH') or exit('No direct script access allowed');



if (!$CI->db->table_exists(db_prefix() . 'feedback')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'feedback` (
  `id` int(11) NOT NULL,
  `type` varchar(40) NOT NULL,
  `customer_id` varchar(40) DEFAULT NULL,
  `feedback_submitted` varchar(40) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `project_id` varchar(40) DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'feedback`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}else{
	       
       $CI->db->empty_table('tblfeedback');
		   $table_name = db_prefix().'feedback';

    	$get_table = $CI->db->get($table_name);

    	if ($get_table) {
            if (!$CI->db->field_exists('comments', $table_name)) {
				
				
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
				 
                
            }
    	}
    }
		