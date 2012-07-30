<?php

add_action('admin_menu','qore_admin_menu');

function qore_admin_menu() {
	add_menu_page('Qore', 'Qore', 'manage_options', 'qore-admininistration', 'qore_admin_page_home', QORE_IMAGES_PATH.'qore.png');
	
	/* add submenu */
	add_submenu_page('qore-admininistration', 'Qore version', 'Qore version', 'manage_options', 'qore-version', 'woothemes_framework_update_page');
	
	
	
	/* qore admin page */
	
	function qore_admin_page_home(){
	?>
		<div class="wrap">
		<?php screen_icon('qore')?>
		<h2>Qore Framework</h2>
		<h4><?php _e('Voici la page d\'administration de Qore','qore') ?></h4>
		</div>
	<?php
	};
	
	function qore_admin_page_version(){
	};
}