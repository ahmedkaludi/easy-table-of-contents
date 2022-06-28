<div id='toc' class='wrap'>
  <a href="https://tocwp.com/" target="_blank" >
  <img src="<?php echo plugins_url( 'assets/eztoc-logo.png', dirname(__FILE__) ) ?>" alt="tocwp" srcset="<?php echo plugins_url( 'assets/eztoc-logo.png', dirname(__FILE__) ) ?> 1x, <?php echo plugins_url( 'assets/eztoc-logo.png', dirname(__FILE__) ) ?> 2x" >
  </a>
<div class="toc-tab-panel">
    <a id="eztoc-welcome" class="eztoc-tablinks" data-href="no" href="#welcome" onclick="tabToggle(event, 'welcome')">Welcome</a>
	  <a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#general-settings" onclick="tabToggle(event, 'general')">Settings</a>
    <?php 
      $pro = '';

      if (function_exists('ez_toc_pro_activation_link')) {
        $pro = '<a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#eztoc-prosettings" onclick="tabToggle(event, "prosettings")">PRO Settings</a>';
      }?>
      <?php echo $pro; ?>

      <?php 
     if (!function_exists('ez_toc_pro_activation_link')) {?>
      <a class="eztoc-tablinks" id="eztoc-freevspro" href="#freevspro-support" onclick="tabToggle(event, 'freevspro')" data-href="no">Free vs PRO</a>
     <?php }
     ?>
	   <a class="eztoc-tablinks" id="eztoc-technical" href="#technical-support" onclick="tabToggle(event, 'technical')" data-href="no">Help & Support</a>
       <a class="eztoc-tablinks" id="eztoc-upgrade" href="https://tocwp.com/pricing/" target="_blank" >UPGRADE to PRO</a>
	    <?php
     
     if (function_exists('ez_toc_pro_activation_link')) {
        $license_info = get_option("easytoc_pro_upgrade_license");
        $license_exp = date('Y-m-d', strtotime($license_info['pro']['license_key_expires']));
        $today = date('Y-m-d');
        $exp_date = $license_exp;
        $date1 = date_create($today);
        $date2 = date_create($exp_date);
        $diff = date_diff($date1,$date2);
        $days = $diff->format("%a");
        $days = intval($days);?>
      <a class="eztoc-tablinks" id="eztoc-license" href="#license" onclick="tabToggle(event, 'license')" data-href="no">License</a>
      <?php 
      if( $days < 30 ){?>
      <span class="dashicons dashicons-warning" style="color: #ffb229;position: relative;top: 15px;left: -10px;"></span>
     <?php } }?>
</div><!-- /.Tab panel -->
        <div class="eztoc_support_div eztoc-tabcontent" id="welcome" style="display: block;">
            <p style="font-weight: bold;font-size: 30px;color: #000;">Thank YOU for using Easy Table of Content. </p>
            <p style="font-size: 18px;padding: 0 10%;line-height: 1.7;color: #000;">We strive to create the best TOC solution in WordPress. Our dedicated development team does continious development and innoviation to make sure we are able to meet your demand.</p> 
            <p style="font-size: 16px;font-weight: 600;color: #000;">Please support us by Upgrading to Premium verison.</p>
            <a target="_blank" href="https://tocwp.com/pricing/"><button class="button-toc" style="display: inline-block;font-size: 20px;">
            <span>YES! I want to Support by UPGRADING.</span></button></a>
            <a href="<?php echo add_query_arg( 'page', 'table-of-contents', admin_url( 'options-general.php' ) ); ?>" style="text-decoration: none;"><button class="button-toc1" style="display: block;text-align: center;border: 0;margin: 0 auto;background: none;">
            <span style="cursor: pointer;">No Thanks, I will stick with FREE version for now.</span></button></a>
         </div><!-- /.Welcome ended -->
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
          <h3><span><?php _e( 'PRO Settings', 'easy-table-of-contents' ); ?></span></h3>   
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
   <div class="eztoc-form-page-ui">
   <div class="eztoc-left-side">
            <p><?php echo esc_html__('We are dedicated to provide Technical support & Help to our users. Use the below form for sending your questions.','easy-table-of-contents') ?> </p>
            <p><?php echo  esc_html__('You can also contact us from ','easy-table-of-contents') ?><a href="https://tocwp.com/contact/">https://tocwp.com/contact/</a>.</p>
   
            <div class="eztoc_support_div_form" id="technical-form">
            <ul>
                <li>
                  <label class="support-label">Email<span class="star-mark">*</span></label>
                   <div class="support-input">
                   		
                   		<input type="text" id="eztoc_query_email" name="eztoc_query_email" placeholder="Enter your Email" required>
                   </div>
                </li>
                
                <li>
                    <label class="support-label">Query<span class="star-mark">*</span></label>                    
                   
                    <div class="support-input"><textarea rows="5" cols="50" id="eztoc_query_message" name="eztoc_query_message" placeholder="Write your query"></textarea>
                    </div>
                
                  
                </li>
                
            </ul>            
                <li>
                    <div class="eztoc-customer-type">
                      <label class="support-label">Type</label>
                      <div class="support-input">
                         <select name="eztoc_customer_type" id="eztoc_customer_type">
                             <option value="select">Select Customer Type</option>
                             <option value="paid">Paid<span> (Response within 24 hrs)</span></option>
                             <option value="free">Free<span> ( Avg Response within 48-72 hrs)</span></option>
                         </select>
                      </div>
                      </div>
                  </li>
                <li><button class="button button-primary eztoc-send-query"><?php echo esc_html__('Send Support Request','easy-table-of-contents'); ?></button></li>
            </ul> 
            <div class="clear"> </div>
                    <span class="eztoc-query-success eztoc-result eztoc_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly','easy-table-of-contents'); ?></span>
                    <span class="eztoc-query-error eztoc-result eztoc_hide"><?php echo esc_html__('Message not sent. please check your network connection','easy-table-of-contents'); ?></span>
            </div> 
       </div>
    <div class="eztoc-right-side">
           <div class="eztoc-bio-box" id="ez_Bio">
                <h1>Vision & Mission</h1>
                <p class="eztoc-p">We strive to provide the best TOC in the world.</p>
                <section class="eztoc_dev-bio"> 
                    <div class="ezoc-bio-wrap">
                    <img width="50px" height="50px" src="<?php echo plugins_url( 'assets/ahmed-kaludi.jpg', dirname(__FILE__) ) ?>">
                        <p>Lead Dev</p>
                    </div>
                    <div class="ezoc-bio-wrap">
                    <img width="50px" height="50px" src="<?php echo plugins_url( 'assets/Mohammed-kaludi.jpeg', dirname(__FILE__) ) ?>">
                        <p>Developer</p>
                    </div>
                    <div class="ezoc-bio-wrap">
                    <img width="50px" height="50px" src="<?php echo plugins_url( 'assets/zabi.jpg', dirname(__FILE__) ) ?>">
                        <p>Developer</p>
                    </div>
                </section>
              <p class="eztoc_boxdesk"> Delivering a good user experience means a lot to us, so we try our best to reply each and every question.</p>
                <p class="company-link"> Support the innovation & development by upgrading to PRO <a href="https://tocwp.com/pricing/">I Want To Upgrade!</a></p>
           </div>
        </div>   </div>   </div>        <!-- /.Technical support div ended -->

        <div class="eztoc_support_div eztoc-tabcontent" id="freevspro">
            <div class="eztoc-wrapper">
            <div class="eztoc-wr">
            <div class="etoc-eztoc-img">
                <span class="sp_ov"></span>
            </div>
            <div class="etoc-eztoc-cnt">
                    <h1><?php _e( 'UPGRADE to PRO Version'); ?></h1>
          <p><?php _e( 'Take your Table of Contents to the NEXT Level!', 'easy-table-of-contents' ); ?></p>
                    <a class="buy" href="#upgrade"><?php _e( 'Purchase Now', 'easy-table-of-contents' ); ?></a>
            </div>
            <div class="pvf">
                <div class="ext">
                    <div class="ex-1 e-1">
                        <h4><?php _e( 'Premium Features', 'easy-table-of-contents' ); ?></h4>
                        <p><?php _e( 'Easy TOC Pro will enhances your website table of contents and takes it to a next level to help you reach more engagement and personalization with your users.', 'easy-table-of-contents' ); ?></p>
                    </div>
                    <div class="ex-1 e-2">
                        <h4><?php _e( 'Continuous Innovation', 'easy-table-of-contents' ); ?></h4>
                        <p><?php _e( 'We are planning to continiously build premium features and release them. We have a roadmap and we listen to our customers to turn their feedback into reality.', 'easy-table-of-contents' ); ?></p>
                    </div>
                    <div class="ex-1 e-3">
                        <h4><?php _e( 'Tech Support', 'easy-table-of-contents' ); ?></h4>
                        <p><?php _e( 'Get private ticketing help from our full-time technical staff & developers who helps you with the technical issues.', 'easy-table-of-contents' ); ?></p>
                    </div>
                </div><!-- /. ext -->
                <div class="pvf-cnt">
                    <div class="pvf-tlt">
                        <h2><?php _e( 'Compare Pro vs. Free Version', 'easy-table-of-contents' ); ?></h2>
                        <span><?php _e( 'See what you\'ll get with the professional version', 'easy-table-of-contents' ); ?></span>
                    </div>
                    <div class="pvf-cmp">
                        <div class="fr">
                            <h1>FREE</h1>
                            <div class="fr-fe">
                                <div class="fe-1">
                                    <h4><?php _e( 'Continious Development', 'easy-table-of-contents' ); ?></h4>
                                    <p><?php _e( 'We take bug reports and feature requests seriously. We’re continiously developing &amp; improve this product for last 2 years with passion and love.', 'easy-table-of-contents' ); ?></p>
                                </div>
                                <div class="fe-1">
                                    <h4><?php _e( '50+ Features', 'easy-table-of-contents' ); ?></h4>
                                    <p><?php _e( 'We\'re constantly expanding the plugin and make it more useful. We have wide variety of features which will fit any use-case.', 'easy-table-of-contents' ); ?></p>
                                </div>
                            </div><!-- /. fr-fe -->
                        </div><!-- /. fr -->
                        <div class="pr">
                            <h1>PRO</h1>
                            <div class="pr-fe">
                                <span><?php _e( 'Everything in Free, and:', 'easy-table-of-contents' ); ?></span>
                                <div class="fet">
                                    <div class="fe-2">
                                        <div class="fe-t">
                                            <img src="<?php echo plugins_url( 'assets/right-tick.png', dirname(__FILE__) ) ?>">
                                            <h4><?php _e( 'Gutenberg Block', 'easy-table-of-contents' ); ?></h4>
                                        </div>
                                        <p><?php _e( 'Easily create TOC in Gutenberg block without the need any coding or shortcode.', 'easy-table-of-contents' ); ?></p>
                                    </div>
                                    <div class="fe-2">
                                        <div class="fe-t">
                                            <img src="<?php echo plugins_url( 'assets/right-tick.png', dirname(__FILE__) ) ?>">
                                            <h4><?php _e( 'Elementor Widget', 'easy-table-of-contents' ); ?></h4>
                                        </div>
                                        <p><?php _e( 'Easily create TOC in Elementor with the widget without the need any coding or shortcode.', 'easy-table-of-contents' ); ?></p>
                                    </div>

                                    <div class="fe-2">
                                        <div class="fe-t">
                                            <img src="<?php echo plugins_url( 'assets/right-tick.png', dirname(__FILE__) ) ?>">
                                            <h4>Fixed/Sticky TOC</h4>
                                        </div>
                                        <p>Users can faster find the content they want with sticky</p>
                                    </div>

                                    
                                    <div class="fe-2">
                                        <div class="fe-t">
                                            <img src="<?php echo plugins_url( 'assets/right-tick.png', dirname(__FILE__) ) ?>">
                                            <h4>Full AMP Support</h4>
                                        </div>
                                        <p>Generates a table of contents with your existing setup and makes them AMP automatically.</p>
                                    </div>
                                    <div class="fe-2">
                                        <div class="fe-t">
                                            <img src="<?php echo plugins_url( 'assets/right-tick.png', dirname(__FILE__) ) ?>">
                                            <h4>Continious Updates</h4>
                                        </div>
                                        <p>We're continiously updating our premium features and releasing them.</p>
                                    </div>
                                    <div class="fe-2">
                                        <div class="fe-t">
                                            <img src="<?php echo plugins_url( 'assets/right-tick.png', dirname(__FILE__) ) ?>">
                                            <h4>Documentation</h4>
                                        </div>
                                        <p>We create tutorials for every possible feature and keep it updated for you.</p>
                                    </div>
                                </div><!-- /. fet -->
                                <div class="pr-btn">
                                    <a href="#upgrade">Upgrade to Pro</a>
                                </div><!-- /. pr-btn -->
                            </div><!-- /. pr-fe -->
                        </div><!-- /.pr -->
                    </div><!-- /. pvf-cmp -->
                </div><!-- /. pvf-cnt -->
                <div id="upgrade" class="amp-upg">
                    <div class="upg-t">
                        <h2>Let's Upgrade Your Easy Table of Contents</h2>
                        <span>Choose your plan and upgrade in minutes!</span>
                    </div>
                    <div class="etoc-pri-lst">
                        <div class="pri-tb">
                            <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=1" target="_blank">
                                <h5>PERSONAL</h5>
                                <span class="d-amt"><sup>$</sup>49</span>
                                <span class="amt"><sup>$</sup>49</span>
                                <span class="s-amt">(Save $59)</span>
                                <span class="bil">Billed Annually</span>
                                <span class="s">1 Site License</span>
                                <span class="e">Tech Support</span>
                                <span class="f">1 year Updates </span>
                                <span class="etoc-sv">Pro Features </span>
                                <span class="pri-by">Buy Now</span>
                            </a>
                        </div>
                        <div class="pri-tb rec">
                            <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=2" target="_blank">
                                <h5>MULTIPLE</h5>
                                <span class="d-amt"><sup>$</sup>69</span>
                                <span class="amt"><sup>$</sup>69</span>
                                <span class="s-amt">(Save $79)</span>
                                <span class="bil">Billed Annually</span>
                                <span class="s">3 Site License</span>
                                <span class="e">Tech Support</span>
                                <span class="f">1 year Updates</span>
                                <span class="etoc-sv">Save 78%</span>
                                <span class="pri-by">Buy Now</span>
                                <span class="etoc-rcm">RECOMMENDED</span>
                            </a>
                        </div>
                        <div class="pri-tb">
                            <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=3" target="_blank">
                                <h5>WEBMASTER</h5>
                                <span class="d-amt"><sup>$</sup>79</span>
                                <span class="amt"><sup>$</sup>79</span>
                                <span class="s-amt">(Save $99)</span>
                                <span class="bil">Billed Annually</span>
                                <span class="s">10 Site License</span>
                                <span class="e">Tech Support</span>
                                <span class="f">1 year Updates</span>
                                <span class="etoc-sv">Save 83%</span>
                                <span class="pri-by">Buy Now</span>
                            </a>
                        </div>
                        <div class="pri-tb">
                            <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=4" target="_blank">
                                <h5>FREELANCER</h5>
                                <span class="d-amt"><sup>$</sup>99</span>
                                <span class="amt"><sup>$</sup>99</span>
                                <span class="s-amt">(Save $119)</span>
                                <span class="bil">Billed Annually</span>
                                <span class="s">25 Site License</span>
                                <span class="e">Tech Support</span>
                                <span class="f">1 year Updates</span>
                                <span class="etoc-sv">Save 90%</span>
                                <span class="pri-by">Buy Now</span>
                            </a>
                        </div>
                        <div class="pri-tb">
                            <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=5" target="_blank">
                                <h5>AGENCY</h5>
                                <span class="d-amt"><sup>$</sup>199</span>
                                <span class="amt"><sup>$</sup>199</span>
                                <span class="s-amt">(Save $199)</span>
                                <span class="bil">Billed Annually</span>
                                <span class="s">Unlimited Sites</span>
                                <span class="e">E-mail support</span>
                                <span class="f">1 year Updates</span>
                                <span class="etoc-sv">UNLIMITED</span>
                                <span class="pri-by">Buy Now</span>
                            </a>
                        </div>
                        <div class="pri-tb">
                            <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=6" target="_blank">
                                <h5>LIFETIME</h5>
                                <span class="d-amt"><sup>$</sup>499</span>
                                <span class="amt"><sup>$</sup>499</span>
                                <span class="s-amt">(Save $199)</span>
                                <span class="bil">Billed Annually</span>
                                <span class="s">Unlimited Sites</span>
                                <span class="e">Unlimited E-mail support</span>
                                <span class="f">Lifetime License</span>
                                <span class="etoc-sv">UNLIMITED</span>
                                <span class="pri-by">Buy Now</span>
                            </a>
                        </div>
                    </div><!-- /.pri-lst -->
                    <div class="tru-us">
                        <img src="<?php echo plugins_url( 'assets/toc-rating.png', dirname(__FILE__) ) ?>">
                        <h2>Used by more than 300,000+ Users!</h2>
                        <p>More than 300k Websites, Blogs &amp; E-Commerce shops are powered by our easy table of contents plugin making it the #1 Independent TOC plugin in WordPress.</p>
                        <a href="https://wordpress.org/support/plugin/easy-table-of-contents/reviews/?filter=5" target="_blank">Read The Reviews</a>
                    </div>
                </div><!--/ .amp-upg -->
                <div class="ampfaq">
                    <h2>Frequently Asked Questions</h2>
                    <div class="faq-lst">
                        <div class="lt">
                            <ul>
                                <li>
                                    <span>Is there a setup fee?</span>
                                    <p>No. There are no setup fees on any of our plans</p>
                                </li>
                                <li>
                                    <span>What's the time span for your contracts?</span>
                                    <p>All the plans are year-to-year which are subscribed annually except for lifetime plan.</p>
                                </li>
                                <li>
                                    <span>What payment methods are accepted?</span>
                                    <p>We accepts PayPal and Credit Card payments.</p>
                                </li>
                                <li>
                                    <span>Do you offer support if I need help?</span>
                                    <p>Yes! Top-notch customer support for our paid customers is key for a quality product, so we’ll do our very best to resolve any issues you encounter via our support page.</p>
                                </li>
                                <li>
                                    <span>Can I use the plugins after my subscription is expired?</span>
                                    <p>Yes, you can use the plugins but you will not get future updates for those plugins.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="rt">
                            <ul>
                                <li>
                                    <span>Can I cancel my membership at any time?</span>
                                    <p>Yes. You can cancel your membership by contacting us.</p>
                                </li>
                                <li>
                                    <span>Can I change my plan later on?</span>
                                    <p>Yes. You can upgrade your plan by contacting us.</p>
                                </li>
                                <li>
                                    <span>Do you offer refunds?</span>
                                    <p>You are fully protected by our 100% Money Back Guarantee Unconditional. If during the next 14 days you experience an issue that makes the plugin unusable and we are unable to resolve it, we’ll happily offer a full refund.</p>
                                </li>
                                <li>
                                    <span>Do I get updates for the premium plugin?</span>
                                    <p>Yes, you will get updates for all the premium plugins until your subscription is active.</p>
                                </li>
                            </ul>
                        </div>
                    </div><!-- /.faq-lst -->
                    <div class="f-cnt">
                        <span>I have other pre-sale questions, can you help?</span>
                        <p>All the plans are year-to-year which are subscribed annually.</p>
                        <a href="https://tocwp.com/contact/'?utm_source=tocwp-plugin&utm_medium=addon-card'" target="_blank">Contact a Human</a>
                    </div><!-- /.f-cnt -->
                </div><!-- /.faq -->
            </div><!-- /. pvf -->
        </div>
            </div>
        </div><!-- /.freevspro div ended -->

        <div id="license" class="eztoc_support_div eztoc-tabcontent">
       <?php
        do_action("admin_upgrade_license_page");
       ?>
      </div>
</div>
