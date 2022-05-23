<div id='toc' class='wrap'>
  <img src="<?php echo plugins_url( 'assets/eztoc-logo.png', dirname(__FILE__) ) ?>" alt="tocwp" srcset="<?php echo plugins_url( 'assets/eztoc-logo.png', dirname(__FILE__) ) ?> 1x, <?php echo plugins_url( 'assets/eztoc-logo.png', dirname(__FILE__) ) ?> 2x" >
<div class="toc-tab-panel">
	  <a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#general-settings" onclick="tabToggle(event, 'general')">Settings</a>
    <?php 
      $pro = '';

      if (function_exists('ez_toc_pro_activation_link')) {
        $pro = '<a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#eztoc-prosettings" onclick="tabToggle(event, "prosettings")">PRO Settings</a>';
      }?>
      <?php echo $pro; ?>
	   <a class="eztoc-tablinks" id="eztoc-technical" href="#technical-support" onclick="tabToggle(event, 'technical')" data-href="no">Help & Support</a>
	    <?php
     if (!function_exists('ez_toc_pro_activation_link')) {?>
      <a class="eztoc-tablinks" id="eztoc-freevspro" href="#freevspro" onclick="tabToggle(event, 'freevspro')" data-href="no">Free vs PRO</a>
     <?php }
     if (function_exists('ez_toc_pro_activation_link')) {?>
      <a class="eztoc-tablinks" id="eztoc-license" href="#license" onclick="tabToggle(event, 'license')" data-href="no">License</a>
     <?php } ?>
</div><!-- /.Tab panel -->
   <div  class="eztoc-tabcontent" id="general">
   	<div id="eztoc-tabs" style="margin-top: 10px;"><a href="#eztoc-general">General</a> | <a href="#eztoc-appearance" >Appearance</a> | <a href="#eztoc-advanced" >Advanced</a></div>
		<form method="post" action="<?php echo esc_url( self_admin_url( 'options.php' ) ); ?>">

			<div class="metabox-holder">

				<div class="postbox" id="eztoc-general">
					<h3><span><?php _e( 'General', 'easy-table-of-contents' ); ?></span></h3>

					<div class="inside">

						<table class="form-table">

							<?php do_settings_fields( 'ez_toc_settings_general', 'ez_toc_settings_general' ); ?>

						</table>

					</div><!-- /.inside -->
				</div><!-- /.postbox -->

			</div><!-- /.metabox-holder -->

			<div class="metabox-holder">

				<div class="postbox" id="eztoc-appearance">
					<h3><span><?php _e( 'Appearance', 'easy-table-of-contents' ); ?></span></h3>

					<div class="inside">

						<table class="form-table">

							<?php do_settings_fields( 'ez_toc_settings_appearance', 'ez_toc_settings_appearance' ); ?>

						</table>

					</div><!-- /.inside -->
				</div><!-- /.postbox -->

			</div><!-- /.metabox-holder -->

			<div class="metabox-holder">

				<div class="postbox" id="eztoc-advanced">
					<h3><span><?php _e( 'Advanced', 'easy-table-of-contents' ); ?></span></h3>

					<div class="inside">

						<table class="form-table">

							<?php do_settings_fields( 'ez_toc_settings_advanced', 'ez_toc_settings_advanced' ); ?>

						</table>

					</div><!-- /.inside -->
				</div><!-- /.postbox -->

			</div><!-- /.metabox-holder -->
<?php if (function_exists('ez_toc_pro_activation_link')) {?>
      <div class="metabox-holder">

        <div class="postbox" id="eztoc-prosettings">

          <div class="inside">

            <table class="form-table">

              <?php do_settings_fields( 'ez_toc_settings_prosettings', 'ez_toc_settings_prosettings' ); ?>

            </table>

          </div><!-- /.inside -->
        </div><!-- /.postbox -->

      </div><!-- /.metabox-holder -->
<?php } ?>
			<?php settings_fields( 'ez-toc-settings' ); ?>
			<?php submit_button( __( 'Save Changes', 'easy-table-of-contents' ) ); ?>
		</form>
    </div><!-- /.General Settings ended -->

	<div class="eztoc_support_div eztoc-tabcontent" id="technical">
            <strong><?php echo esc_html__('If you have any query, please write the query in below box or email us at','easy-table-of-contents') ?> <a href="mailto:team@magazine3.in">team@magazine3.in</a> <?php echo  esc_html__('we will reply to your email address shortly.','easy-table-of-contents') ?></strong>
       
            <ul>
                <li>
                  <label class="support-label">Email<span class="star-mark">*</span></label>
                   <div class="support-input">
                   		<input type="text" id="eztoc_query_email" name="eztoc_query_email" placeholder="email" required>
                   </div>
                </li>
                <li>
                    <label class="support-label">Query<span class="star-mark">*</span></label>                    
                    <div class="support-input"><textarea rows="5" cols="60" id="eztoc_query_message" name="eztoc_query_message" placeholder="Write your query"></textarea>
                    </div>
                    <div class="clear"> </div>
                    <span class="eztoc-query-success eztoc-result eztoc_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly','easy-table-of-contents'); ?></span>
                    <span class="eztoc-query-error eztoc-result eztoc_hide"><?php echo esc_html__('Message not sent. please check your network connection','easy-table-of-contents'); ?></span>
                </li>
                <li><button class="button eztoc-send-query"><?php echo esc_html__('Send Message','easy-table-of-contents'); ?></button></li>
            </ul>            
                   
        </div><!-- /.Technical support div ended -->

        <div class="eztoc_support_div eztoc-tabcontent" id="freevspro">
            <div class="eztoc-wrapper">
       <div class="table basic">
           <div class="price-section">
               <div class="price-area">
                   <div class="inner-area">
                       <span class="text">
                         &dollar;
                       </span>
                       <span class="price">00</span>
                   </div>
               </div>
           </div>
           <div class="package-name">
    
           </div>
           <div class="features">
               <li>
                   <span class="list-name">Auto Insert in Posts, Pages, CPT etc </span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">6 themes to change the TOC design</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Smooth Scroll</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Change Alignment</span> 
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Change Width,Font Size etc </span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
                <li>
                   <span class="list-name">Gutenberg Block</span>
                   <span class="icon cross"><i class="far fa-times-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Elementor Widget</span>
                   <span class="icon cross"><i class="far fa-times-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Fixed/Sticky TOC</span>
                   <span class="icon cross"><i class="far fa-times-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Full AMP Support</span>
                   <span class="icon cross"><i class="far fa-times-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Exclude headings by its class</span>
                   <span class="icon cross"><i class="far fa-times-circle"></i></span>
               </li>
           </div>
       </div>
       <div class="table Premium">
           <div class="price-section">
               <div class="price-area">
                   <div class="inner-area">
                       <span class="text">
                         &dollar;
                       </span>
                       <span class="price">49</span>
                   </div>
               </div>
           </div>
           <div class="package-name">
            
           </div>
           <div class="features">
               <li>
                   <span class="list-name">All the benefits of Free</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Gutenberg Block</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Elementor Widget</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Fixed/Sticky TOC</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Full AMP Support</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               <li>
                   <span class="list-name">Exclude headings by its class</span>
                   <span class="icon check"><i class="fas fa-check-circle"></i></span>
               </li>
               
               <div class="btn"><button>Purchase</button></div>
           </div>
       </div>
   </div>
        </div><!-- /.freevspro div ended -->

        <div id="license" class="eztoc_support_div eztoc-tabcontent">
       <?php
        do_action("admin_upgrade_license_page");
       ?>
      </div>
</div>
