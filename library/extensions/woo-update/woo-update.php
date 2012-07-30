<?php
/*
 * all credit belong to the wooteam
 */

/*-----------------------------------------------------------------------------------*/
/* WooFramework Update Page */
/*-----------------------------------------------------------------------------------*/

function woothemes_framework_update_page(){
        $method = get_filesystem_method();
        $to = TEMPLATEPATH. "/";
        if(isset($_POST['password'])){
            
            $cred = $_POST;
            $filesystem = WP_Filesystem($cred);
            
        }
        elseif(isset($_POST['woo_ftp_cred'])){
            
             $cred = unserialize(base64_decode($_POST['woo_ftp_cred']));
             $filesystem = WP_Filesystem($cred);  
            
        } else {
            
           $filesystem = WP_Filesystem(); 
            
        };
        $url = admin_url( 'themes.php?page=qore_update' );
        ?>
            <div class="wrap">

            <?php
            if($filesystem == false){
                
            request_filesystem_credentials ( $url );
                
            }  else {
            ?>
            
            <?php 
            $localversion = get_option( 'qore_version' );
            $remoteversion = woo_get_fw_version();
            // Test if new version
            $upd = false;
			$loc = explode( '.',$localversion);				
			$rem = explode( '.',$remoteversion);	                
			
            if( $loc[0] < $rem[0] )  
            	$upd = true;
            elseif ( $loc[1] < $rem[1] )
            	$upd = true;
            elseif( $loc[2] < $rem[2] )
            	$upd = true;

            ?>
            <?php screen_icon('qore')?>
            <h2>Qore Update</h2>
            <span style="display:none"><?php echo $method; ?></span>
            <form method="post"  enctype="multipart/form-data" id="wooform" action="<?php /* echo $url; */ ?>">
                
                <?php if( $upd ) { ?>
                <?php wp_nonce_field( 'update-options' ); ?>
                <h3>A new version of Qore is available.</h3>
                <p>&rarr; <strong>Your version:</strong> <?php echo $localversion; ?></p>
                
                <p>&rarr; <strong>Current Version:</strong> <?php echo $remoteversion; ?></p>
                
                <input type="submit" class="button" value="Update Framework" />
                <?php } else { ?>                
                <h3>You have the latest version of Qore</h3>
                <p>&rarr; <strong>Your version:</strong> <?php echo $localversion; ?></p>
                <?php } ?>
                <input type="hidden" name="woo_update_save" value="save" />
                <input type="hidden" name="woo_ftp_cred" value="<?php echo base64_encode(serialize($_POST)); ?>" />

            </form>
            <?php } ?>
            </div>
            <?php
};

/*-----------------------------------------------------------------------------------*/
/* WooFramework Update Head */
/*-----------------------------------------------------------------------------------*/

function woothemes_framework_update_head(){

  if(isset($_REQUEST['page'])){
	
	// Sanitize page being requested.
	$_page = strtolower( strip_tags( trim( $_REQUEST['page'] ) ) );
	
	if( $_page == 'qore_update'){
              
		//Setup Filesystem 
		$method = get_filesystem_method(); 
		
		if(isset($_POST['woo_ftp_cred'])){ 
			 
			$cred = unserialize(base64_decode($_POST['woo_ftp_cred']));
			$filesystem = WP_Filesystem($cred);
			
		} else {
			
		   $filesystem = WP_Filesystem(); 
			
		};     
	
		if($filesystem == false && $_POST['upgrade'] != 'Proceed'){
			
			function woothemes_framework_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo "<div id='filesystem-warning' class='updated fade'><p>Failed: Filesystem preventing downloads. ( ". $method .")</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_filesystem_warning' );
				return;
		}
		if(isset($_REQUEST['woo_update_save'])){
		
			// Sanitize action being requested.
			$_action = strtolower( trim( strip_tags( $_REQUEST['woo_update_save'] ) ) );
		
		if( $_action == 'save' ){
		
		$temp_file_addr = download_url( 'http://labs.quiboweb.com/qore-update/qore.zip' );
		
		if ( is_wp_error($temp_file_addr) ) {
			
			$error = $temp_file_addr->get_error_code();
		
			if($error == 'http_no_url') {
			//The source file was not found or is invalid
				function woothemes_framework_update_missing_source_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_missing_source_warning' );
			} else {
				function woothemes_framework_update_other_upload_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_other_upload_warning' );
				
			}
			
			return;
	
		  } 
		//Unzipp it
		$to = get_theme_root(). "/";
		
		$dounzip = unzip_file($temp_file_addr, $to);
		
		unlink($temp_file_addr); // Delete Temp File
		
		if ( is_wp_error($dounzip) ) {
			
			//DEBUG
			$error = $dounzip->get_error_code();
			$data = $dounzip->get_error_data($error);
			//echo $error. ' - ';
			//print_r($data);
							
			if($error == 'incompatible_archive') {
				//The source file was not found or is invalid
				function woothemes_framework_update_no_archive_warning() {
					echo "<div id='woo-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_no_archive_warning' );
			} 
			if($error == 'empty_archive') {
				function woothemes_framework_update_empty_archive_warning() {
					echo "<div id='woo-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_empty_archive_warning' );
			}
			if($error == 'mkdir_failed') {
				function woothemes_framework_update_mkdir_warning() {
					echo "<div id='woo-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_mkdir_warning' );
			}  
			if($error == 'copy_failed') {
				function woothemes_framework_update_copy_fail_warning() {
					echo "<div id='woo-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
				}
				add_action( 'admin_notices', 'woothemes_framework_update_copy_fail_warning' );
			}
				
			return;
	
		} 
		
		function woothemes_framework_updated_success() {
			echo "<div id='framework-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
		}
		add_action( 'admin_notices', 'woothemes_framework_updated_success' );
		
		}
	}
	} //End user input save part of the update
 }
}
                             
add_action( 'admin_head','woothemes_framework_update_head' );

/*-----------------------------------------------------------------------------------*/
/* WooFramework Version Getter */
/*-----------------------------------------------------------------------------------*/

function woo_get_fw_version($url = ''){
	
	if(!empty($url)){
		$fw_url = $url;
	} else {
    	$fw_url = 'http://labs.quiboweb.com/qore-update/qore-changelog.txt';
    }
    
	$temp_file_addr = download_url($fw_url);
	if(!is_wp_error($temp_file_addr) && $file_contents = file($temp_file_addr)) {
        foreach ($file_contents as $line_num => $line) {
                            
                $current_line =  $line;
                
                if($line_num > 1){    // Not the first or second... dodgy :P
                    
                    if (preg_match( '/^[0-9]/', $line)) {
                                            
                            $current_line = stristr($current_line,"version" );
                            $current_line = preg_replace( '~[^0-9,.]~','',$current_line);
                            $output = $current_line;
                            break;
                    }
                }     
        }
        unlink($temp_file_addr);
        return $output;

        
    } else {
        return 'Currently Unavailable';
    }

}