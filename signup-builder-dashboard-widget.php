<?php
function my_custom_dashboard_widgets() {
  global $wp_meta_boxes;
  wp_add_dashboard_widget('custom_help_widget', 'Signup Builders', 'custom_dashboard_help');
}

function custom_dashboard_help() {
echo "<iframe src='http://www.fb-520.com/wordpress/plugin/?plugin=account_builder' border=0 style='height:300px;width:100%;overflow:hidden' scrollbar=no></iframe>";
}
?>