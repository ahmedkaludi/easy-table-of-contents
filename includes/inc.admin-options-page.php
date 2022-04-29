<div id='toc' class='wrap'>
	<h1><?php _e( 'Table of Contents', 'easy-table-of-contents' ); ?></h1>
<div class="toc-tab-panel">
	  <a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#general-settings" onclick="tabToggle(event, 'general')">General</a>
	   <a class="eztoc-tablinks" id="eztoc-technical" href="#technical-support" onclick="tabToggle(event, 'technical')" data-href="no">Technical Support</a>
</div><!-- /.Tab panel -->

   <div  class="eztoc-tabcontent" id="general">
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

			<?php settings_fields( 'ez-toc-settings' ); ?>
			<?php submit_button( __( 'Save Changes', 'easy-table-of-contents' ) ); ?>
		</form>
    </div><!-- /.General Settings ended -->

	<div class="eztoc_support_div eztoc-tabcontent" id="technical">
            <strong><?php echo esc_html__('If you have any query, please write the query in below box or email us at','easy-table-of-contents') ?> <a href="mailto:support@magazine3.in">support@magazine3.in</a> <?php echo  esc_html__('we will reply to your email address shortly.','easy-table-of-contents') ?></strong>
       
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
</div>
