<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
	<label class="screen-reader-text" for="s"><?php _e('Rechercher dans ce blogue','qore')?></label>
	<input type="text" value="" name="s" id="s" />
	<input type="submit" id="searchsubmit" value="<?php _e('Recherche','qore')?>" />
	<div class="clear"></div>
</form>