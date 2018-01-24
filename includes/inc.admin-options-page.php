<div id='toc' class='wrap'>
	<h1><?php _e( 'Table of Contents', 'easy-table-of-contents' ); ?></h1>

	<form method="post" action="<?php echo esc_url( self_admin_url( 'options.php' ) ); ?>">

		<div class="metabox-holder">

			<div class="postbox">
				<h3><span><?php _e( 'General', 'easy-table-of-contents' ); ?></span></h3>

				<div class="inside">

					<table class="form-table">

						<?php do_settings_fields( 'ez_toc_settings_general', 'ez_toc_settings_general' ); ?>

					</table>

				</div><!-- /.inside -->
			</div><!-- /.postbox -->

		</div><!-- /.metabox-holder -->

		<div class="metabox-holder">

			<div class="postbox">
				<h3><span><?php _e( 'Appearance', 'easy-table-of-contents' ); ?></span></h3>

				<div class="inside">

					<table class="form-table">

						<?php do_settings_fields( 'ez_toc_settings_appearance', 'ez_toc_settings_appearance' ); ?>

					</table>

				</div><!-- /.inside -->
			</div><!-- /.postbox -->

		</div><!-- /.metabox-holder -->

		<div class="metabox-holder">

			<div class="postbox">
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
</div>
