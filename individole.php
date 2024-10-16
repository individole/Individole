<?php

use ScssPhp\ScssPhp\Compiler;
use BorlabsCookie\Cookie\Cookie;
//use WPMedia\Cloudflare;
//use ScssPhp\ScssPhp\Server;

#[AllowDynamicProperties]
class individole {
	function __construct() {
		global $individole_version;
		
		$this->initDatabase();
		
		$this->version 							= '2.13.22';
		
		$this->path['individole']				= rtrim(ABSPATH, '/').'/wp-content/individole/'.$individole_version;
		
		$this->url['base_individole']			= dirname(dirname(get_stylesheet_directory_uri())).'/individole';
		$this->url['individole']				= $this->url['base_individole'].'/'.$individole_version;
		$this->url['individole_fonts']		= $this->url['individole'].'/_fonts';
		
		$this->path['base']						= get_stylesheet_directory();
		$this->path['individole_acf']			= $this->path['individole'] . '/_acf';
		$this->path['acf']						= $this->path['base'] . '/_acf';
		$this->path['fonts']						= $this->path['base'] . '/_fonts';
		$this->path['gfx']						= $this->path['individole'] . '/_images';
		$this->path['images']					= $this->path['base'] . '/_images';
		$this->path['css']						= $this->path['individole'] . '/_css/css';
		$this->path['flexible_modules']		= $this->path['individole'] . '/_acf/_flexible_modules';
		$this->path['tools']						= $this->path['individole'] . '/_tools';
		$this->path['tools_extra']				= $this->path['base'] . '/_tools/_individole';
		$this->path['dashboard']				= $this->path['base'] . '/_dashboard';
		$this->path['libraries']				= $this->path['individole'].'/_libraries';
		
		$this->l = substr( get_bloginfo ( 'language' ), 0, 2 );
		$this->l_default = $this->l;
		
		$timezone = wp_timezone_string();
		if(!empty($timezone) && substr($timezone, 0, 1 ) !== "+" && substr($timezone, 0, 1 ) !== "-") {
			date_default_timezone_set($timezone);
		}
		
		if(isset($_SERVER['REQUEST_SCHEME'])) {
			$server_site_url = $_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'];
		
		} else if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
			$server_site_url = 'https://' . $_SERVER['HTTP_HOST'];
		}
		
		(defined("ACF_FOLDER")) 	? $this->acf = ACF_FOLDER 					: $this->acf = '_acf';
		(!defined('WP_SITEURL')) 	? $this->WP_SITEURL = $server_site_url 		: $this->WP_SITEURL = WP_SITEURL;
		
		$this->users							= array();
		$this->users_options					= array();
		
		$this->tools_separated = array(
			'ai',
			'library',
			'grid',
			'calendly',
			'dashboard',
			'facebook',
			'flickr',
			'fonts',
			'form',
			'google',
			'instagram',
			'inxmail',
			'jvectormap',
			'linked_in',
			'mailchimp',
			'masks',
			'module',
			'pinterest',
			'satoshipay',
			'sendinblue',
			'seo',
			'shadowbox',
			'soundcloud',
			'stripe',
			'twitter',
			'vimeo',
		);
		
		$this->do_microtime = 0;
		if(($this->isDennis() && $this->io("debug_microtime") == 1) || $this->io("debug_microtime_force") == 1) {
			$this->getMicrotime("individole");
			$this->do_microtime = 1;
		}
		
		//!_VERSION / libraries
		$this->fb_sdk_version 					= '5.5';
		if($this->io("facebook_api_version") != "") {
			$this->fb_version 					= $this->io("facebook_api_version");
		
		} else {
			$this->fb_version 					= '3.1';
		}
		$this->path['facebook']					= $this->path['libraries'].'/_facebook/v'.$this->fb_sdk_version;
		
		$this->ga_offline 						= '<p>Tool currently disabled because of Google API performance issues. Please use <a href="https://analytics.google.com" target="_blank"><b>analytics.google.com</b></a>.';
		
		if($this->isSuperAdmin()) {
			$this->ga_offline .= '<p><a href="/wp-admin/admin.php?page=individole-options#group_group_google_analytics_stats">Enable tool in admin settings</a>';
		}
		
		add_action('wp_ajax_ajaxGetCheckMail', 							array($this, 'ajaxGetCheckMail'));
		add_action('wp_ajax_nopriv_ajaxGetCheckMail', 					array($this, 'ajaxGetCheckMail'));
		
		$this->phpmailer_version 					= '6.9.1';
		$this->owl_version 							= '2.3.4';
		$this->masonry_version 						= '4.2.2';
		$this->mediaelement_version 				= '4.2.16';
		// $this->mediaelement_version 				= '7.0.3';
		$this->jvectormap_version					= '2.0.5';
		$this->jqvmap_version						= '1.5.0';
		$this->jquery_version						= '2.2.4';
		$this->select2_version						= '4.0.13';
		$this->medium_editor_version				= '1';
		$this->jquery_migrate_version				= '3.4.1';//3.3.2';//'3.4.0';
		
		$this->global_image_timestamp 			= get_option("global_image_timestamp");
		
		$this->inline_scripts						= array();
		$this->inline_scripts_header				= array();
		$this->inline_scripts_html					= array();
		$this->inline_scripts_include				= array();
		$this->inline_scripts_include_end		= array();
		$this->inline_scripts_include_end_2		= array();
		$this->inline_scripts_include_header	= array();
		$this->inline_scripts_ready				= array();
		$this->inline_scripts_load					= array();
		$this->wp_footer_content					= array();
		$this->header_css								= array();
		$this->rel_prev_next							= array();
		$this->fonts_selected						= array();
		$this->registered_post_types				= array();
		$this->structured_data 						= array();
		$this->google_maps_ids						= array();
		$this->the_list_columns_css				= array();
		
		//!Instagram connect --> sends request via individole.com to the Instagram connect module
		$this->instagram_client_id					= '328463828392921';
		$this->instagram_client_secret 			= 'a097524a8da0734ffad15624240335f9';
		$this->instagram_redirect_uri 			= 'https://www.individole.com/instagram-token/';
		
		//!Pinterest connect --> sends request via individole.com to the Pinterest connect module
		$this->pinterest_client_id					= '4985541009489280656'; //individole
		$this->pinterest_client_secret 			= 'c7d64d4220b3fca6aab55a336c122814f9c124a45c8bb4bea6149dbf802cf569'; //cremeguides
		$this->pinterest_redirect_uri 			= 'https://www.individole.com/pinterest-token/';
		
		$this->has_pagination						= 0;
		$this->tabindex								= 1;
		$this->acfTab									= 1;
		$this->i_module								= 0;
		$this->i_mask								= 0;
		$this->i_structured_data					= 0;
		$this->has_h1									= 0;
		$this->canonical_extern 					= 0;
		$this->language_toggle						= 3;
		$this->hide_data_title						= 0;
		$this->has_password_protect				= 0;
		$this->has_footer_js							= 0;
		$this->captcha									= 1;
		
		$this->debug_apc_set							= array();
		$this->debug_apc_get							= array();
		$this->posts_used								= array();
		$this->purge_urls 							= array();
		
		// $_SESSION['the_list_columns_css'] = array();
		
		$this->fontawesome_styles = array(
			'brands'	=> 400,
			'solid'		=> 900,
			'regular'	=> 400,
			'light'		=> 300,
			'thin'		=> 100,
			'duotone'	=> 900,
		);
		
		$this->menu_additional_settings 		= array(
			'cpt',
			'media_query',
			'taxonomy',
			'shortcode',
			'anchor',
			'params',
			'ga_label',
			'ga_pageview',
			'subnavi_extra',
		);
		
		$this->queryParametersToRemove = array(
			'_gl',
			'affiliate',
			'debug',
			'fbclid',
			'filter',
			'ga',
			'gad_source',
			'gbraid',
			'gclid',
			'highlight',
			'lang',
			'locale',
			'map',
			'mode',
			'nocache',
			'order',
			'p',
			'post_type',
			'preview',
			'purge_cache',
			'q',
			'ref',
			'search',
			'sessionid',
			'sid',
			'sort',
			'source',
			'ssp_iabi',
			'test',
			'utm_content',
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'view',
			'wbraid',
			
			// 'pagenumber',
			// 'page',
		);
		
		$this->object_positions = array(
			'tl' 					=> '<i class="fas fa-arrow-up-left"></i>',
			't' 					=> '<i class="fas fa-arrow-up"></i>',
			'tr' 					=> '<i class="fas fa-arrow-up-right"></i>',
			'l' 					=> '<i class="fas fa-arrow-left"></i>',
			'center' 			=> '<i class="fas fa-circle-small"></i>',
			'r' 					=> '<i class="fas fa-arrow-right"></i>',
			'bl' 					=> '<i class="fas fa-arrow-down-left"></i>',
			'b' 					=> '<i class="fas fa-arrow-down"></i>',
			'br' 					=> '<i class="fas fa-arrow-down-right"></i>',
		);
		
		$this->icon_desktop					= '<i class="far fa-desktop"></i>';
		$this->icon_tablet_ls				= '<i class="far fa-tablet-alt fa-rotate-90"></i>';
		$this->icon_tablet					= '<i class="far fa-tablet-alt"></i>';
		$this->icon_phone						= '<i class="far fa-mobile"></i>';
		
		$this->icon_desktop_disabled		= '<i class="far fa-desktop disabled"></i>';
		$this->icon_tablet_ls_disabled	= '<i class="far fa-tablet-alt fa-rotate-90 disabled"></i>';
		$this->icon_tablet_disabled		= '<i class="far fa-tablet-alt disabled"></i>';
		$this->icon_phone_disabled			= '<i class="far fa-mobile disabled"></i>';
		
		$this->icon_divider					= '&nbsp;&nbsp;';
		
		$this->object_visibilities = array(
			'default'			=> $this->icon_desktop.$this->icon_divider.$this->icon_tablet_ls.$this->icon_divider.$this->icon_tablet.$this->icon_divider.$this->icon_phone,
			'hide_tablet'		=> $this->icon_desktop.$this->icon_divider.$this->icon_tablet_ls_disabled.$this->icon_divider.$this->icon_tablet_disabled.$this->icon_divider.$this->icon_phone_disabled,
			'hide_desktop show_tablet hide_tablet_mini'		=> $this->icon_desktop_disabled.$this->icon_divider.$this->icon_tablet_ls.$this->icon_divider.$this->icon_tablet_disabled.$this->icon_divider.$this->icon_phone_disabled,
			'hide_desktop show_tablet_mini hide_phone3'		=> $this->icon_desktop_disabled.$this->icon_divider.$this->icon_tablet_ls_disabled.$this->icon_divider.$this->icon_tablet.$this->icon_divider.$this->icon_phone_disabled,
			'hide_desktop show_phone3'						=> $this->icon_desktop_disabled.$this->icon_divider.$this->icon_tablet_ls_disabled.$this->icon_divider.$this->icon_tablet_disabled.$this->icon_divider.$this->icon_phone,
			'hide_tablet_mini'								=> $this->icon_desktop.$this->icon_divider.$this->icon_tablet_ls.$this->icon_divider.$this->icon_tablet_disabled.$this->icon_divider.$this->icon_phone_disabled,
			'hide_phone3'									=> $this->icon_desktop.$this->icon_divider.$this->icon_tablet_ls.$this->icon_divider.$this->icon_tablet.$this->icon_divider.$this->icon_phone_disabled,
			'hide_desktop show_tablet'						=> $this->icon_desktop_disabled.$this->icon_divider.$this->icon_tablet_ls.$this->icon_divider.$this->icon_tablet.$this->icon_divider.$this->icon_phone,
			'hide_desktop show_tablet_mini'					=> $this->icon_desktop_disabled.$this->icon_divider.$this->icon_tablet_ls_disabled.$this->icon_divider.$this->icon_tablet.$this->icon_divider.$this->icon_phone,
		);
		
		$this->tab_settings = array(
			'label'				=> '<i class="fas fa-cog"></i>',
			'type'				=> 'tab',
			'name'				=> '__config_base',
			'_name'				=> '__config_base',
			'key'					=> '__config_base',
			//'placement'			=> 'left aligned',
		);
		
		if(!defined("GUTENBERG")) {
			add_filter('use_block_editor_for_post', '__return_false');
		}
		
		spl_autoload_register(array($this, "autoloadClasses"));
		
		if(!headers_sent()) {
			ini_set('zlib.output_compression', '0');
		}
		
		if($this->isAdmin() && is_admin()) {
			//SET timestamp of user interaction to show currently logged users on dashboard & wordpress update page
			$this->setGoogleAnalyticsProperty();
		}
		
		$this->snippets											= $this->getSnippets();
		$this->individole_flexible_modules_settings		= $this->unserialize(get_option("individole_flexible_modules"));
		
		if(!is_array($this->individole_flexible_modules_settings)) {
			$this->individole_flexible_modules_settings = array();
		}
		
		//!ADD project specific modules
		if($this->isAdmin()) {
			$this->modules_folders = array(
				get_stylesheet_directory().'/'.$this->acf.'/_flexible_modules/',
				get_stylesheet_directory().'/_acf_defaults/_flexible_modules/',
			);
			
			foreach($this->modules_folders AS $modules_folder) {
				foreach(glob($modules_folder.'*.php') AS $file) {
					$module_name = basename($file, '.php');
					$this->individole_flexible_modules_settings[$module_name] = 1;
				}
			}
		}
		
		$this->individole_flexible_modules_settings_trigger 	= $this->unserialize(get_option("individole_flexible_modules_trigger"));
		
		$this->variables('individole_configs');
		$this->choices_forms_autofill 		= $this->variables('choices_forms_autofill');
		$this->country_codes_eu 				= $this->variables('country_codes_eu');
		$this->shopping_feed_attributes 		= $this->variables('shopping_feed_attributes');
		$this->job_posting_employment_types = $this->variables('job_posting_employment_types');
		// $this->debug($this->google_fonts);
		
		if($this->isSuperAdmin()) {
			add_action('init',									array($this, 'setSessionSuperAdmin'));
			add_action('admin_init',							array($this, 'upgradeWPRocket'));
			add_action('admin_notices', 						array($this, 'modifyUpdatePage'), 1);
			add_action('core_options-discussion_preamble', 	array($this, 'modifyUpdatePage'), 1);
		}
		
		if($this->isDennis()) {
			add_action('admin_head', array($this, 'modifyAdminBackWPUpFTPPage'));
		}
		
		remove_filter('template_redirect','redirect_canonical');
		// remove_filter('wp_is_php_version_acceptable', true);
		//add_filter('editable_slug', 							array($this, 'prefix_wp_unique_post_slug', 2, 2 ));
		
		add_filter('rest_endpoints', 	array($this, 'disableRestEndpoints'));
		add_filter('rocket_lrc_optimization', 				'__return_false' , 999);
		add_filter('posts_fields', 								array($this, 'searchSelect'));
		add_filter('posts_join', 								array($this, 'searchJoin'));
		add_filter('posts_where', 								array($this, 'searchWhere'));
		add_filter('posts_groupby', 							array($this, 'searchGroupBy'));
		add_filter('posts_distinct', 							array($this, 'searchDistinct'));
		add_filter('disable_captions', 							array($this, 'disableCaptionInsert'));
		add_filter('clean_url', 								array($this, 'enqueueScriptsAsync'), 11, 1 );
		
		//add_action('current_screen', 							array($this, 'screen'), 10, 1);
		add_action('admin_init', 								array($this, 'is_plugin_active'));
		add_action('set_object_terms', 							array($this, 'termsOrderSet'), 10, 6);
		add_filter('get_the_terms', 							array($this, 'termsOrderGet'), 10, 3);
		add_filter('terms_to_edit', 							array($this, 'termsOrderEdit'), 10, 2);
		
		add_filter('upload_dir', 								array($this, 'modifyUploadDir'));
		add_action('load-upload.php', 							array($this, 'modifyAdminMedia'));
		
		add_action('delete_expired_transients', function($args) {
			error_log(print_r($args, true));
		});
		
		add_action('delete_expired_posts', function($args) {
			error_log(print_r($args, true));
		});
		
		add_action('wp_footer', function() {
			if(session_id()) {
				session_write_close();
			}
		});
		
		add_action('admin_footer', function() {
			if(session_id()) {
				session_write_close();
			}
		});
		
		$this->addShortcodes();
		
		$wp_upload_dir = wp_upload_dir();
		
		$upload_path = get_option("upload_path");
		if($upload_path == "") {
			$upload_path = $wp_upload_dir['basedir'];
		}
		$this->path['base_images']								= $upload_path;
		
		$upload_url_path = get_option("upload_url_path");
		
		if($upload_url_path == "") {
			if(defined("WP_SITEURL")) {
				$upload_url_path = WP_SITEURL.'/wp-content/uploads';
				
			} else if($this->io("upload_url_path") != "") {
				$upload_url_path = $this->io("upload_url_path");
			
			} else {
				$upload_url_path = $wp_upload_dir['baseurl'];
			}
		}
		$this->path['abs_images']								= $upload_url_path;
		
		$this->path['all_tools'] = array(
			'extra'		=> $this->path['tools_extra'],
			'default'	=> $this->path['tools'],
		);
		
		//!DEFINE urls
		$this->url['base']									= get_stylesheet_directory_uri();
		$this->url['fonts']									= $this->url['base'] . '/_fonts';
		$this->url['gfx']										= $this->url['individole'] . '/_images';
		$this->url['images']									= $this->url['base'] . '/_images';
		$this->url['libraries']								= $this->url['individole'] . '/_libraries';
		$this->url['tools_extra']							= $this->url['base'] . '/_tools/_individole';
		
		//!DEFINE variables
		$this->col_w											= $this->io("col_w");
		$this->acf_pm_init										= 0;
		$this->col_gap											= $this->io("col_gap");
		
		$this->cpt												= array();
		add_action('init',									array($this, 'getConfigCPT'));
		
		$this->post_active									= $this->getActivePosts();
		$this->page_hierarchy_active						= array();
		$this->page_hierarchy_selected					= 0;
		$this->tools											= array();
		$this->favicon											= 'favicon';
		$this->i_frontend_edit								= 0;
		$this->i_frontend_edit_pe							= 0;
		$this->i_columns										= 0;
		$this->satoshipay_script							= 0;
		$this->debug_current									= 0;
		$this->debug_final									= array();
		$this->doing_meta_description 					= 0;
		$this->body_classes									= array();
		$this->translations									= array();
		$this->translations_slug							= '';
		$this->acfPM 											= 0;
		$this->stripe											= array();
		$this->masonry											= 1;
		$this->menu_level										= 1;
		$this->rowgrid											= 1;
		$this->slider											= 0;
		$this->uislider										= 0;
		$this->plusminusnumber								= 0;
		$this->jvector_map									= 0;
		$this->h1												= 0;
		$this->duration_picker								= 0;
		$this->month_picker									= 0;
		$this->modelviewer									= 0;
		$this->owl												= 0;
		$this->vector_map										= 0;
		$this->google_map										= 0;
		$this->google_map_is_included 					= 0;
		$this->accordion										= 0;
		$this->textcolumns									= 0;
		$this->form												= 0;
		$this->form_css										= 0;
		$this->newsletter_form								= 0;
		$this->ecwid_buy_button								= 0;
		$this->ecwid_is_included 							= 0;
		$this->mediaelement									= 0;
		$this->modules_pdf_embed							= 0;
		$this->video											= 1;
		$this->video_youtube									= 0;
		$this->video_vimeo									= 0;
		$this->chartjs											= 0;
		$this->hide_email										= 1;
		$this->admin_h1_actions								= array();
		$this->seo_image_ids 								= array();
		$this->frontend_options								= array();
		$this->individole_tools								= array();
		$this->individole_tool								= array();
		$this->individole_tools_active					= array();
		$this->acf_options									= array();
		$this->acf_special_options							= array();
		$this->acf_options_raw								= array();
		$this->acf_options_settings						= array();
		$this->acfPMColumns									= array();
		$this->file_versions									= array();
		$this->placeholder									= urlencode('__Placeholder__');
		$this->shortcode_instructions 					= array();
		$this->menu_active									= array();
		$this->acfGetRowKeys									= array();
		$this->new_minified_file 							= '';
		$this->admin_css										= array();
		$this->account											= array();
		$this->borlabs											= array();
		$this->borlabs_version								= 0;
		$this->cookiebot_version							= 0;
		$this->tcpdi_pdf_background_path					= '';
		$this->tcpdi_pdf_background_path2				= '';
		
		$this->w_tablet_ls 									= 1194;
		$this->w_tablet										= 834;
		$this->w_phone 										= 576;
		$this->w_phone_2 										= 414;
		
		$this->shopping_feed_max_length_title			= 80;
		$this->shopping_feed_max_length_description	= 1500;
		
		$this->minify_X										= "\x1A";
		$this->minify_SS										= '"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'';
		$this->minify_CC										= '\/\*[\s\S]*?\*\/';
		$this->minify_CH										= '<\!--[\s\S]*?-->';
		
		$this->seo_title_max_length						= 70;
		$this->seo_description_max_length				= 160;
		$this->seo_var											= 'seo_0_title--seo_0_description';
		$this->seo_value										= array(
			'head'			=> 'SEO',
			'titles'		=> 'Title,Text',
			'formats'		=> 'text,seo_text',
			'width'			=> 160,
		);
		
		$this->save_input_wrap = '
			<i class="fas fa-save init"></i>
			<i class="fas fa-spinner fa-spin saving"></i>
			<i class="fas fa-check done"></i>
		';
		
		(!defined("ENCRYPT_KEY")) 								? define("ENCRYPT_KEY", 'klkflds788767sdftb-_dfdsfjdhsiu§$34§wefdfds') : "";
		
		$this->dhl_products 		= array();
		$this->carriers 			= $this->variables('carriers');
		
		$this->normalizeChars 	= $this->variables('normalizeChars');
		$this->umlaut_chars 		= $this->variables('umlaut_chars', array('and' => $this->translateAND()));
		$this->regex_sonderzeichen 	= $this->variables('regex_sonderzeichen');
		$this->individole_admin_menu_positions = $this->variables('individole_admin_menu_positions');
		
		$this->l_force = '';
		
		if($this->isWPML()) {
			global $sitepress;
			
			$this->l_default = $this->wpmlGetDefaultLanguage();
			
			if(isset($_POST['l'])) {
				$this->l = $_POST['l'];
			
			} else {
				$this->l = $this->wpmlGetCurrentLanguage(array());
			}
			
			if(is_admin()) {
				if($this->io("wpml_disable_translators_all") == 0) {
					add_action('wpml_override_is_translator', array($this, 'wpmlOverrideIsTranslator'), 10, 3);
				}
			}
			
			if(class_exists('TranslationManagement')) {
				global $iclTranslationManagement;
				
				$iclTranslationManagement->settings['custom_fields_translation'] = array();
			}
			
			add_action('icl_make_duplicate', array($this, 'wpmlUpdatePostAfterDuplicate'), 10, 4);
			
			$this->sitepress = $sitepress;
			
			$this->languages = apply_filters('wpml_active_languages', NULL, 'skip_missing=0&orderby=menu_order&order=asc&link_empty_to=str');
			// $this->debug($this->languages);
			
			$hidden_languages = $sitepress->get_setting( 'hidden_languages' );
			
			if($this->isAdmin()) {
				if(!empty($hidden_languages)) {
					foreach($this->languages AS $l_code => $l_values) {
						if(in_array($l_code, $hidden_languages)) {
							$this->languages[$l_code]['hidden'] = 1;
						}
					}
				}
				
			} else {
				if(!empty($hidden_languages)) {
					foreach($this->languages AS $l_code => $l_values) {
						if(in_array($l_code, $hidden_languages)) {
							unset($this->languages[$l_code]);
						}
					}
				}
			}
						
			$home_id = get_option('page_on_front');
			
			$trid = $sitepress->get_element_trid($home_id);
			$home_translations = $sitepress->get_element_translations($trid);
			
			foreach($home_translations AS $home_translation) {
				if(isset($this->languages[$home_translation->language_code])) {
					$this->languages[$home_translation->language_code]['home_id'] = $home_translation->element_id;
				}
			}
			
			$this->languages 		= $this->wpmlGetAllLanguages();
			
			if(!isset($_SESSION['icl_language'])) {
				$_SESSION['icl_language'] = $this->l;
			}
			
		} else {
			$this->l = substr( get_bloginfo ( 'language' ), 0, 2 );
		}
		
		$this->setLocales();
		
		add_filter( 'duplicate_post_excludelist_filter', array($this, 'dpExcludeDuplicatePM'));
		
		add_action('createSitemapXMLInit', 							array($this, 'createSitemapXML'));
		add_action('createWordIndexInit', 							array($this, 'createWordIndex'));
		add_action('createSendMail404StatsInit', 					array($this, 'createSendMail404Stats'));
		add_action('createRSSInit', 									array($this, 'createRSS'));
		add_action('createPublishMissingPostsInit', 				array($this, 'publishMissingPosts'));
		add_action('createShoppingFeedsInit', 						array($this, 'createShoppingFeeds'));
		add_action('createDailyCachePurgeInit', 					array($this, 'purgeAll'));
		
		if($this->isAdmin()) {
			add_action('acf/save_post', 							array($this, 'acfCreateMissingRepeaterFieldsOption'), 20, 1);
			add_action('acf/save_post', 							array($this, 'acfCleanupUnusedPM'), 1);
			add_action('acf/save_post', 							array($this, 'deleteAPCDataACFOptions'), 20, 1);
			add_action('acf/save_post', 							array($this, 'createDHLShippingRates'), 10, 0);
			
		} else {
			if(is_404()) {
				add_action('init',									array($this, 'save404'), 99);
			}
		}
		
		add_action('save_post',										array($this, 'acfCreateMissingRepeaterFields'), 1, 2);
		add_action('save_post',										array($this, 'savePost'), 10, 2);
		add_action('save_post',										array($this, 'createOffline'), 99, 1);
		add_action('save_post',										array($this, 'purgePages'), 99, 1);
		
		add_action('acf/include_field_types', 					array($this, 'acfIncludeFieldTypes'));
		add_filter('acf/load_value', 								array($this, 'acfFixGetFieldFunction'), 20, 3);
		add_filter('acf/settings/enable_post_types', 		'__return_false');
		add_filter('acf/field_group/enable_field_browser', '__return_false');
		add_filter('acf/field_group/disable_field_settings_tabs', '__return_true');
		
		//!INIT fronend/backend tools for ALL
		add_filter('get_user_option_admin_color', 				array($this, 'forceAdminColor'));
		add_filter('auto_update_core', 							'__return_false');
		add_filter('pre_option_link_manager_enabled', 							'__return_false');
		add_filter('big_image_size_threshold', 					'__return_false');
		add_filter('max_srcset_image_width', 					array($this, 'disableAutoResponsive'));
		add_filter('wp_headers',								array($this, 'removeHeaders'));
		add_filter('heartbeat_settings',						array($this, 'modifyHeartbeat'), 1);
		//add_action('init', 										array($this, 'disableHeartbeat'), 1);
		add_action('init',										array($this, 'includeAllFunctions'));
		add_action('init',										array($this, 'registerNavMenus'));
		add_action('init',										array($this, 'getCurrentUser'));
		add_action('init',										array($this, 'addImageSizes'), 1);
		add_action('init',										array($this, 'removeWPHead'));
		add_action('init',										array($this, 'createCustomPostTypes'), 12, 2);
		add_action('admin_footer',								array($this, 'createCustomColumnCSS'), 13);
		add_filter('init',										array($this, 'removeOembedProviders') , 10, 1);
		
		add_filter('wp_video_shortcode_library',				array($this, 'removeMediaElement'), 100);
		add_action('init',										array($this, 'registerStyles'), 5);
		
		add_filter('acf/load_value/type=text', 					array($this, 'acfFormatValue'), 10, 3);
		add_filter('acf/load_value/type=textarea', 				array($this, 'acfFormatValue'), 10, 3);
		add_filter('acf/load_value/type=wysiwyg', 				array($this, 'acfFormatValue'), 10, 3);
		// add_filter('acf/load_value/type=repeater', 				array($this, 'acfFormatValue'), 10, 3);
		
		add_action("wp_enqueue_scripts",						array($this, 'enqueue_media_uploader'));
		add_filter('the_content',								'wpautop', 10);
		add_filter('acf_the_content',							'wpautop', 10);
		remove_filter('the_content',							array($GLOBALS['wp_embed'], 'autoembed'), 8);
		add_theme_support('html5',								array('search-form', 'date' ));
		add_theme_support('post-thumbnails');
		add_filter('jpeg_quality',								array($this, 'setJPEGQuality'));
		add_filter('wp_editor_set_quality',						array($this, 'setJPEGQuality'));
		
		add_filter('wfu_before_upload', 						array($this, 'wfuUploaderStart'), 10, 2);
		add_filter('wfu_after_file_upload', 					array($this, 'wfuUploaderFileUpLoaded'), 10, 2);
		add_filter('wfu_after_upload', 							array($this, 'wfuUploaderDone'), 10, 2);
		
		add_action('wp_ajax_ajaxSetNonce', 					array($this, 'ajaxSetNonce'));
		add_action('wp_ajax_nopriv_ajaxSetNonce', 		array($this, 'ajaxSetNonce'));
		
		add_action('wp_ajax_printURL', 							array($this, 'printURL'));
		add_action('wp_ajax_nopriv_printURL', 					array($this, 'printURL'));
		
		add_action('wp_ajax_ajaxGetWordIndex', 					array($this, 'ajaxGetWordIndex'));
		add_action('wp_ajax_nopriv_ajaxGetWordIndex', 			array($this, 'ajaxGetWordIndex'));
		
		add_action('wp_ajax_ajaxGetStats', 						array($this, 'ajaxGetStats'));
		add_action('wp_ajax_nopriv_ajaxGetStats', 			array($this, 'ajaxGetStats'));
		
		add_action('wp_ajax_ajaxGetAIAPIRequest', 			array($this, 'ajaxGetAIAPIRequest'));
		
		add_action('wp_ajax_ajaxGetDeeplTranslation', 			array($this, 'ajaxGetDeeplTranslation'));
		add_action('wp_ajax_ajaxGetDeeplTranslationButton', 	array($this, 'ajaxGetDeeplTranslationButton'));
		
		add_action('wp_ajax_ajaxRemoveServiceWorker', 			array($this, 'removeServiceWorker'));
		add_action('wp_ajax_nopriv_ajaxRemoveServiceWorker', 	array($this, 'removeServiceWorker'));
		
		//!INIT default shortcodes
		add_action('wp_ajax_ajaxFacebookUser', 					array($this, 'ajaxFacebookUser'));
		add_action('wp_ajax_nopriv_ajaxFacebookUser', 			array($this, 'ajaxFacebookUser'));
		
		add_action('wp_ajax_ajaxCreateAccount', 				array($this, 'ajaxCreateAccount'));
		add_action('wp_ajax_nopriv_ajaxCreateAccount', 			array($this, 'ajaxCreateAccount'));
		
		add_action('wp_ajax_ajaxCreatePNGFromBase64', 			array($this, 'ajaxCreatePNGFromBase64'));
		add_action('wp_ajax_nopriv_ajaxCreatePNGFromBase64', 	array($this, 'ajaxCreatePNGFromBase64'));
		
		add_action('wp_ajax_ajaxGetMetaGoogle', 				array($this, 'ajaxGetMetaGoogle'));
		add_action('wp_ajax_nopriv_ajaxGetMetaGoogle', 			array($this, 'ajaxGetMetaGoogle'));
		
		add_action('wp_ajax_ajaxLogoutAccount', 				array($this, 'ajaxLogoutAccount'));
		add_action('wp_ajax_nopriv_ajaxLogoutAccount', 			array($this, 'ajaxLogoutAccount'));
		
		add_action('wp_ajax_ajaxGetLocalTime', 					array($this, 'ajaxGetLocalTime'));
		add_action('wp_ajax_nopriv_ajaxGetLocalTime', 			array($this, 'ajaxGetLocalTime'));
		
		add_action('wp_ajax_createShippingLabel', 				array($this, 'createShippingLabel'));
		add_action('wp_ajax_deleteShippingLabel', 				array($this, 'deleteShippingLabel'));
		add_action('wp_ajax_saveShippingLabel', 				array($this, 'saveShippingLabel'));
		
		add_action('wp_ajax_ecwidCreateDHLLabel', 				array($this, 'ecwidCreateDHLLabel'));
		// add_action('wp_ajax_nopriv_ecwidCreateDHLLabel', 		array($this, 'ecwidCreateDHLLabel'));
		
		add_action('wp_ajax_ajaxGetGoogleTranslate', 			array($this, 'ajaxGetGoogleTranslate'));
		add_action('wp_ajax_nopriv_ajaxGetGoogleTranslate', 	array($this, 'ajaxGetGoogleTranslate'));
		
		add_action('wp_ajax_ajaxGetACFSingleValue', 			array($this, 'ajaxGetACFSingleValue'));
		add_action('wp_ajax_nopriv_ajaxGetACFSingleValue', 		array($this, 'ajaxGetACFSingleValue'));
		
		add_action('wp_ajax_ajaxURLToPostID', 					array($this, 'ajaxURLToPostID'));
		add_action('wp_ajax_nopriv_ajaxURLToPostID', 			array($this, 'ajaxURLToPostID'));
		
		add_action('wp_ajax_ajaxSaveACFSingleValue', 			array($this, 'ajaxSaveACFSingleValue'));
		add_action('wp_ajax_nopriv_ajaxSaveACFSingleValue', 	array($this, 'ajaxSaveACFSingleValue'));
		
		add_action('wp_ajax_ajaxIndividoleSendForm', 			array($this, 'ajaxIndividoleSendForm'));
		add_action('wp_ajax_nopriv_ajaxIndividoleSendForm', 	array($this, 'ajaxIndividoleSendForm'));
		
		add_action('wp_ajax_ajaxMailchimpSendForm', 			array($this, 'ajaxMailchimpSendForm'));
		add_action('wp_ajax_nopriv_ajaxMailchimpSendForm', 		array($this, 'ajaxMailchimpSendForm'));
		
		add_action('wp_ajax_ajaxSendInBlueSendForm', 				array($this, 'ajaxSendInBlueSendForm'));
		add_action('wp_ajax_nopriv_ajaxSendInBlueSendForm', 		array($this, 'ajaxSendInBlueSendForm'));
		
		//!INIT backend functions for SUPERADMIN
		add_action('wp_ajax_ajaxCountReadPost', 						array($this, 'ajaxCountReadPost'));
		add_action('wp_ajax_nopriv_ajaxCountReadPost', 					array($this, 'ajaxCountReadPost'));
		
		add_action('wp_ajax_ajaxSetCookie', 							array($this, 'ajaxSetCookie'));
		add_action('wp_ajax_nopriv_ajaxSetCookie', 						array($this, 'ajaxSetCookie'));
		
		add_action('wp_ajax_ajaxSetSession', 							array($this, 'ajaxSetSession'));
		add_action('wp_ajax_nopriv_ajaxSetSession', 					array($this, 'ajaxSetSession'));
		
		add_action('wp_ajax_ajaxGetAdminSearchresults', 				array($this, 'ajaxGetAdminSearchresults'));
		add_action('wp_ajax_nopriv_ajaxGetAdminSearchresults', 			array($this, 'ajaxGetAdminSearchresults'));
		
		add_action('wp_ajax_ajaxGetSearchSuggestions', 					array($this, 'ajaxGetSearchSuggestions'));
		add_action('wp_ajax_nopriv_ajaxGetSearchSuggestions', 			array($this, 'ajaxGetSearchSuggestions'));
		
		add_action('wp_ajax_ajaxSaveSessionValue',						array($this, 'ajaxSaveSessionValue'));
		add_action('wp_ajax_nopriv_ajaxSaveSessionValue',				array($this, 'ajaxSaveSessionValue'));
		
		add_action('wp_ajax_ajaxGetPermalink', 							array($this, 'ajaxGetPermalink'));
		add_action('wp_ajax_nopriv_ajaxGetPermalink', 					array($this, 'ajaxGetPermalink'));
		
		add_action('wp_ajax_wpmlCreateDuplicate', 						array($this, 'wpmlCreateDuplicate'));
		add_action('wp_ajax_nopriv_wpmlCreateDuplicate', 				array($this, 'wpmlCreateDuplicate'));
		
		add_action('wp_login', 											array($this, 'wpmlAddLanguagePairsAfterLogin'), 10, 2);
		
		add_filter('private_title_format', function ($title) { return "%s"; });
		
		if(!is_admin() && $GLOBALS['pagenow'] != 'wp-login.php') {
			add_action('wp_print_scripts', 								array($this, 'removeScripts'), 100);
			add_action('wp_print_styles',	 							array($this, 'removeStyles'), 100);
			add_action('wp_enqueue_scripts',							array($this, 'enqueueScripts'));
		}
		
		if(!is_admin()) {
			add_action( 'init', 										array($this, 'disableEmbedsCodeInit'), 9999 );
		}
		
		if(is_admin() && $this->isDennis()) {
			add_action('admin_notices', 								array($this, 'createSavePostMessages'));
		}
		
		add_filter('cron_schedules',									array($this, 'cronAddSchedules'));
		add_filter('action_scheduler_run_schedule', 				function($arg) { return 86400; });
		add_filter('action_scheduler_run_queue', 					function($arg) { return 86400; });
		add_action('init',												array($this, 'cronInit'));
		
		$this->wprocket_excluded = array();
		if(function_exists("rocket_clean_domain")) {
			$wprocket_excluded = get_rocket_option( 'cache_reject_uri' );
			
			if(is_array($wprocket_excluded)) {
				$this->wprocket_excluded = array_flip($wprocket_excluded);
			}
		}
		
		if($this->isAdmin() && is_admin()) {
			add_action('init',											array($this, 'setBackendToolsSettings'), 99);
			// add_filter('http_request_args', 							array($this, 'disableHTTPAPICalls'), 99, 2);
			add_action('http_api_curl', 								array($this, 'disableHTTPAPICalls'), 10, 3);
			add_action('pmxi_before_xml_import', 					array($this, 'importBeforeImport'), 10, 1);
			add_action('pmxi_after_xml_import', 					array($this, 'importAfterImport'), 10, 1);
			
			$this->acf_field_IDs 	= $this->getACFFieldIDs();
			$this->acf_pm_columns 	= $this->getACFPMColumns();
			
			add_filter( 'acf/fields/wysiwyg/toolbars' , 			array($this, "acfWYSIWYGToolbars"));
			
			add_filter("upload_dir", 									array($this, "setPostIDUploadDirectory"));
			add_filter("upload_dir", 									array($this, "setProtectedCPTUploadDirectory"));
			
			add_action('admin_head',						array($this, 'modifyAdminListPagesHeader'), 99);
			// add_action('admin_head-admin.php',						array($this, 'modifyAdminListPagesHeader'), 99);
			add_action('add_meta_boxes',								array($this, 'metaboxes'), 99);
			
			if($this->io("special_settings") == 1) {
				$acf_options_page = acf_add_options_page(array(
					'page_title' 	=> 'Special settings',
					'menu_title'	=> 'Special settings',
					'menu_slug' 	=> 'acf-options-individole_special_settings',
					'capability'	=> 'edit_posts',
					'redirect'		=> true,
				));
				
				if($this->isWPML() && $this->io("special_settings_redirect_default_language") == 1 && is_admin() && isset($_GET['page']) && $_GET['page'] == "acf-options-individole_special_settings") {
					global $sitepress;
					
					$default_language = $sitepress->get_default_language();
					$current_language = $this->l;
					
					if($default_language != $current_language) {
						$redirect_url = $_SERVER['REQUEST_URI'].'&lang='.$default_language;
						print('<script>window.location.href="'.$redirect_url.'"</script>');
					}
				}
			}
			
			global $individole_acf_options_pages;
			if($individole_acf_options_pages) {
				foreach($individole_acf_options_pages AS $k => $v) {
					acf_add_options_sub_page(array(
						'page_title' 	=> 'Special settings / '.$v,
						'menu_title'	=> '&#8627; '.$v,
						'menu_slug' 	=> 'acf-options-individole_special_settings_'.$k,
						'capability'	=> 'edit_posts',
						'redirect'		=> true,
						'parent_slug'	=> $acf_options_page['menu_slug'],
					));
				}
			}
			
			add_action('acf/init', 										array($this, 'setACFSettings'));
			add_filter('acf/location/rule_types', 						array($this, 'acfLocationRules_page_level'));
			add_filter('acf/location/rule_operators', 					array($this, 'acfLocationRules_operators'));
			add_filter('acf/location/rule_values/page_level', 			array($this, 'acfLocationRules_values_page_level'));
			add_filter('acf/location/rule_match/page_level', 			array($this, 'acfLocationRules_match_page_level'), 10, 3);
			add_filter('acf/prepare_field/type=post_object', 			array($this, 'acfFieldPostObjectPrepare'));
			add_filter('acf/fields/post_object/query', 					array($this, 'acfFieldPostObjectQuery'), 10, 3);
			add_action('acf/render_field/type=post_object', 			array($this, 'acfFieldPostObjectRender'), 10, 1 );
			add_action('acf/input/admin_footer', 						array($this, 'addWPEditorFieldClass'));
			
			add_action('wp_dashboard_setup',							array($this, 'adminDashboardSetup'));
			
			add_action('pmxi_after_xml_import', 						array($this, 'wpAllImportDeleteAPCData'));
						
			add_action('admin_init',									array($this, 'deregisterAdminScripts'));
			add_action('admin_init',									array($this, 'setWPRocketNeverCache'));
			add_action('admin_init',									array($this, 'setUploadPath'),20);
			
			add_action('admin_bar_menu',								array($this, 'modifyAdminBar'), 999999);
			
			add_action('admin_menu', 									array($this, 'addAdminIndividoleMenu'));
			add_action('admin_menu',									array($this, 'setBackendToolsMenu'), 10, 1);
			add_action('admin_menu',									array($this, 'removeIndividoleInitSubmenu'), 10, 1);
			
			add_action('admin_head',									array($this, 'removeMetaBoxesPost'), 99, 1);
			add_action('admin_head',									array($this, 'setCustomAdminCSS'));
			add_action('admin_head',									array($this, 'setCustomAdminJavascript'));
			
			add_action('admin_footer',									array($this, 'setCustomAdminCSS'));
			
			add_action('wp_before_admin_bar_render', 					array($this, 'addAdminBarTheme'), 1000);
			add_action('in_admin_footer',								array($this, 'addLibraryFilenamesCB'), 10, 0);
			
			add_action('manage_nav-menus_columns',						array($this, 'removeMetaBoxesMenus'), 99, 1);
			add_filter('upload_mimes',									array($this, 'addMimeTypes'));
			add_action('post_submitbox_minor_actions',					array($this, 'modifySubmitboxMinorActions'), 10, 1);
			add_action('post_submitbox_misc_actions',					array($this, 'modifySubmitboxMiscActions'), 10, 1);
			add_action('wp_before_admin_bar_render',					array($this, 'modifyAdminBarBefore'));
			add_filter('mce_css',										array($this, 'addWPEditorStylesheets'));
			add_filter('page_attributes_dropdown_pages_args',			array($this, 'enableDraftsParents'));
			add_filter('quick_edit_dropdown_pages_args',				array($this, 'enableDraftsParents'));
			
			add_filter('post_mime_types',								array($this, 'modifyMimeTypes'));
			add_filter('manage_media_columns',							array($this, 'setLibraryColumns'));
			add_action('manage_media_custom_column',					array($this, 'setLibraryColumnsValue'), 10, 2);
			add_filter('attachment_fields_to_edit',						array($this, 'addLibraryFields'), 999, 2);
			add_filter('attachment_fields_to_save',						array($this, 'addLibraryFieldsSave'), 10, 2);
			add_action('admin_enqueue_scripts',							array($this, 'enqueueAdminScripts'));
			add_filter('wp_image_editors',								array($this, 'forceGDLib'));
			add_filter('wp_prepare_attachment_for_js', 					array($this, 'showSVGLibraryPreview'), 10, 3);
			
			add_filter('mce_external_plugins', 							array($this, 'setTinyMCEExternalPlugins'));
			
			add_filter('tiny_mce_before_init',							array($this, 'setTinyMCEFormats'), 10, 2);
			
			if(function_exists("rocket_clean_domain")) {
				add_action('before_rocket_clean_domain',				array($this, 'deleteAPCDataAll'), 10, 1);
			}
		}
		
		//!INIT frontend/backend functions for ADMIN
		if($this->isAdmin()) {
			add_filter('wp_check_filetype_and_ext', 								array($this, "disableRealMimeCheck"), 10, 4 );
			
			add_action('wp_ajax_ajaxDelete404',										array($this, 'ajaxDelete404'));
			
			add_action('wp_ajax_ajaxToggleHideDrafts',								array($this, 'ajaxToggleHideDrafts'));
			add_action('wp_ajax_nopriv_ajaxToggleHideDrafts',						array($this, 'ajaxToggleHideDrafts'));
			
			add_action('wp_ajax_ajaxToggleGrid',									array($this, 'ajaxToggleGrid'));
			add_action('wp_ajax_nopriv_ajaxToggleGrid',								array($this, 'ajaxToggleGrid'));
			
			add_action('wp_ajax_ajaxToggleGAEvents',								array($this, 'ajaxToggleGAEvents'));
			add_action('wp_ajax_nopriv_ajaxToggleGAEvents',							array($this, 'ajaxToggleGAEvents'));
			
			add_action('wp_ajax_ajaxToggleEdit',									array($this, 'ajaxToggleEdit'));
			add_action('wp_ajax_nopriv_ajaxToggleEdit',								array($this, 'ajaxToggleEdit'));
			
			add_action('wp_ajax_ajaxCreateInstagramTestwall',						array($this, 'ajaxCreateInstagramTestwall'));
			add_action('wp_ajax_nopriv_ajaxCreateInstagramTestwall',				array($this, 'ajaxCreateInstagramTestwall'));
						
			add_action('wp_ajax_ajaxSaveMediathekData', 							array($this, 'ajaxSaveMediathekData'));
			add_action('wp_ajax_nopriv_ajaxSaveMediathekData', 						array($this, 'ajaxSaveMediathekData'));
			
			add_action('wp_ajax_ajaxSaveAttachmentFilters', 						array($this, 'ajaxSaveAttachmentFilters'));
			add_action('wp_ajax_nopriv_ajaxSaveAttachmentFilters', 					array($this, 'ajaxSaveAttachmentFilters'));
			
			add_action('wp_ajax_ajaxSaveToolFormsFilters', 							array($this, 'ajaxSaveToolFormsFilters'));
			
			add_action('wp_ajax_ajaxSaveAdminMetaboxDuplicatePM', 					array($this, 'ajaxSaveAdminMetaboxDuplicatePM'));
			add_action('wp_ajax_nopriv_ajaxSaveAdminMetaboxDuplicatePM', 			array($this, 'ajaxSaveAdminMetaboxDuplicatePM'));
			
			add_action('wp_ajax_ajaxSaveAdminValue', 								array($this, 'ajaxSaveAdminValue'));
			add_action('wp_ajax_nopriv_ajaxSaveAdminValue', 						array($this, 'ajaxSaveAdminValue'));
			
			add_action('wp_ajax_ajaxCreateDataACFPM', 								array($this, 'ajaxCreateDataACFPM'));
			add_action('wp_ajax_nopriv_ajaxCreateDataACFPM', 						array($this, 'ajaxCreateDataACFPM'));
			
			add_action('wp_ajax_createMainCSS', 									array($this, 'createMainCSS'));
			add_action('wp_ajax_nopriv_createMainCSS', 								array($this, 'createMainCSS'));
			
			add_action('wp_ajax_createFooterJS', 									array($this, 'createFooterJS'));
			add_action('wp_ajax_nopriv_createFooterJS', 							array($this, 'createFooterJS'));
			
			add_action('wp_ajax_ajaxCreatePosts', 									array($this, 'ajaxCreatePosts'));
			add_action('wp_ajax_nopriv_ajaxCreatePosts', 							array($this, 'ajaxCreatePosts'));
			
			add_action('wp_ajax_ajaxPostSocialTwitter',								array($this, 'ajaxPostSocialTwitter'));
			add_action('wp_ajax_nopriv_ajaxPostSocialTwitter',						array($this, 'ajaxPostSocialTwitter'));
			add_action('wp_ajax_updatePostOrder',									array($this, 'updatePostOrder'));
			add_action('wp_ajax_updateTermOrder',									array($this, 'updateTermOrder'));
			add_action('init',														array($this, 'toggleDrafts'));
			add_action('init',														array($this, 'getFrontendOptions'));
			
			global $wptoolset_forms;
			remove_filter('sanitize_file_name', 									array($wptoolset_forms, 'sanitize_file_name') );
			add_filter('sanitize_file_name',										array($this, 'sanitizeFileName'), 10, 1);
			
			remove_filter('sanitize_title',											'sanitize_title_with_dashes', 11);
			add_filter('sanitize_title',											array($this, 'sanitizeTitle'), 12, 2);
			// add_filter('wp_unique_post_slug', 										'mg_unique_post_slug', 10, 6 );
			
			add_filter('wp_generate_attachment_metadata', 							array($this, 'purgeCloudflareFiles'));
			add_action('wp_handle_upload_prefilter', 								array($this, 'setAttachmentTitleRemember'));
			add_action('add_attachment', 											array($this, 'setAttachmentTitleSave'));
			add_action('enable-media-replace-upload-done', 							array($this, 'setAttachmentTitleSave'));
			add_action('add_attachment', 											array($this, 'savePDFTextToMeta'));
			add_action('edit_attachment', 											array($this, 'savePDFTextToMeta'));
			add_action('edit_attachment', 											array($this, 'savePostAttachment'), 10, 1);
			
			add_filter('image_size_names_choose', 									array($this, 'showAllImageSizesDropdown'), 11, 1);
			
			if(isset($_GET['purge_cache']) || isset($_POST['purge_cache'])) {
				$this->purgeMinifiedFiles();
				$this->deleteAPCDataAll();
				add_action('init',													array($this, 'purgePages'), 99, 1);
			}
			
			if(isset($_POST['delete_transients_prefix'])) {
				$this->deleteAPCDataPrefix();
			}
			
			add_action('wp_ajax_ajaxCreateDataEdit',								array($this, 'ajaxCreateDataEdit'), 0, 99);
			add_action('wp_ajax_nopriv_ajaxCreateDataEdit',						array($this, 'ajaxCreateDataEdit'), 0, 99);
			add_action('wp_ajax_ajaxCreateDataEditMeta',							array($this, 'ajaxCreateDataEditMeta'));
			add_action('wp_ajax_nopriv_ajaxCreateDataEditMeta', 				array($this, 'ajaxCreateDataEditMeta'));
			
			add_action('wp_ajax_ajaxDeleteIndividoleFormData',					array($this, 'ajaxDeleteIndividoleFormData'));
			
			add_action('wp_ajax_ajaxECWIDSaveTrackingNumbers',					array($this, 'ajaxECWIDSaveTrackingNumbers'));
			add_action('wp_ajax_ajaxECWIDSaveProductValue',						array($this, 'ajaxECWIDSaveProductValue'));
			add_action('wp_ajax_ajaxECWIDSaveOrderValue',						array($this, 'ajaxECWIDSaveOrderValue'));
			
			add_action('wp_ajax_ajaxGetIndividolePosts',							array($this, 'ajaxGetIndividolePosts'));
			add_action('wp_ajax_nopriv_ajaxGetIndividolePosts', 				array($this, 'ajaxGetIndividolePosts'));
			add_action('wp_ajax_ajaxGetIndividolePostsSelect2',				array($this, 'ajaxGetIndividolePostsSelect2'));
			add_action('wp_ajax_nopriv_ajaxGetIndividolePostsSelect2', 		array($this, 'ajaxGetIndividolePostsSelect2'));
			
			add_action('wp_ajax_ajaxSaveMeta',										array($this, 'ajaxSaveMeta'));
			add_action('wp_ajax_nopriv_ajaxSaveMeta',								array($this, 'ajaxSaveMeta'));
			add_action('wp_ajax_ajaxSaveDataEdit',									array($this, 'ajaxSaveDataEdit'));
			add_action('wp_ajax_nopriv_ajaxSaveDataEdit',						array($this, 'ajaxSaveDataEdit'));
			add_action('wp_ajax_ajaxSaveDataTags',									array($this, 'ajaxSaveDataTags'));
			add_action('wp_ajax_nopriv_ajaxSaveDataTags',						array($this, 'ajaxSaveDataTags'));
			add_action('wp_ajax_ajaxSaveOption',									array($this, 'ajaxSaveOption'));
			add_action('wp_ajax_nopriv_ajaxSaveOption',							array($this, 'ajaxSaveOption'));
			add_action('wp_ajax_ajaxSavePostTitles',								array($this, 'ajaxSavePostTitles'));
			add_action('wp_ajax_nopriv_ajaxSavePostTitles',						array($this, 'ajaxSavePostTitles'));
			
			// add_action('wp_ajax_ajaxDashboardGoogleAnalytics', 				array($this, 'ajaxDashboardGoogleAnalytics'));
			//$this->createFrontendTools();
			
			add_filter('custom_admin_menu', 											array($this, 'ameMenuOrder'), 10, 1);
			add_filter('custom_admin_submenu', 										array($this, 'ameMenuOrder'), 10, 2);
			
		} else {
			//add_filter('xmlrpc_enabled',							'__return_false');
			
		}
		
		add_action('init',																array($this, '_init'), 10);
		
		add_action('login_enqueue_scripts', 										array($this, 'enqueueScriptsLogin'), 10);
		add_action('wp_logout',															array($this, 'removeSession'));
	}
	
	function _init() {
		global $individole;
		
		global $individole_configs;
		global $config_cpt;

		if($this->isWPML()) {
			global $sitepress;

			remove_action( 'wp_head', array($sitepress, 'meta_generator_tag') );
		}

		$path_types = array(
			'repeater_modules',
			'base_stuff_modules',
			'flexible_modules',
		);

		$paths = array(
			get_stylesheet_directory().'/_acf_defaults',
			get_stylesheet_directory().'/'.$this->acf,
			$this->path['individole_acf'],
		);

		foreach($path_types AS $path_type) {
			if(!isset($individole_configs[$path_type])) {
				$individole_configs[$path_type] = array();
			}

			foreach($paths AS $path) {
				$path = $path.'/_'.$path_type.'/';

				foreach(glob($path.'*.php') AS $filename) {
					$var = str_replace($path, "", $filename);
					$var = str_replace(".php", "", $var);

					$individole_configs[$path_type][$var] = $var;
				}
			}
		}

		include_once($this->path['individole'].'/_classes/walker/individole_walker_nav_menu_admin.php');

		if(file_exists($this->path['base'].'/'.$this->acf.'/individole-configs.php')) {
			include_once($this->path['base'].'/'.$this->acf.'/individole-configs.php');
		}
		
		$this->local_fonts = $this->getLocalFonts();

		$custom_post_types = array();
		$config_cpt_theme = array();
		if(file_exists($this->path['base'].'/'.$this->acf.'/individole-configs-cpt.php')) {
			include_once($this->path['base'].'/'.$this->acf.'/individole-configs-cpt.php');
		}
		
		$config_cpt = $this->getCPTConfig(array(
			'custom_post_types'		=> $custom_post_types,
			'config_cpt_theme'		=> $config_cpt_theme,
		));
		
		if($this->isECWID()) {
			if($ecwid_settings = $this->ecwidGetSettings()) {
				$this->ecwid_settings = $ecwid_settings;
				$this->ecwid_design = $this->ecwidGetDesign();
				
				$this->ecwid = $this->getAPIDataEcwid(array(
					'force_refresh'	=> 1,
					'endpoint'			=> 'profile',
				));
				
				add_action('createECWIDScheduleInit', array($this, 'ecwidSendOrderNotification'));
			}
		}
		
		add_action('createGoogleAnalyticsInit', array($this, 'updateGoogleAnalyticsLocalData'));
		add_action('createVATRateEUScheduleInit', array($this, 'getVATRateEUInit'));

		if($this->io("wp_object_cache") == 1) {
			$object_cache_path = $this->path['libraries'].'/_object_cache/object-cache-4-everyone.php';
			if(file_exists($object_cache_path)) {
				include_once($object_cache_path);
				oc4everyone_plugins_loaded_activation();
			}
		}
		
		add_action('template_redirect', array($this, 'doRedirects'), 99);
	}
	
	function updateGoogleAnalyticsLocalData() {
		$obj = new individole_update_google_analytics_data();
		return $obj->create();
	}
	
	function accountIsLogged() {
		$obj = new individole_account_is_logged();
		return $obj->create();
	}
	
	function accountGetData() {
		$obj = new individole_account_get_data();
		return $obj->create();
	}
	
	function accountGetEmail() {
		$obj = new individole_account_get_email();
		return $obj->create();
	}
	
	function accountGetUserID() {
		$obj = new individole_account_get_user_id();
		return $obj->create();
	}
	
	function acfIcon($fa, $prepend="", $append="") {
		$fa = explode(',', $fa);
		
		$items = array();
		
		if(!empty($prepend)) {
			$items[] = '<b>'.$prepend.'</b>';
		}
		
		foreach($fa AS $icon) {
			$items[] = '<i class="'.$icon.'"></i>';
		}
		
		if(!empty($append)) {
			$items[] = '<b>'.$append.'</b>';
		}
		
		$return = '
			<div class="icon">'.implode("", $items).'</div>
		';
		
		return $return;
	}
	
	function acfImage($image, $w=0, $h=0, $title="") {
		if(($w == 0 || $w == "") && ($h == 0 || $h == "")) {
			$style = 'width:150px; height:auto;';
		
		} else if($w > 0 && $h > 0) {
			$style = 'width:'.$w.'px; height:'.$h.'px;';
		
		} else if($w > 0) {
			$style = 'width:'.$w.'px; height:auto;';
			
		} else if($h > 0) {
			$style = 'width:auto; height:'.$h.'px;';
		}
		
		$return = '<img src="'.$this->version($this->path['acf'].'/_images/'.$image).'" style="display:block; '.$style.'">';
		
		if(!empty($title)) {
			$return = '<div><b>'.$title.'</b></div>'.$return;
		}
		
		return $return;
	}
	
	function acfSetLanguage() {
		return acf_get_setting('default_language');
	}
	
	function acfCleanupUnusedPM($post_id) {
		if($this->isDennis() && $this->io("acf_cleanup") == 1) {
			$q = '
			DELETE FROM
			'.TABLE_PREFIX.'postmeta
			WHERE
			(meta_key LIKE "%page_content%" OR meta_key LIKE "%page_options%")
			AND '.TABLE_PREFIX.'postmeta.post_id = "'.$post_id.'"
			';
			
			mysqli_query($this->mysql, $q);
		}
	}
	
	function acfCSS() {
		return '<link href="/wp-content/plugins/advanced-custom-fields-pro/assets/build/css/acf-input.css" rel="stylesheet">';
	}
	
	function acfCreateLocalFieldGroups($args) {
		$obj = new individole_acf_create_local_field_groups();
		return $obj->create($args);
	}
	
	function acfFixGetFieldFunction( $value, $post_id, $field ) {
		if ( null !== $value ) {
			return $value;
		}
		
		if(isset($field['allow_null']) && $field['allow_null'] == 1) {
			return $value;
		}
		
		if(isset($field['type']) && ($field['type'] == "tab" || $field['type'] == "message")) {
			return $value;
		}
		
		// $this->debug($field);
		
		$decoded = acf_decode_post_id( $post_id );
		$id      = $decoded['id'];
		$type    = $decoded['type'];
		
		if ( ! $id ) {
			return $value;
		}
		
		$name = $field['name'];
		
		// if($this->isDennis()) {
		// 	$this->debug('acfFixGetFieldFunction executed! - field_name:'.$field['name'].' - $post_id:'.$post_id);
		// 	// $this->debug($field);
		// }
		
		if('option' === $type) {
			return get_option("{$id}_{$name}", null );
			
		} else {
			$meta = get_metadata( $type, $id, $name, false );
			return isset($meta[0]) ? $meta[0] : null;
		}
	}
	
	function acfFormatValue( $value, $post_id, $field ) {
		$value = $this->replaceStrings($value);
		
		if($field['type'] == 'wysiwyg') {
			$value = wpautop($value);
			$value = $this->removeParagraphsFromShortcodes($value);
		}
		
		return $value;
	}
	
	// function acfFormatValueWYSIWYG($value) {
	// 	$value = wpautop($value);
	// 	$value = $this->removeParagraphsFromShortcodes($value);
	//
	// 	return $value;
	// }
	
	function acfGetData($post_id, $args=array()) {
		$obj = new individole_acf_get_data();
		return $obj->create($post_id, $args);
	}
	
	function acfReplaceRowKeys($array){
		array_unique($this->acfGetRowKeys);
		
		if(!empty($this->acfGetRowKeys)) {
			$array = json_encode($array);
			
			foreach($this->acfGetRowKeys AS $old_key) {
				$new_key = str_replace("row-", "", $old_key);
				
				$array = str_replace('"'.$old_key.'":', '"'.$new_key.'":', $array);
			}
			
			$array = json_decode($array, true);
		}
		
		return $array;
	}
	
	function acfGetRowKeys($array){
		$return = array();
		foreach ($array as $key => $value) {
			if($this->startsWith($key, 'row-')) {
				$this->acfGetRowKeys[] = $key;
			}
			
			if (is_array($value)) {
				$value = $this->acfGetRowKeys($value);
			}
			
			$return[$key] = $value;
		}
		return $return;
	}
	
	function acfIncludeFieldTypes() {
		require($this->path['individole_acf'].'/individole-repeaters.php');
		require($this->path['individole_acf'].'/individole-flexible-fields.php');
		require($this->path['individole_acf'].'/individole-posts.php');
		require($this->path['individole_acf'].'/individole-post-object.php');
		require($this->path['individole_acf'].'/individole-multiple-choice.php');
		require($this->path['individole_acf'].'/individole-base-stuff.php');
		require($this->path['individole_acf'].'/individole-duration-picker.php');
		require($this->path['individole_acf'].'/individole-month-picker.php');
		require($this->path['individole_acf'].'/individole-year-picker.php');
		require($this->path['individole_acf'].'/individole-table.php');
		require($this->path['individole_acf'].'/individole-encrypt.php');
		require($this->path['individole_acf'].'/individole-hash.php');
		require($this->path['individole_acf'].'/individole-md5.php');
		require($this->path['individole_acf'].'/individole-message.php');
		require($this->path['individole_acf'].'/individole-file.php');
		require($this->path['individole_acf'].'/individole-countries.php');
	}
	
	function acfRemoveCloneIndex($array, $make_empty = false) {
		if(!is_array($array)) {
			return;
		}
		
		foreach ( $array as $key => $value ) {
			if(is_array($value)) {
				$array[$key] = $this->acfRemoveCloneIndex( $array[$key] );
			}
			
			if($key === "acfcloneindex") {
				unset($array[$key]);
			}
		}
		
		return $array;
	}
	
	function acfConvertPOST2Array($array) {
		$array = $this->acfRemoveCloneIndex($array);
		$array = $this->acfGetRowKeys($array);
		$array = $this->acfReplaceRowKeys($array);
		
		return $array;
	}
	
	function acfWYSIWYGToolbars($toolbars) {
		$obj = new individole_acf_wysiwyg_toolbars();
		return $obj->create($toolbars);
	}
	
	function prefix_wp_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
		$this->debug(1);
		
		$prefix = 'meta-';
		if ( strripos($slug, $prefix) !== 0 ) {
			$slug = $prefix . $slug;
		}
		
		return $slug;
	}

	function acfCreateMissingRepeaterFields($post_id, $post) {
		$c = $this->acfGetFieldGroupsByCPT($post->post_type);
		
		// if($this->isDennis()) {
		// 	$this->debug($c, 2);
		// }
		
		if(!empty($c)) {
			//$this->debug($c, 2);
			
			foreach($c AS $k => $v) {
				// if(!isset($v[0])) {
				// 	$this->debug($v, 2);
				// 	$this->debug($post, 2);
				// }
				
				if($v[0]['type'] == "individole_repeaters") {
					//$this->debug($v[0], 2);
					
					$check_fields = get_post_meta($post_id, $v[0]['name'], true);
					
					if($check_fields == 1) {
						
					} else {
						update_post_meta($post_id, $v[0]['name'], 1);
						update_post_meta($post_id, '_'.$v[0]['name'], $v[0]['key']);
					}
				}
			}
		}
	}
	
	function acfCreateMissingRepeaterFieldsOption($post_id) {
		if($this->isSuperAdmin() && $post_id == "options") {
			$acf_field_groups = acf_get_field_groups();
			foreach($acf_field_groups as $acf_field_group) {
				foreach($acf_field_group['location'] as $group_locations) {
					foreach($group_locations as $rule) {
						if($rule['param'] == 'options_page' && $rule['operator'] == '==' && $rule['value'] == 'acf-options-individole_special_settings') {
							$acf_options_fields = acf_get_fields( $acf_field_group );
							
							//$this->update_option("individole_save_post", $acf_options_fields);
							
							foreach($acf_options_fields AS $acf_options_field) {
								if($acf_options_field['type'] == "individole_repeaters") {
									$option_field = 'options_'.$acf_options_field['name'];
									
									$options_check = get_option($option_field);
									
									if(!$options_check) {
										$this->update_option($option_field, 1);
										$this->update_option('_'.$option_field, $acf_options_field['key']);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	function acfFieldPostObjectPrepare($field) {
		if(isset($field['field_key'])) {
			$field['key'] = $field['field_key'];
		}
		
		return $field;
	}
	
	function acfFieldPostObjectQuery( $args, $field, $post_id ) {
		if(isset($_COOKIE["select2_post_type"]) && $_COOKIE["select2_post_type"] != "") {
			$args['post_type'] = explode(",", $_COOKIE["select2_post_type"]);
		}
		
		return $args;
	}
	
	function acfFieldPostObjectRender( $field ) {
		//global $individole;
		
		//$individole->debug($_COOKIE["select2_post_type"]);
		//$individole->debug($field);
		
		if(isset($field['post_type']) && !empty($field['post_type'])) {
			if(!is_array($field['post_type'])) {
				$field['post_type'] = explode(",", $field['post_type']);
			}
			
			echo '<input class="select2_post_types" type="hidden" value="'.implode(",", $field['post_type']).'">';
		}
	}
	
	function acfGetFieldGroupsByCPT($cpt) {
		$result = array();
		$acf_field_groups = acf_get_field_groups();
		
		foreach($acf_field_groups as $acf_field_group) {
			if($acf_field_group['active'] == true) {
				// $this->debug('$acf_field_group', 2);
				// $this->debug($acf_field_group, 2);
				
				foreach($acf_field_group['location'] as $group_locations) {
					foreach($group_locations as $rule) {
						// $this->debug('$rule', 2);
						// $this->debug($rule, 2);
						
						if($rule['param'] == 'post_type' && $rule['operator'] == '==' && $rule['value'] == $cpt) {
							// $this->debug('ok 2a', 2);
							$result[] = acf_get_fields( $acf_field_group );
							// $this->debug('ok 2b', 2);
							
						} else if($rule['param'] == 'post_template' && $rule['operator'] == '==' && isset($_POST['page_template']) && $rule['value'] == $_POST['page_template']) {
							// $this->debug('ok 3a', 2);
							$result[] = acf_get_fields( $acf_field_group );
							// $this->debug('ok 3b', 2);
						}
					}
				}
			}
			
			// $this->debug('ok ok', 2);
		}
		
		return $result;
	}
	
	function acfGetFieldNameByFieldKey($field_key) {
		$field_object = get_field_object($field_key);
		//$this->debug($field_object, 2);
		
		if(isset($field_object['name'])) {
			return $field_object['name'];
		}
		
		return $field_key;
	}
	
	function acfGetOption($option) {
		return $this->getACFOption($option);
	}
	
	function acfPMAddColumn($column, $args=array()) {
		$obj = new individole_acf_pm_add_column();
		return $obj->create($column, $args);
	}
	
	function acfPMCreateTable() {
		$obj = new individole_acf_pm_create_table();
		return $obj->create();
	}
	
	function acfPMGetPostTypes() {
		$obj = new individole_acf_pm_get_posttypes();
		return $obj->create();
	}
	
	function acfPM($args) {
		$obj = new individole_acf_pm($args);
		return $obj->create();
	}
	
	function acfPMJoin($clauses) {
		$clauses['join'] .= ' LEFT JOIN '.TABLE_PREFIX.'individole_postmeta AS acf_pm ON ('.TABLE_PREFIX.'posts.ID = acf_pm.post_id)';
		return $clauses;
	}
	
	function acfPMRemove() {
		remove_filter('posts_clauses', array($this, 'acfPMJoin'));
		remove_filter('posts_clauses', array($this, 'acfPMWhere'));
		remove_filter('posts_clauses', array($this, 'acfPMOrderBy'));
	}
		
	function acfPMOrderBy($clauses) {
		if(strtolower((string) $this->acfPMOrderBy) == "rand()") {
			$clauses['orderby'] = 'rand()';
			
		} else {
			$clauses['orderby'] = 'acf_pm.'.$this->acfPMOrderBy.', '.$clauses['orderby'];
		}
		
		return $clauses;
	}
	
	function acfPMWhere($clauses) {
		foreach($this->acfPMWhere AS $pm) {
			$clauses['where'] .= ' AND '.str_replace("*", "acf_pm.", $pm);
		}
		
		return $clauses;
	}
	
	function acfPMUpdateData($post_id, $p) {
		$obj = new individole_acf_pm_update_data();
		return $obj->create($post_id, $p);
	}
	
	function acfJsonCreateFiles($args=array()) {
		if($this->isSuperadmin()) {
			$this->acfJsonCreateFolder();
			
			$field_groups = acf_get_field_groups();
			acf_update_setting('save_json', $this->acfJsonSavePoint());
			
			foreach ($field_groups as $v) {
				if($v['title'] != "Flexible Modules" && $v['active'] == 1) {
					$field_group = acf_get_field_group($v['key']);
					$field_group['fields'] = acf_get_fields($field_group);
					
					acf_write_json_field_group($field_group);
				}
			}
		}
	}
	
	function acfJsonCreateFolder() {
		$acf_json_theme = $this->acfJsonSavePoint();
		
		if(is_writable(WP_CONTENT_DIR)) {
			$acf_json_base = WP_CONTENT_DIR.'/acf-json';
			
			if(!file_exists($acf_json_base)) {
				mkdir($acf_json_base);
				chmod($acf_json_base, 0777);
			}
			
			//$this->debug($acf_json_theme);
			
			if(!file_exists($acf_json_theme)) {
				mkdir($acf_json_theme);
				chmod($acf_json_theme, 0777);
			}
		}
		
		return $acf_json_theme;
	}
	
	function acfJsonSavePoint() {
		$theme 			= wp_get_theme();
		$theme_name 	= $theme->get_stylesheet();
		
		//$this->debug($theme);
		//$this->debug(WP_CONTENT_DIR);
		
		$acf_json_path = WP_CONTENT_DIR.'/acf-json/'.$theme_name;
		
		return $acf_json_path;
	}
	
	function acfJsonLoadPoint( $paths ) {
		// remove original path (optional)
		unset($paths[0]);
		
		// append path
		$paths[] = $this->acfJsonSavePoint();
		
		//$this->debug($paths);
		
		// return
		return $paths;
	}
	
	function acfLocationRules_page_level($choices) {
		$choices['Page']['page_level'] = 'Page Level';
		return $choices;
	}
	
	function acfLocationRules_operators($choices) {
		$new_choices = [
			'<' => 'is less than',
			'<=' => 'is less than or equal to',
			'>=' => 'is greater than or equal to',
			'>' => 'is greater than'
		];
		foreach ($new_choices as $key => $value) {
			$choices[$key] = $value;
		}
		return $choices;
	}
	
	function acfLocationRules_values_page_level($choices) {
		for ($i = 1; $i <= 4; $i++) {
			$choices[$i] = $i;
		}
		return $choices;
	}
	
	
	
	function acfLocationRules_match_page_level($match, $rule, $options) {
		if (!isset($options['post_id'])) {
			return $match;
		}
		$page_level = count(get_post_ancestors($options['post_id'])) + 1;
		$operator = $rule['operator'];
		$value = intval($rule['value']);
		switch ($operator) {
			case '==':
				$match = ($page_level === $value);
				break;
			case '!=':
				$match = ($page_level !== $value);
				break;
			case '<':
				$match = ($page_level < $value);
				break;
			case '<=':
				$match = ($page_level <= $value);
				break;
			case '>=':
				$match = ($page_level >= $value);
				break;
			case '>':
				$match = ($page_level > $value);
				break;
		}
		return $match;
	}
	
	function addAdminBarTheme() {
		$obj = new individole_theme_adminbar();
		return $obj->create();
	}
	
	function addAdminIndividoleMenu() {
		$obj = new individole_add_admin_individole_menu();
		return $obj->create();
	}
	
	// function addAdminIndividoleSubMenu() {
	// 	$obj = new individole_theme_settings();
	// 	return $obj->create();
	// }
	
	function addHtaccessRules($args=array()) {
		$obj = new individole_add_htaccess_rules();
		return $obj->create($args);
	}
	
	function addImageByURL($image_url) {
		$upload_dir = wp_upload_dir();
		
		$image_data = file_get_contents( $image_url );
		
		$filename = basename( $image_url );
		
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
		  $file = $upload_dir['path'] . '/' . $filename;
		}
		else {
		  $file = $upload_dir['basedir'] . '/' . $filename;
		}
		
		file_put_contents( $file, $image_data );
		
		$wp_filetype = wp_check_filetype( $filename, null );
		
		$attachment = array(
		  'post_mime_type' => $wp_filetype['type'],
		  'post_title' => sanitize_file_name( $filename ),
		  'post_content' => '',
		  'post_status' => 'inherit'
		);
		
		$attach_id = wp_insert_attachment( $attachment, $file );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		
		return $attach_id;
	}
	
	function addImageSizes() {
		$obj = new individole_add_image_sizes();
		return $obj->create();
	}
	
	function addLibraryFilenamesCB()  {
		if ( has_action( 'admin_footer', 'wp_print_media_templates' ) ) {
			remove_action( 'admin_footer', 'wp_print_media_templates' );
			add_action( 'admin_footer', array($this, 'addLibraryFilenamesOutput'), 99);
		}
	}
	 
	function addLibraryFilenamesOutput() {
		$obj = new individole_add_library_filenames_output();
		return $obj->create();
	}
	
	function addLazyClass($text) {
		$obj = new individole_add_lazy_class();
		return $obj->create($text);
	}
	
	function addLibraryFields($form_fields, $post) {
		$obj = new individole_add_library_fields();
		return $obj->create($form_fields, $post);
	}
	
	function addLibraryFieldsSave($post, $attachment) {
		$obj = new individole_add_library_fields_save();
		return $obj->create($post, $attachment);
	}
	
	function addNewPost() {
		if(is_admin()) {
			$admin_screen = get_current_screen();
			
			if(is_object($admin_screen)) {
				if(property_exists($admin_screen, "action") && property_exists($admin_screen, "base")) {
					if($admin_screen->base == "post" && $admin_screen->action == "add") {
						return true;
					}
				}
			} else {
				$this->update_option('_individole_addNewPost', $_POST);
			}
		}
	}
	
	function addPostsClausesChildren($clauses) {
		$clauses['where'] .= ' AND '.TABLE_PREFIX.'posts.post_parent > 0';
		
		return $clauses;
	}
	
	function addPostsClausesNoCache($clauses) {
		if($this->isDennis()) {
			$clauses['fields'] = ' SQL_NO_CACHE '.$clauses['fields'];
		}
		
		return $clauses;
	}
	
	function addPostsClausesPostDateArchive($clauses) {
		global $post;
		
		$clauses['where'] .= ' AND '.TABLE_PREFIX.'posts.post_date < "'.$post->post_date.'"';
	
		return $clauses;
	}
	
	function addPostsClausesPMSitemap($clauses) {
		$obj = new individole_add_posts_clauses_pm_sitemap();
		return $obj->create($clauses);
	}
	
	function addPostsClausesPMHide($clauses) {
		$obj = new individole_add_posts_clauses_pm_hide();
		return $obj->create($clauses);
	}
	
	function addPostsClausesPostTitle($clauses) {
		$obj = new individole_add_posts_post_title();
		return $obj->create($clauses);
	}
	
	function addPostsClausesPostTitleANDName($clauses) {
		$obj = new individole_add_posts_post_title_and_name();
		return $obj->create($clauses);
	}
	
	function addPostsClausesPostTypeAttachment($clauses) {
		global $wpdb;
		
		$clauses['where'] .= ' AND '.TABLE_PREFIX.'posts.post_type = "attachment"';
		
		return $clauses;
	}
	
	function addMimeTypes($mime_types=array()) {
		$obj = new individole_add_mime_types();
		return $obj->create($mime_types);
	}
	
	function addPToText($text) {
		// !$this->startsWith($text, "<span") &&
		if(!$this->startsWith($text, "<div") && !$this->startsWith($text, "<table") && !$this->startsWith($text, "<iframe") && !$this->startsWith($text, "<h")) {
			if(!$this->startsWith($text, "<p")) {
				return '<p>'.$text;
			
			} else {
				return $text;
			}
			
		} else {
			return $text;
		}
	}
	
	function addQueryFilterOrderTitle($clauses) {
		global $wpdb;
	
		$clauses['orderby'] = $wpdb->posts.'.post_title ASC';
	
		return $clauses;
	}
	
	function addQueryFilterTitleSearch($clauses) {
		global $wpdb;
		
		$search_value = '';
		if(isset($_POST['value']) && $_POST['value'] != "") {
			$search_value = $_POST['value'];
			
		} else if(isset($_POST['search_value']) && $_POST['search_value'] != "") {
			$search_value = $_POST['search_value'];
		}
		
		if($search_value != "") {
			$clauses['where'] .= ' AND '.$wpdb->posts.'.post_title LIKE "%'.$search_value.'%"';
		}
		
		return $clauses;
	}
	
	function addQueryFilterWPML($clauses) {
		global $sitepress;
		
		$clauses['fields'] .= ', t_ind.language_code AS language';
		
		$clauses['join'] .= ' LEFT JOIN '.TABLE_PREFIX.'icl_translations t_ind ON '.TABLE_PREFIX.'posts.ID = t_ind.element_id AND t_ind.element_type = CONCAT("post_", '.TABLE_PREFIX.'posts.post_type)';
		
		$clauses['where'] .= ' AND (t_ind.language_code = "'.$sitepress->get_current_language().'" OR 0 )';
		
		//$this->debug($clauses);
		
		return $clauses;
	}
	
	function addServiceWorkerQueryParameter($url="") {
		$return = $url;
		
		if($this->io("add_offline_parameter") == 1 && ($this->getServiceWorker())) {
			$append = '';
			if($url != "") {
				if($this->containsWith($url, "?")) {
					$parse_url = parse_url($url);
					
					//$this->debug($parse_url);
					
					if(isset($parse_url['query'])) {
						$append = '&'.$parse_url['query'];
						$url = str_replace("?".$parse_url['query'], "", $url);
					}
				}
			}
			
			$return = $url.'?offline=1'.$append;
		}
		
		//$this->debug($return);
		
		return $return;
	}
	
	function addShadowboxToLinks($string) {
		global $page_id;
		
		$pattern = '/<a(.*?)href="(.*?).(bmp|gif|jpeg|jpg|png)"(.*?)>/i';
		$replacement = '<a$1href="$2.$3" rel=\'shadowbox['.$page_id.']\'$4>';
		return preg_replace($pattern, $replacement, (string) $string);
	}
	
	function addShortcodes() {
		$obj = new individole_add_shortcodes();
		return $obj->create($this);
	}
	
	function addWPEditorFieldClass() {
		$return = '
			<script>
				(function($) {
					acf.add_filter("wysiwyg_tinymce_settings", function( mceInit, id ) {
						var wysiwyg_id = $("#wp-" + mceInit.id + "-wrap").closest(".acf-field-wysiwyg").attr("id");
						
						if(wysiwyg_id != "") {
							mceInit.body_class += " " + wysiwyg_id;
						}
						
						return mceInit;
					});
				})(jQuery);
			</script>
		';
		
		echo $return;
	}
	
	function addWPEditorStylesheets($url) {
		$obj = new individole_add_wp_editor_stylesheets();
		return $obj->create($url);
	}
	
	function adjustBrightness($hexCode, $adjustPercent) {
		$hexCode = ltrim((string) $hexCode, '#');
		
		if (strlen((string) $hexCode) == 3) {
			$hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
		}
		
		$hexCode = array_map('hexdec', str_split($hexCode, 2));
		
		foreach ($hexCode as & $color) {
			$adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
			$adjustAmount = ceil($adjustableLimit * $adjustPercent);
			
			$color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
		}
		
		return '#' . implode($hexCode);
	}
	
	function e($p, $label="", $position="") {
		$obj = new individole_admin_create_edit();
		return $obj->create($p, $label, $position);
	}
	
	function e_center($p, $label="", $position="") {
		return $this->e($p, $label, 'center');
	}
	
	function e_left($p, $label="", $position="") {
		return $this->e($p, $label, 'left');
	}
	
	function e2($p, $label="", $position="") {
		$obj = new individole_admin_create_edit();
		return $obj->create($p, $label, $position, 1);
	}
	
	function edit($post_id, $label="", $position="left", $anchor="", $css="") {
		$obj = new individole_admin_create_edit_button();
		return $obj->create($post_id, $label, $position, $anchor, $css);
	}
	
	function createAdminButton($post_id, $label="", $position="left", $anchor="", $css="") {
		return $this->edit($post_id, $label, $position, $anchor, $css);
	}
	
	function adminDashboardSetup() {
		$this->getACFFieldsDefaults();
		$this->modifyAdminDashboards();
		$this->updateVersionInfo();
		
		if(is_admin() && $this->isSuperAdmin()) {
			$this->acfPMCreateTable();
			$this->formsCreateTable();
			//$this->apcCreateTable();
		}
	}
	
	function adminDashboardQuicklinks() {
		$obj = new individole_admin_dashboard_quicklinks();
		return $obj->create();
	}
	
	function adminGetErrorLog() {
		$obj = new individole_admin_get_error_log($this->error_log);
		return $obj->returnJson();
	}
	
	function adminCurrentPage($page="") {
		if($page != "") {
			$screen = get_current_screen();
			
			if($screen->id == $page) {
				return true;
			}
		}
	}
	
	function adminNotice($text) {
		echo '
			<div class="notice notice-info">
				<p>Modify Upload Folder Year/Month before upload:
				<select id="modify_upload_month">'.$text.'</select>
			</div>
		';
	}
	
	function adminNoticeHTACCESS() {
		$this->adminNotice('<b>Error 500</b><br>Something went wrong!');
	}
	
	function adminNoticeMediaUpladDir() {
		$current_year 	= date('Y');
		$current_month 	= date('n');
		
		$options = array();
		for($i_month = $current_month; $i_month >= 1; --$i_month) {
			$options[] = '<option value="/'.$current_year.'/'.$this->formatNumberZero($i_month).'">'.$current_year.'-'.$this->formatNumberZero($i_month).'</option>';
		}
		
		for($i_year = $current_year - 1; $i_year >= $current_year - 5; --$i_year) {
			for($i_month = 12; $i_month >= 1; --$i_month) {
				$options[] = '<option value="/'.$i_year.'/'.$this->formatNumberZero($i_month).'">'.$i_year.'-'.$this->formatNumberZero($i_month).'</option>';
			}
		}
		
		$this->adminNotice(implode("", $options));
	}
	
	function adminNoticeSCSS2CSS(){
		$this->adminNotice('<b>SCSS2CSS</b><br>CSS files successfully created!');
	}
	
	function adminSettingsButton($url) {
		$return = '
			'.$this->clearer(10).'<a href="'.$url.'" class="button button_green button_small button_single"><i class="fas fa-cog"></i><b> Edit settings</b></a>
		';
		
		return $return;
	}
	
	function adminShowDebug() {
		global $individole_admin_show_debug;
		
		++$individole_admin_show_debug;
				
		if($individole_admin_show_debug > 1) {
			$this->debug("Double init --> individole_admin_show_debug");
		}
		
		if($this->isAdmin()) {
			if(!empty($this->debug_final) && ($this->isSuperAdmin() || SHOW_DEBUG == true)) {
				$final_debug = '<div id="debug" class="noprint"><div id="debug_close">close</div>'.implode("", $this->debug_final).'</div>';
				
				if(is_admin()) {
					echo $final_debug;
					
				} else {
					return $final_debug;
				}
			}
		}
		
		if(isset($this->mysql)){
			mysqli_close($this->mysql);
			unset($this->mysql);
		}
	}
	
	function ajaxCountReadPost() {
		$obj = new individole_ajax_count_read_post();
		return $obj->create();
	}
	
	function ajaxCreateDataACFPM() {
		$obj = new individole_ajax_create_data_acf_pm();
		return $obj->create();
	}
	
	function ajaxCreateDataEdit() {
		$obj = new individole_ajax_data_edit();
		return $obj->create();
	}
	
	function ajaxCreateDataEditField($args) {
		$obj = new individole_ajax_data_edit_field($args);
		return $obj->create();
	}
	
	function ajaxCreateDataEditMeta() {
		$obj = new individole_ajax_data_edit_meta();
		return $obj->create();
	}
	
	function ajaxCreateAccount() {
		$obj = new individole_ajax_create_account();
		return $obj->create();
	}
	
	function ajaxCreateInstagramTestwall() {
		$obj = new individole_ajax_create_instagram_testwall();
		return $obj->create();
	}
	
	function ajaxCreatePNGFromBase64() {
		$obj = new individole_ajax_create_png_from_base64();
		return $obj->create();
	}
	
	function ajaxCreatePosts() {
		$obj = new individole_ajax_create_posts();
		return $obj->create();
	}
	
	function ajaxDashboardGoogleAnalytics() {
		$obj = new individole_ajax_dashboard_google_analytics();
		return $obj->create();
	}
	
	function ajaxDelete404() {
		$obj = new individole_save_404();
		return $obj->delete();
	}
		
	function ajaxDeleteIndividoleFormData() {
		$obj = new individole_ajax_delete_individole_form_data();
		return $obj->create();
	}
	
	function ajaxECWIDSaveOrderValue() {
		$obj = new individole_ecwid_save_order_value();
		return $obj->create();
	}
	
	function ajaxECWIDSaveProductValue() {
		$obj = new individole_ecwid_save_product_value();
		return $obj->create();
	}
	
	function ajaxECWIDSaveTrackingNumbers() {
		$obj = new individole_ecwid_save_tracking_numbers();
		return $obj->create();
	}
	
	function ajaxFacebookUser() {
		$obj = new individole_facebook();
		return $obj->getUser();
	}
	
	function ajaxGetStats() {
		$ga = new individole_google_analytics(array());
		$ga->getDataLocal();
	}
	
	function ajaxGetAIAPIRequest($args=array()) {
		$obj = new individole_ajax_get_ai_api_request();
		return $obj->create($args);
	}
	
	function ajaxGetDeeplTranslation() {
		$obj = new individole_ajax_get_deepl_translation();
		return $obj->create();
	}
	
	function ajaxGetDeeplTranslationButton() {
		$obj = new individole_ajax_get_deepl_translation_button();
		return $obj->create();
	}
	
	function ajaxGetGoogleTranslate($args=array()) {
		$obj = new individole_ajax_get_google_translate();
		return $obj->create($args);
	}
	
	function ajaxGetIndividolePosts() {
		$obj = new individole_ajax_posts();
		return $obj->create();
	}
	
	function ajaxGetIndividolePostsSelect2() {
		$obj = new individole_ajax_posts_select2();
		return $obj->create();
	}
	
	function ajaxGetAdminSearchresults() {
		$obj = new individole_ajax_get_admin_searchresults();
		return $obj->create();
	}
	
	function ajaxGetPost() {
		$obj = new individole_ajax_get_post();
		return $obj->create();
	}
	
	function ajaxGetSearchSuggestions() {
		$obj = new individole_ajax_get_search_suggestions();
		return $obj->create();
	}
	
	function ajaxGetWordIndex() {
		$obj = new individole_ajax_get_wordindex();
		return $obj->create();
	}
	
	function ajaxGetLocalTime() {
		$obj = new individole_ajax_get_local_time();
		return $obj->create();
	}
	
	function ajaxGetCheckMail() {
		$obj = new individole_ajax_get_check_mail();
		return $obj->create();
	}
	
	function ajaxGetMetaGoogle($args) {
		$obj = new individole_ajax_get_meta_google();
		return $obj->create($args);
	}
	
	function ajaxSetNonce() {
		$obj = new individole_ajax_set_nonce();
		return $obj->create();
	}
	
	function ajaxIndividoleSendForm() {
		$obj = new individole_forms_send_mail();
		return $obj->create();
	}
	
	function ajaxLogoutAccount() {
		if(isset($_SESSION['account'])) { unset($_SESSION['account']); }
	}
	
	function ajaxMailchimpSendForm($args=array()) {
		$obj = new individole_ajax_mailchimp_send_form();
		return $obj->create($args);
	}
	
	function ajaxSendInBlueSendForm($args=array()) {
		$obj = new individole_ajax_sendinblue_send_form();
		return $obj->create($args);
	}
	
	function ajaxPostSocialTwitter($args) {
		
	}
	
	function ajaxGetACFSingleValue() {
		$obj = new individole_ajax_get_acf_single_value();
		return $obj->create();
	}
	
	function ajaxGetPermalink() {
		$obj = new individole_ajax_get_permalink();
		return $obj->create();
	}
	
	function ajaxSaveACFSingleValue() {
		$obj = new individole_ajax_save_acf_single_value();
		return $obj->create();
	}
	
	function ajaxSaveAdminMetaboxDuplicatePM() {
		$obj = new individole_ajax_save_admin_metabox_duplicate_pm();
		return $obj->create();
	}
	
	function ajaxSaveAdminValue() {
		$obj = new individole_ajax_save_admin_value();
		return $obj->create();
	}
	
	function ajaxSaveAttachmentFilters() {
		$obj = new individole_ajax_save_attachment_filters();
		return $obj->create();
	}
	
	function ajaxSaveMediathekData() {
		$obj = new individole_ajax_save_mediathek_data();
		return $obj->create();
	}
	
	function ajaxSaveMeta() {
		$obj = new individole_ajax_save_meta();
		return $obj->create();
	}
	
	function ajaxSaveDataEdit() {
		$obj = new individole_ajax_save_data_edit();
		return $obj->create();
	}
	
	function ajaxSaveDataTags() {
		$obj = new individole_ajax_save_data_tags();
		return $obj->create();
	}
	
	function ajaxSaveOption() {
		$obj = new individole_ajax_save_option();
		return $obj->create();
	}
	
	function ajaxSavePostTitles() {
		$obj = new individole_ajax_save_post_titles();
		return $obj->create();
	}
	
	function ajaxSaveSessionValue() {
		$obj = new individole_ajax_save_session_value();
		return $obj->create();
	}
	
	function ajaxSaveToolFormsFilters() {
		$obj = new individole_ajax_save_tool_forms_filters();
		return $obj->create();
	}
	
	function ajaxSetCookie() {
		if(isset($_POST['name']) && isset($_POST['expires']) && isset($_POST['value'])) {
			if(isset($_POST['path'])) {
				$cookie_path = $_POST['path'];
				
			} else {
				$cookie_path = '/';
			}
			
			$expires = time() + $_POST['expires'];
			$expires = (int)$expires;
			
			setcookie($_POST['name'], $_POST['value'], $expires, $cookie_path);
		}
	}
	
	function ajaxSetSession() {
		if(isset($_POST['name']) && isset($_POST['value'])) {
			$_SESSION[$_POST['name']] = $_POST['value'];
		}
	}
	
	function ajaxToggleEdit() {
		if($this->isAdmin()){
			if(isset($_SESSION['individole_edit'])) {
				unset($_SESSION['individole_edit']);
				
			} else {
				$_SESSION['individole_edit'] = 1;
			}
			
		}
	}
	
	function ajaxToggleGAEvents() {
		if($this->isAdmin()){
			if(isset($_SESSION['individole_ga_events'])) {
				unset($_SESSION['individole_ga_events']);
				
			} else {
				$_SESSION['individole_ga_events'] = 1;
			}
			
		}
	}
	
	function ajaxToggleGrid() {
		if($this->isAdmin()){
			if(isset($_SESSION['individole_grid'])) {
				unset($_SESSION['individole_grid']);
				
			} else {
				$_SESSION['individole_grid'] = 1;
			}
		}
		
		echo json_encode($_SESSION);
		exit;
	}
	
	function ajaxToggleHideDrafts() {
		if($this->isAdmin()){
			if(isset($_SESSION['individole_hide_draft'])) {
				unset($_SESSION['individole_hide_draft']);
				
			} else {
				$_SESSION['individole_hide_draft'] = 1;
			}
			
		}
	}
	
	function ajaxURLToPostID() {
		$obj = new individole_ajax_url_to_postid();
		return $obj->create();
	}
	
	function ameMenuOrder($item, $parentSlug="") {
		// $this->debug($parentSlug);
		// $this->debug($item);
		
		if($parentSlug != "") {
			// $this->debug($item);
			
			$parentSlugSpecials = array(
				'admin',
				'individole',
				'ecwid',
				'ecwid_settings',
				'ecwid_design',
			);
			
			if(in_array($parentSlug, $parentSlugSpecials)) {
				// if(isset($this->individole_admin_menu_positions[$item['file']]) && $this->individole_admin_menu_positions[$item['file']] > -1) {
				// 	$item['position'] = $this->individole_admin_menu_positions[$item['file']];
				// }
				
				if(isset($this->individole_admin_menu_positions[$item['file']])) {
					$item['position'] = $this->individole_admin_menu_positions[$item['file']];
				}
				
				if($this->startsWith($item['file'], '#separator-')) {
					$item['separator'] = true;
				}
			}
			
		} else {
			// $this->debug($item);
			
			if(in_array($item['file'], $this->individole_admin_menu_positions['misc'])) {
				$item['position'] = 1000 + array_search($item['file'], $this->individole_admin_menu_positions['misc']);
				
				// $this->debug($item);
				
			} else {
				// $this->debug($item['file']);
			}
		}
		
		return $item;
	}
	
	function apcCreateTable() {
		$obj = new individole_apc_create_table();
		return $obj->create();
	}
	
	function arrayEqual($a, $b) {
		 return (
	 		  is_array($a) && is_array($b) &&
			 count($a) == count($b) &&
			 array_diff($a, $b) === array_diff($b, $a)
		);
	}
	
	function arrayInsert( array $array, $key, array $new ) {
		$keys = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos = false === $index ? count( $array ) : $index + 1;
	
		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}
	
	function arrayShuffle($list) {
		if (!is_array($list)) {
			return $list;
		}

		$keys = array_keys($list);
		shuffle($keys);
		$random = array();
		foreach ($keys as $key) {
			$random[$key] = $list[$key];
		}
			
		return $random;
	}
	
	function arraySortByArray($order, $array) {
		return array_replace(array_flip($order), $array);
	}
	
	function arraySplicePreserveKeys(&$input, $offset, $length=null, $replacement=array()) {
		 if (empty($replacement)) {
			  return array_splice($input, $offset, $length);
		 }
	
		 $part_before  = array_slice($input, 0, $offset, $preserve_keys=true);
		 $part_removed = array_slice($input, $offset, $length, $preserve_keys=true);
		 $part_after	= array_slice($input, $offset+$length, null, $preserve_keys=true);
	
		 $input = $part_before + $replacement + $part_after;
		
		//e.g.:
		//$individole->arraySplicePreserveKeys($array, 1, 0, $new_array_to_insert);
		
		 return $part_removed;
	}
	
	function array2Table($array, $args=array()){
		$obj = new individole_create_table_from_array();
		return $this->minifyHTML($obj->create($array, $args), 1);
	}
	
	function array2Tree($elements = array(), $parent_id = 0, $parent_var = 'parent_id') {
		$tree = array();
		
		if(!empty($elements)) {
			foreach($elements as $element) {
				//$this->debug($element);
				
				if(isset($element[$parent_var]) && $element[$parent_var] == $parent_id) {
					$children = $this->array2Tree($elements, $element['id'], $parent_var);
					if ($children) {
						$element['children'] = $children;
					}
				}
				
				$tree[] = $element;
			}
		}
		
		return $tree;
	}
	
	function autoloadClasses($class_name) {
		$class_name_explode = explode("_", $class_name);
		
		if($class_name_explode[0] == "individole") {
			if($class_name_explode[1] == "api") {
				$file = $this->path['individole'].'/_api/'.$class_name.'.php';
				
			} else if($class_name_explode[1] == "modules") {
				$file = $this->path['individole'].'/_modules/'.$class_name.'.php';
				
			} else if($class_name_explode[1] == "dashboard") {
				$file = $this->path['individole'].'/_dashboard/'.$class_name.'.php';
				
			} else {
				$file = $this->path['individole'].'/_classes/'.$class_name_explode[1].'/'.$class_name.'.php';
			}
			
			if(file_exists($file)) {
				require_once($file);
			
			} else {
				$file = $this->path['individole'].'/_classes/'.$class_name.'.php';
			
				if(file_exists($file)) {
					require_once($file);
				}
			}
		}
	}
	
	function cookiebot($cookie="") {
		if($this->is_plugin_active("cookiebot/cookiebot.php")) {
			$cookiebot_data = get_plugin_data(ABSPATH."wp-content/plugins/cookiebot/cookiebot.php");
			// $this->debug($cookiebot_data);
			
			$this->cookiebot_version = $cookiebot_data['Version'];
			
			return true;
		}
	}
	
	function borlabs($cookie="") {
		if(!empty($this->borlabs)) {
			// $this->debug($this->borlabs);
			
			if(!empty($cookie)) {
				if(isset($this->borlabs[$cookie])) {
					return $this->borlabs[$cookie];
				}
				
			} else {
				return true;
			}
		}
		
		$borlabs_active = "";
		if($this->is_plugin_active("borlabs-cookie-v2/borlabs-cookie.php")) {
			$borlabs_active = ABSPATH."wp-content/plugins/borlabs-cookie-v2/borlabs-cookie.php";
		
		} else if($this->is_plugin_active("borlabs-cookie/borlabs-cookie.php")) {
			$borlabs_active = ABSPATH."wp-content/plugins/borlabs-cookie/borlabs-cookie.php";
		}
		
		// $this->debug($borlabs_active);
		
		if(!empty($borlabs_active)) {
			$borlabs_data = get_plugin_data($borlabs_active);
			
			// $this->debug($borlabs_data);
			
			if($borlabs_data['Version'] >= 3) {
				$this->borlabs_version = 3;
				
				$q = '
				SELECT
					*
				FROM
					'.TABLE_PREFIX.'borlabs_cookie_content_blockers
				WHERE
					`status` = 1
				GROUP BY
					`key`
				';
				$result = mysqli_query($this->mysql, $q);
				
				if($result) {
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						$this->borlabs[$row['key']] = $row['key'];
						$this->borlabs[$row['borlabs_service_package_key']] = $row['borlabs_service_package_key'];
					}
					
					// $this->debug($this->borlabs);
					
					if(!empty($cookie)) {
						if(isset($this->borlabs[$cookie])) {
							return $this->borlabs[$cookie];
						}
					
					} else {
						return true;
					}
				}
				
			} else {
				$this->borlabs_version = 2;
				
				$borlabs = new BorlabsCookie\Cookie\Frontend\Cookies;
				$cookies = $borlabs->getAllCookiesOfLanguage($this->l);
				
				foreach($cookies AS $k => $v) {
					$cookies[$k] = $k;
				}
				
				$this->borlabs = $cookies;
				
				// $this->debug($cookies);
				
				if(!empty($cookie)) {
					if(isset($this->borlabs[$cookie])) {
						return $this->borlabs[$cookie];
					}
				
				} else {
					return true;
				}
			}
		}
		
		return false;
	}
	
	function borlabsFacebook() {
		return $this->borlabs("facebook");
	}
	
	function borlabsGoogleMaps() {
		$status = $this->borlabs("googlemaps");
		// $this->debug('borlabsGoogleMaps / googlemaps / '.$status);
		
		if(!$status) {
			$status = $this->borlabs("google-maps");
			// $this->debug('borlabsGoogleMaps / google-maps / '.$status);
		}
		
		return $status;
	}
	
	function borlabsYoutube() {
		return $this->borlabs("youtube");
	}
	
	function borlabsVimeo() {
		return $this->borlabs("vimeo");
	}
		
	function br2nl($string) {
		$return = $string;
		
		$return = str_replace("<br /> ", "<br />", $return);
		$return = preg_replace('#<br\s*?/?>#i', "\r", $return);
		$return = preg_replace('#\r\r#i', "\r", $return);
		
		return $return;
	}
	
	function checkCronFuturePost($post_id) {
		if(!isset($this->current_cronlist)) {
			$this->current_cronlist = _get_cron_array();
		}
		
		//$this->debug($this->current_cronlist);
		
		if(!empty($this->current_cronlist)) {
			foreach($this->current_cronlist AS $k => $v) {
				$cron_name = array_key_first($v);
				
				if($cron_name == "publish_future_post") {
					$v = reset($v);
					$v = reset($v);
					
					if(isset($v['args']) && in_array($post_id, $v['args'])) {
						return true;
					
					} else {
						$this->debug($v);
					}
				}
			}
		}
	}
	
	function checkMail($email) {
		$obj = new individole_check_mail();
		return $obj->create($email);
	}
	
	function checkMailSyntax($email) {
		return $this->checkMail($email);
	}
	
	function checkPagePassword() {
		$obj = new individole_check_page_password();
		return $obj->create();
	}
	
	function checkSEODescription($post_id=0) {
		$obj = new individole_check_seo();
		return $obj->create($post_id, "description");
	}
	
	function checkSEOTitle($post_id=0) {
		$obj = new individole_check_seo();
		return $obj->create($post_id, "title");
	}
		
	function checkURLSyntax($url) {
		//return $url;
		
		$pattern = "=^([a-z0-9\-_]{2,}\.)+([a-z0-9\-_]+)\.(.*)$=i";
		//$check = "=^([a-z0-9\-_]+)\.(.*)$=i";
		$pattern = "#(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?(.*)*$#i";
		
		if(preg_match($pattern, $url)) {
			//echo "<p>check_url_syntax: 1 ($url)";
			return $url;
		
		} else {
			//echo "<p>check_url_syntax: 2 ($url)";
			return false;
		}
	}
	
	function cleanPostValue($value) {
		return str_replace('"', "'", urldecode((string) $value));
	}
	
	function clearer($h=0, $id="", $args=array()) {
		$obj = new individole_create_clearer();
		return $obj->create($h, $id, $args);
	}
	
	function col($args, $offset=false) {
		return $this->getColumnWidth($args, $offset);
	}
	
	function containsWith($string, $string_to_find) {
		if(is_array($string_to_find)) {
			foreach($string_to_find AS $v) {
				if(strpos($string, $v) !== false ) {
					return true;
				}
			}
			
		} else if(is_string($string) && strpos($string, $string_to_find) !== false ) {
			return true;
		}
	}
	
	function convertEmail($email) {
		$p = str_split(trim((string) $email));
		$new_mail = '';
		foreach ($p as $val) {
			$new_mail .= '&#'.ord($val).';';
		}
		
		return $new_mail;
	}
	
	function convertEntities($field){
		$obj = new individole_convert_entities();
		return $obj->create($field);
	}
	
	function convertLinebreaksToPTags($str) {
		//$str = preg_replace('/\n(\s*\n)+/', '</p><p>', $str);
		//$str = preg_replace('/\n/', '<br>', $str);
		//$str = "<p>".$str."</p>";
		
		$str = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $str);
		
		return $str;
	}
	
	function convertH1($string) {
		$return = str_replace(array(
			'<h1',
			'/h1>',
			
		), array(
			'<h2',
			'/h2>',
			
		), $string);
		
		return $return;
	}
	
	function convertHTMLToInlineCSS($html, $css="") {
		$file = $this->path['libraries'].'/_emogrifier/Emogrifier.php';
	
		require_once($file);
		
		$emogrifier = new \Pelago\Emogrifier($html, $css);
		$emogrifier->enableCssToHtmlMapping();
		$emogrifier->disableStyleBlocksParsing();
		// $emogrifier->disableInlineStyleAttributesParsing();
		$emogrifier->disableInvisibleNodeRemoval();
		
		$return = $emogrifier->emogrifyBodyContent();
		
		return $return;
	}
	
	function replaceNoHyphens($text, $args=array()) {
		if(empty($args)) {
			$args = $this->iWord("replace_nohyphens");
			
			if(!empty($args)) {
				$args = explode(",", $args);
			}
		}
		
		if(empty($args)) {
			return $text;
			
		} else {
			$output = $text;
			foreach($args AS $string) {
				$output = preg_replace_callback("#".trim((string) $string)."#is", function($m){
					return '<span class="nohyphens">'.$m[0].'</span>';
					
				}, $output);
			}
			
			return $output;
		}
	}
	
	function convertSCSS2CSS() {
		$obj = new individole_convert_scss_to_css();
		return $obj->create();
	}
	
	function convertTablesResponsive($htmlContent) {
		if($this->containsWith($htmlContent, "table_responsive")) {
			libxml_use_internal_errors(true);
			
			try {
				$dom = new DOMDocument;
				$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
				$xpath = new DOMXPath($dom);
				
				$tables = $xpath->query('//table[contains(@class, "table_responsive")]');
				
				foreach ($tables as $table) {
					$rows = $table->getElementsByTagName('tr');
					if($rows->length > 0) {
						$firstRow = $rows->item(0);
						$header = $dom->createElement('thead');
						$header->appendChild($firstRow->cloneNode(true));
						$table->insertBefore($header, $table->firstChild);
						
						$headers = [];
						foreach ($firstRow->childNodes as $headerCell) {
							if ($headerCell->nodeType === XML_ELEMENT_NODE) {
								$headers[] = $headerCell->textContent;
							}
						}
						
						$table->getElementsByTagName('tbody')->item(0)->removeChild($firstRow);
						
						for($i = 1; $i < $rows->length; $i++) {
							$cells = $rows->item($i)->getElementsByTagName('td');
							for($j = 1; $j < $cells->length; $j++) {
								if(isset($headers[$j])) {
									$cells->item($j)->setAttribute('data-label', $headers[$j]);
								}
							}
						}
					}
				}
			
				return $dom->saveHTML();
				
			} catch (Exception $e) {
				libxml_clear_errors();
				return $htmlContent;
			}
			
		} else {
			return $htmlContent;
		}
	}
	
	function convertSeconds($seconds) {
		$return = '';
		
		$hours = floor($seconds / 3600);
		$mins = floor($seconds / 60 % 60);
		$secs = floor($seconds % 60);
		
		if($hours > 0) {
			$return = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
			
		} else {
			$return = sprintf('%02d:%02d', $mins, $secs);
		}
		
		return $return;
	}
	
	function replaceSZett($text, $args=array()) {
		if(empty($args)) {
			return str_replace("ß", "&#7838;", $text);
			
		} else {
			$output = $text;
			foreach($args AS $tag) {
				$tag = trim((string) $tag);
				
				$output = preg_replace_callback("#<".$tag.">(.+?)<\/".$tag.">#is", function($m){
					return str_replace("ß", "&#7838;", $m[0]);
					
				}, $output);
			}
			
			return $output;
		}
	}
	
	function convertToArray($obj) {
		$json = json_encode($obj);
		return json_decode($json, true);
	}
	
	function convertToNumber($val) {
		$val = str_replace(",",".",$val);
		$val = preg_replace('/\.(?=.*\.)/', '', $val);
		return floatval($val);
	}
	
	function convertToPostDate($date) {
		$return = $this->formatDate(array(
			'format'	=> '%Y-%m-%d %H:%M:%S',
			'date'		=> $date,
		));
		
		return $return;
	}
	
	function convertToRoem($arabische_zahl) {
		$obj = new individole_convert_to_roem();
		return $obj->create($arabische_zahl);
	}
	
	function create404($args=array()) {
		$obj = new individole_create_404();
		return $obj->create($args);
	}
	
	function createAccordion($args, $args2="", $args3="") {
		$obj = new individole_create_accordion();
		return $obj->create($args, $args2, $args3);
	}
	
	function createTextColumns($args, $args2="", $args3="") {
		$obj = new individole_create_text_columns();
		return $obj->create($args, $args2, $args3);
	}
	
	function createACFConditionalLogic($v=array()) {
		$obj = new individole_acf_create_conditional_logic();
		return $obj->create($v);
	}
	
	function createACFFields($args) {
		$obj = new individole_acf_create_fields();
		return $obj->create($args);
	}
	
	function createACFMessage($text="???", $label="", $conditional_logic="") {
		$obj = new individole_acf_create_message();
		return $obj->create($text, $label, $conditional_logic);
	}
	
	function createACFMessageText($text="???") {
		$return = array(
			'label'				=> '',
			'message'			=> do_shortcode($text),
			'type'				=> 'individole_message',
		);
		
		return $return;
	}
	
	function createACFSubtitle($text="???", $label="", $conditional_logic="") {
		$text = '<span class="subtitle">'.$text.'</span>';
		
		return $this->createACFMessage($text, $label, $conditional_logic);
	}
	
	function createACFTab($text="???", $conditional_logic=array(), $placement="") {
		$return = array(
			'label'				=> do_shortcode($text),
			'type'				=> 'tab',
			'_name'				=> 'tab_'.$this->acfTab,
			'name'				=> 'tab_'.$this->acfTab,
			'key'				=> 'tab_'.$this->acfTab,
		);
		
		++$this->acfTab;
		
		//$this->debug($conditional_logic);
		
		if(!empty($conditional_logic)) {
			$return['conditional_logic'] = $this->createACFConditionalLogic($conditional_logic);
		}
		
		if($placement != "") {
			$return['placement'] = $placement;
		}
		
		return $return;
	}
	
	function createACFTitle($text="???", $label="", $conditional_logic="") {
		$text = '<span class="title">'.$text.'</span>';
		
		return $this->createACFMessage($text, $label, $conditional_logic);
	}
	
	function createAdminFlag($lang="", $mr=4, $m="") {
		$obj = new individole_admin_create_flag();
		return $obj->create($lang, $mr, $m);
	}
	
	function createFlag($lang="") {
		$obj = new individole_create_flag();
		return $obj->create($lang);
	}
	
	function createIcon($icon, $style="regular", $args=array()) {
		$obj = new individole_create_icon();
		return $obj->create($icon, $style, $args);
	}
	
	function createICS($args=array()) {
		$obj = new individole_create_ics();
		return $obj->create($args);
	}
	
	function createAdminGrid($args) {
		$obj = new individole_admin_create_grid($args);
		return $obj->create();
	}
	
	function createAdminImageUpload($args=array()) {
		$obj = new individole_admin_create_image_upload();
		return $obj->create($args);
	}
	
	function createAdminFileUpload($args=array()) {
		$obj = new individole_admin_create_file_upload();
		return $obj->create($args);
	}
	
	function createAdminOptions(){
		$obj = new individole_frontend_options();
		return $obj->create();
	}
	
	// function createAdminPostTypeFilter($args=array()) {
	// 	$obj = new individole_admin_create_post_type_filter();
	// 	return $obj->create($args);
	// }
	
	function createAltTitleTag($args="") {
		$obj = new individole_create_alt_title_tag();
		return $obj->create($args);
	}
	
	function createAltTitleLinkTag($post_id, $reset=false) {
		$title_link_tag = $this->getMetaInternalLink($post_id, $reset);
		$title_link_tag = $this->formatTitleTag($title_link_tag);
		
		if($title_link_tag != "") {
			$return = ' title="'.$title_link_tag.'"';
			
			return $return;
		}
	}
	
	function createArrayDates($start, $end='now') {
		$obj = new individole_create_array_dates();
		return $obj->create($start, $end);
	}
	
	function createBackendDashboardsOutput() {
		echo $this->dashboard_data->output();
	}
	
	function createBackToTop($args) {
		$obj = new individole_create_back_to_top();
		return $obj->create($args);
	}
	
	function createBodyClasses($classes=array()) {
		$obj = new individole_create_body_classes();
		return $obj->create($classes);
	}
	
	function createBootstrapDivider() {
		$svg = $this->getSVGContent('/_individole/_images/bootstrap_divider.svg');
		
		$return = '
			<div class="bootstrap_divider">'.$svg.'</div>
		';
		
		return $return;
	}
	
	function createBreadcrumb($args) {
		$menu = $this->createMenu($args);
	}
	
	function createBreadcrumbByID($id, $args=array()) {
		$obj = new individole_create_breadcrumb_by_id();
		return $obj->create($id, $args);
	}
	
	function createButton($args, $args2="", $args3="") {
		$obj = new individole_create_button();
		return $obj->create($args, $args2, $args3);
	}
	
	function createBreak($args) {
		//$this->debug($args);
		
		$classes = array();
		if(!empty($args)) {
			foreach($args AS $v) {
				$classes[] = 'br-'.$v;
			}
		}
		
		if(!empty($classes)) {
			$return = '<br class="'.implode(" ", $classes).'">';
			
		} else {
			$return = '<br>';
		}
		
		return $return;
	}
	
	function includeCalendlyScripts() {
		$this->inline_scripts_include_end['calendly'] = '
			<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
			<script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript" async></script>
		';
	}
	
	function createCalendlyButton($args, $args2="", $args3="") {
		$this->includeCalendlyScripts();
		
		// $this->debug($args);
		// $this->debug($args2);
		// $this->debug($args3);
		
		$class = '';
		if($args3 == "calendly_button") {
			$url = urldecode((string) $args['url']);
			$title = urldecode((string) $args['title']);
			
			if(isset($args['class'])) {
				$class = urldecode((string) $args['class']);
			}
			
		} else {
			$url = $args['url'];
			$title = $args['title'];
			
			if(isset($args['class'])) {
				$class = $args['class'];
			}
		}
		
		$return = '
			<div onclick="Calendly.initPopupWidget({url: \''.$url.'\'});return false;" class="'.$class.'">'.$title.'</div>
		';
		
		return $return;
	}
	
	function createCaptcha($args) {
		$obj = new individole_create_captcha();
		return $obj->create($args);
	}
	
	function createChart($args) {
		$obj = new individole_create_chart();
		return $obj->create($args);
	}
	
	function createCode($code) {
		return implode(" ", $code);
	}
	
	function createCurve($args) {
		(isset($args['position']) ? $position = $args['position'] : $position = 'right');
		
		$curve_top = '<svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 500" style="enable-background:new 0 0 250 500;" xml:space="preserve"><path d="M0,0h250v250C250,111.92883,138.07117,0,0,0z M0,500h250V250C250,388.07117,138.07117,500,0,500z"/></svg>';
		
		$curve_right = '<svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 500" style="enable-background:new 0 0 250 500;" xml:space="preserve"><path d="M0,0h250v250C250,111.92883,138.07117,0,0,0z M0,500h250V250C250,388.07117,138.07117,500,0,500z"/></svg>';
		
		$curve_bottom = '<svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 500" style="enable-background:new 0 0 250 500;" xml:space="preserve"><path d="M0,0h250v250C250,111.92883,138.07117,0,0,0z M0,500h250V250C250,388.07117,138.07117,500,0,500z"/></svg>';
		
		$curve_left = '<svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 500" style="enable-background:new 0 0 250 500;" xml:space="preserve"><path d="M0,0h250v250C250,111.92883,138.07117,0,0,0z M0,500h250V250C250,388.07117,138.07117,500,0,500z"/></svg>';
		
		$return = '
			<div class="curve">'.${'curve_'.$position}.'</div>
		';
		
		return $return;
	}
	
	function createCurveTop() {
		return $this->createCurve(array(
			"position" 	=> "top",
		));
	}
	
	function createCurveRight() {
		return $this->createCurve(array(
			"position" 	=> "right",
		));
	}
	
	function createCurveBottom() {
		return $this->createCurve(array(
			"position" 	=> "bottom",
		));
	}
	
	function createCurveLeft() {
		return $this->createCurve(array(
			"position" 	=> "left",
		));
	}
	
	function createDHLShippingRates() {
		$obj = new individole_create_dhl_shipping_rates();
		return $obj->create();
	}
	
	function createDirectCode($code) {
		$this->debug($code);
		
		return $code;
	}
	
	function createCookieHint($args) {
		$obj = new individole_create_cookie_hint($args);
		return $obj->create();
	}
	
	function createCopyright() {
		return '&copy; '.$this->getCurrentYear();
	}
	
	function createCustomColumn($column) {
		$obj = new individole_create_custom_column($column);
		return $obj->create();
	}
	
	function createCustomColumnCSS() {
		echo '<style>'.implode("", $this->the_list_columns_css).'</style>';
	}
	
	function createCustomPostTypes() {
		$obj = new individole_create_custom_post_types();
		return $obj->create();
	}
	
	function createDate() {
		$return = $this->formatDate(array(
			'date'		=> date('Y-m-d'),
		));
		
		return $return;
	}
	
	function createDateTime() {
		$return = $this->formatDate(array(
			'format'	=> '%d.%m.%Y %H:%M:%S',
			'date'		=> date('Y-m-d H:i:s'),
		));
		
		return $return;
	}
	
	function createDraftClass($post_status) {
		if($post_status != "publish" && $this->isAdmin()) {
			return ' individole_post_status_'.$post_status;
		}
	}
	
	function createDraftLabel($post_status, $args=array()) {
		if($post_status != "publish" && $this->isAdmin()) {
			return $this->getPostStatus($post_status, $args);
		}
	}
	
	function createDraftSymbol($post_status="", $args=array()){
		if($post_status != "publish" && $post_status != "" && $this->isAdmin()) {
			// $title = '<i class="fas fa-minus-circle"></i>';
			// $title = 'DRAFT';
			
			$return = '<sup class="sup_draft"><span>'.$post_status.'</span></sup>';
		
			return $return;
		}
	}
	
	function createExcerpt($text, $args=array()) {
		$obj = new individole_create_excerpt();
		return $obj->create($text, $args);
	}
	
	function createFavicon() {
		if(is_writable(ABSPATH) && (!file_exists(ABSPATH.'favicon.ico') || isset($_POST['create_favicon']))) {
			$favicon_rel = get_stylesheet_directory().'/_images/_favicons/favicon.ico';
			$favicon_abs = get_stylesheet_directory_uri().'/_images/_favicons/favicon.ico';
			
			if(file_exists($favicon_rel)) {
				copy($favicon_rel, ABSPATH.'favicon.ico');
			}
		}
	}
	
	function createUserINI() {
		if(is_writable(ABSPATH) && isset($_POST['create_user_ini'])) {
			$file_source = $this->path['individole'].'/_files/.user.ini';
			$file_target = ABSPATH.'.user.ini';
			copy($file_source, $file_target);
			chmod($file_target, 0744);
		}
	}
	
	function createFilterButtons($args) {
		$obj = new individole_create_filter_buttons();
		return $obj->create($args);
	}
	
	function createFontAwesome($type, $args) {
		$obj = new individole_create_fontawesome();
		return $obj->create($type, $args);
	}
	
	function createFontAwesomeBrands($args) {
		return $this->createFontAwesome("b", $args);
	}
	
	function createFontAwesomeLight($args) {
		return $this->createFontAwesome("l", $args);
	}
	
	function createFontAwesomeRegular($args) {
		return $this->createFontAwesome("r", $args);
	}
	
	function createFontAwesomeSolid($args) {
		return $this->createFontAwesome("s", $args);
	}
	
	function createFontAwesomeDuotone($args) {
		return $this->createFontAwesome("d", $args);
	}
	
	function createFontAwesomeThin($args) {
		return $this->createFontAwesome("t", $args);
	}
	
	function createFooterJS($args=array()) {
		$obj = new individole_create_footer_js();
		return $obj->create($args);
	}
	
	function createFrontendEdit($args) {
		$obj = new individole_frontend_edits($args);
		return $obj->create();
	}
	
	function createFullscreenIFrame($args) {
		global $post;
		
		$url = get_field("iframe_url", $post->ID);
		//$individole->debug($url);
		
		$parse = parse_url($_SERVER['REQUEST_URI']);
		//$individole->debug($parse);
		
		$param = array();
		$param[] = 'src="'.$url.'"';
		
		if(isset($args['id'])) {
			$param[] = 'id="'.$args['id'].'"';
		}
		
		$return = '<iframe '.implode(" ", $param).'></iframe>';
		
		return $return;
	}
	
	function createGAOptOut($args, $args2, $args3) {
		return '<a href="javascript:gaOptout();">'.$args2.'</a>';
	}
	
	function createGap($args) {
		if(isset($args['single_value']) && is_numeric($args['single_value'])) {
			return $this->clearer($args['single_value'], "", $args);
		
		} else if(isset($args[0]) && is_numeric($args[0])) {
			return $this->clearer($args[0], "", $args);
		}
	}
	
	function createGettyEmbed($args) {
		$obj = new individole_create_getty_embed($args);
		return $obj->create();
	}
	
	function createGettyImagesFromEmbedKeys($string) {
		preg_match_all("%(id|sig|,w|,h|,items|,tld):'([^']+)'%i", $string, $results);
		
		// $this->debug('<textarea>'.$string.'</textarea>');
		// $this->debug($results);
		
		$keys = array();
		if(isset($results[1]) && is_array($results[1]) && sizeof($results[1]) == 6) {
			$i = 0;
			foreach($results[1] AS $k) {
				$k 			= trim((string) $k, ",");
				$keys[$k] 	= rtrim((string) $results[2][$i], "px");
				
				++$i;
			}
			
		} else {
			preg_match_all("%(width|height)=\"([^\"]+)%i", $string, $results_sizes);
			preg_match_all("%embed/([^\?]+)%i", $string, $results_items);
			preg_match_all("%sig=([^\"&]+)%i", $string, $results_sig);
			preg_match_all("%et=([^\"&]+)%i", $string, $results_id);
			
			//$this->debug($results_items);
			//$this->debug($results_id);
			//$this->debug($results_sig);
			//$this->debug($results_sizes);
			
			if(isset($results_items[1][0]) && isset($results_id[1][0]) && isset($results_sig[1][0]) && isset($results_sizes[2][0]) && isset($results_sizes[2][1])) {
				$keys = array(
					'id'		=> $results_id[1][0],
					'sig'		=> $results_sig[1][0],
					'w'		=> $results_sizes[2][0],
					'h'		=> $results_sizes[2][1],
					'items'	=> $results_items[1][0],
				);
			}
			
			// $this->debug($keys);
		}
				
		return $keys;
	}
	
	function createGettyImagesFromEmbed($string) {
		$keys = $this->createGettyImagesFromEmbedKeys($string);
		
		// $this->debug($keys);
		
		if(isset($keys['items'])) {
			$curl_url = 'https://embed.gettyimages.com/oembed?url=http%3a%2f%2fgty.im%2f'.$keys['items'].'&caller='.$_SERVER['HTTP_HOST'];
			//$this->debug($curl_url);
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $curl_url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_TIMEOUT, 20);
			$result = curl_exec($ch);
			curl_close($ch);
			
			$data = json_decode($result, true);
			// $this->debug($data);
			
			if($data && isset($data['html'])) {
				$keys = $this->createGettyImagesFromEmbedKeys($data['html']);
			
				if(isset($keys['id'])) {
					$ratio = ($keys['h'] * 100) / $keys['w'];
					
					$url = '//embed.gettyimages.com/embed/'.$keys['items'].'?et='.$keys['id'].'&tld='.$keys['tld'].'&sig='.$keys['sig'].'&caption=true&ver=2';
					
					$src_type = 'src';
					$class = '';
					if($this->io("library_lazyload") == true) {
						$src_type = 'data-src';
						// $src_type = 'loading="lazy" src';
						$class = 'lazy lazyload';
					}
					
					$return = '
						<div class="embed-container" style="padding-bottom:'.$ratio.'%;">
							<iframe '.$src_type.'="'.$url.'" class="'.$class.'" width="100%" scrolling="no" frameborder="no" width="594px" height="389px"></iframe>
						</div>
					';
				
					return $return;
				
				} else {
					return false;
				}
				
			} else {
				return false;
			}
			
		} else {
			return false;
		}
	}
	
	function createGoogleFontKey($file_key) {
		$file_key = basename(dirname($file_key, 1)).'/'.basename($file_key);
		return $file_key;
	}
	
	function createGoogleFonts($args) {
		$obj = new individole_create_google_fonts();
		return $obj->create($args);
	}
	
	function createBorlabsSettingsButton($args, $args2="", $args3="") {
		$obj = new individole_create_borlabs_settings_button();
		return $obj->create($args, $args2, $args3);
	}
	
	function createGoogleMap($args) {
		$obj = new individole_create_google_map($args);
		return $obj->create();
	}
	
	function createGoogleMapIFrame($iframe) {
		$code = str_replace("%20", " ", $iframe[0]);
		
		$return = '
			<div class="map_container">'.$code.'</div>
		';
		
		return $return;
	}
	
	function createGrid($args) {
		$obj = new individole_create_grid();
		return $obj->create($args);
	}
	
	function createHamburger($args="") {
		$data 	= "";
		$title 	= "";
		
		$content = '';
		$line = '';
		
		if(is_array($args)) {
			if(isset($args['data'])) {
				$data = $args['data'];
			}
			
			if(isset($args['content'])) {
				$content = $args['content'];
			}
			
			if(isset($args['line'])) {
				$line = $args['line'];
			}
			
			if(isset($args['title'])) {
				$title = '<span class="title">'.$args['title'].'</span>';
				
			} else {
				$title = '';
			}
			
		} else if($args != "") {
			$title = '<span class="title">'.$args.'</span>';
		}
		
		if($content == "") {
			$content = '
				<div class="hamburger_line hamburger_line_1">'.$line.'</div>
				<div class="hamburger_line hamburger_line_2a">'.$line.'</div>
				<div class="hamburger_line hamburger_line_2b">'.$line.'</div>
				<div class="hamburger_line hamburger_line_3">'.$line.'</div>
			';
		}
	
		$return = '
			<div class="hamburger noprint" '.$data.'>
				'.$content.'
				'.$title.'
			</div>
		';
		
		return $return;
	}
	
	function createIFrame($args, $args2="", $args3="") {
		$attributes = array();
		
		if(isset($args['source_values'])) {
			$attributes['src'] = urldecode((string) $args['source_values']);
		
		} else {
			if(!empty($args)) {
				foreach($args AS $k => $v) {
					if($k != "source_values") {
						$attributes[$k] = trim((string) $k).'="'.trim((string) $v, '"').'"';
					}
				}
			}
		}
		
		if(isset($attributes['src']) || isset($attributes['data-src'])) {
			$return = '<iframe '.implode(" ", $attributes).'></iframe>';
			
			return $return;
		}
	}
	
	function createIndividoleForm($args) {
		$obj = new individole_forms_create_form($args);
		return $obj->create();
	}
	
	function createIndividolePostObjectFilter($post_types) {
		$obj = new individole_create_individole_post_object_filter();
		return $obj->create($post_types);
	}
	
	function createIndividolePostObjectTitle($p, $args) {
		$obj = new individole_create_individole_post_object_title();
		return $obj->create($p, $args);
	}
	
	function createIndividoleToolFilter($headline, $content) {
		if(is_array($content)) {
			$content = '
				<div class="buttons">'.implode("", $content).'</div>
			';
		}
		
		$return = '
			<h2>'.$headline.'</h2>
			'.$content.'
		';
		
		return $return;
	}
	
	function createIndividoleToolFilterLanguages() {
		if($this->isWPML()) {
			$languages = $this->wpmlGetAllLanguages();
			// $this->debug($languages);
			
			if(sizeof($languages) > $this->language_toggle) {
				$buttons = array();
				foreach($languages AS $language) {
					$classes = array();
					$classes[] = 'button';
					$classes[] = 'button_small';
					$classes[] = 'button_inline';
					$classes[] = 'active';
					
					if($language['code'] == $this->l_default) {
						$classes[] = 'force_active';
					}
					
					$buttons[] = '
						<div class="'.implode(" ", $classes).'" data-wpml_toggle="'.$language['code'].'">'.$this->createAdminFlag($language['code'], "", "margin:0px !important;").'</div>
					';
				}
				
				$return = $this->createIndividoleToolFilter('Languages', '<div class="buttons">'.implode("", $buttons).'</div>');
				
				return $return;
			}
		}
	}
	
	function createIndividoleToolBtnSave($args=array()) {
		$return = '
			<div class="individole_tool_btn_save">
				<button class="button button_save button_large"><i class="fas fa-save"></i>Save values</button>
			</div>
		';
		
		return $return;
	}
	
	function createInputWrap($args) {
		$obj = new individole_create_input_wrap();
		return $obj->create($args);
	}
	
	function createInstagramItem($i, $args=array()) {
		$class = array();
		
		if(isset($args['bootstrap'])) {
			$class[] = $args['bootstrap'];
		}
		
		$class[] = 'item';
		$class[] = 'item_instagram';
		
		$return = '
			<div class="'.implode(" ", $class).'" data-instagram_item="'.$i.'"><i class="fas fa-spinner fa-spin" class="loading"></i></div>
		';
		
		return $return;
	}
	
	function createInstagramConnect() {
		$obj = new individole_create_instagram_connect();
		return $obj->create();
	}
	
	function createInstagramToken() {
		$obj = new individole_create_instagram_token();
		return $obj->create();
	}
	
	function createIndividoleSlider($args) {
		$obj = new individole_create_slider($args);
		return $obj->create();
	}
	
	function createJVectorMap($args) {
		$obj = new individole_create_jvector_map();
		return $obj->create($args);
	}
	
	function createLanguageNamesByISO($iso="", $args=array()) {
		$obj = new individole_create_language_names_by_iso();
		return $obj->create($iso, $args);
	}
	
	function createCountryNamesByISO($iso="", $args=array()) {
		$obj = new individole_create_country_names_by_iso();
		return $obj->create($iso, $args);
	}
	
	function createLink($args, $args2="", $args3="") {
		$obj = new individole_create_link();
		return $obj->create($args, $args2, $args3);
	}
	
	function createLinkList($links, $wrapper="") {
		$obj = new individole_create_linklist();
		return $obj->create($links, $wrapper);
	}
	
	function createLinksByIDs($ids, $args=array()) {
		$obj = new individole_create_links_by_ids();
		return $obj->create($ids, $args);
	}
	
	function createTitlesByIDs($ids, $args=array()) {
		$args['titles_only'] = 1;
		
		$obj = new individole_create_links_by_ids();
		return $obj->create($ids, $args);
	}
	
	function createLoremIpsum($args, $args2="", $args3="") {
		$obj = new individole_create_lorem_ipsum();
		return $obj->create($args, $args2, $args3);
	}
	
	function createModelViewer($args) {
		$obj = new individole_create_modelviewer();
		return $obj->create($args);
	}
	
	function createModuleWrapper($view, $content) {
		$return = '
			<div class="mo mv_'.$view.'">
				<div class="module">
					<div class="co co_'.$view.'">
						'.$content.'
					</div>
				</div>
			</div>
		';
		
		return $return;
	}
	
	function createPinterestConnect() {
		$obj = new individole_create_pinterest_connect();
		return $obj->create();
	}
	
	function createPinterestToken() {
		$obj = new individole_create_pinterest_token();
		return $obj->create();
	}
	
	function createMailBody($args=array()) {
		$obj = new individole_mail_create_body();
		return $obj->create($args);
	}
	
	function createMailchimpCURL($args) {
		$obj = new individole_create_mailchimp_curl();
		return $obj->create($args);
	}
	
	function createMailchimpForm($args) {
		$obj = new individole_create_mailchimp($args);
		return $obj->create();
	}
	
	function createMainCSS($args=array()) {
		$obj = new individole_create_main_css();
		return $obj->create($args);
	}
	
	function createMasonry($args) {
		$obj = new individole_create_masonry($args);
		return $obj->create();
	}
	
	function createMenu($args) {
		$obj = new individole_menu($args);
		return $obj->create();
	}
	
	function createMenuCPT($args) {
		$obj = new individole_menu_cpt($args);
		return $obj->create($args);
	}
	
	function createSendInBlueCURL($args) {
		$obj = new individole_create_sendinblue_curl();
		return $obj->create($args);
	}
	
	function getChildrenIDs($post_id, $args=array()) {
		$obj = new individole_get_children_ids();
		return $obj->create($post_id, $args);
	}
	
	function createMenuItemColumnbreak($args=array()) {
		$classes = array();
		$classes[] = 'columnbreak';
		
		if(isset($args['media_query']) && $args['media_query'] != "") {
			$classes[] = 'mq_'.$args['media_query'];
		}
		
		if(isset($args['columns']) && $args['columns'] == 1) {
			return '</div><div>';
			
		} else {
			return '<li class="'.implode(" ", $classes).'">&nbsp;</li>';
		}
		
	}
	
	function createMenuItemDivider($args=array()) {
		$classes = array();
		$classes[] = 'divider';
		
		if(isset($args['media_query']) && $args['media_query'] != "") {
			$classes[] = 'mq_'.$args['media_query'];
		}
		
		if(isset($args['classes']) && $args['classes'] != "") {
			$classes[] = $args['classes'];
		}
		
		return '<li class="'.implode(" ", $classes).'">&nbsp;</li>';
	}
	
	function createMenuItemLinebreak($args=array()) {
		$classes = array();
		$classes[] = 'linebreak';
		
		if(isset($args['media_query']) && $args['media_query'] != "") {
			$classes[] = 'mq_'.$args['media_query'];
		}
		
		if(isset($args['classes']) && $args['classes'] != "") {
			$classes[] = $args['classes'];
		}
		
		return '<li class="'.implode(" ", $classes).'">&nbsp;</li>';
	}
	
	function createMenuItemDescription($args) {
		$this->debug($args);
		
		return 'test';
		
		// if(isset($args['extra']) && $args['extra'] != "") {
		// 	return $this->createModulePageElement($args['extra']);
		// }
	}
	
	function createMenuItemPageElement($args) {
		//$this->debug($args);
		
		if(isset($args['extra']) && $args['extra'] != "") {
			return $this->createModulePageElement($args['extra']);
		}
	}
		
	function createMenuItemsChilds($args) {
		$args['parent'] 	= $args['parent_id'];
		$args['cpt'] 		= get_post_type($args['parent_id']);
		
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createMenuItemsAddChilds($args) {
		$args['parent'] = 0;
		
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createBorlabsLink($title) {
		if($this->borlabs_version == 3) {
			return do_shortcode('[borlabs-cookie type="btn-consent-preferences" title="'.$title.'" element="link"/]');
			
		} else if($this->borlabs_version == 2) {
			return do_shortcode('[borlabs-cookie type="btn-cookie-preference" title="'.$title.'" element="link"/]');
		}
		
		return $title;
	}
	
	function createMenuItemsCookieSettings($args, $args2) {
		$obj = new individole_menu_items_cookie_settings();
		return $obj->create($args, $args2);
	}
	
	function createMenuItemsMenu($args) {
		// $this->debug($args);
		
		if(isset($args['extra']) && $args['extra'] != "") {
			$args['menu'] = $args['extra'];
		}
		
		if(isset($args['menu']) && $args['menu'] != "") {
			$args['menu'] = str_replace("{language}", $this->l, $args['menu']);
			
			$obj = new individole_menu($args);
			$menu = $obj->create();
			
			if(isset($menu['menu'])) {
				return $menu['menu'];
			}
		}
	}
	
	function createMenuItemsCPT($args) {
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createMenuItemsCPTAnchorsLevel0($args) {
		$args['parent'] = 0;
		$args['anchor'] = 1;
		
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createMenuItemsCPTLevel0($args) {
		$args['parent'] = 0;
		
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createMenuItemsCPTLevel1($args) {
		$args['hide_parent'] = 1;
		
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createMenuItemsCPTWithOverview($args) {
		$args['prepend_overview'] = 1;
		
		$obj = new individole_menu_items_cpt($args);
		return $obj->create();
	}
	
	function createMenuItemsParentAnchors($args) {
		$obj = new individole_menu_items_parent_anchors();
		return $obj->create($args);
	}
	
	function createMenuItemsTaxonomy($args) {
		$obj = new individole_menu_items_taxonomy();
		return $obj->create($args);
	}
	
	function metaboxes() {
		$obj = new individole_metabox_setup();
		return $obj->create();
	}
		
	function metaboxDuplicatePMContent() {
		$obj = new individole_metabox_duplicate_pm_content();
		return $obj->create();
	}
	
	function metaboxRegenerateThumbnails($p) {
		echo '<a class="button-secondary" href="/wp-admin/tools.php?page=regenerate-thumbnails#/regenerate/'.$p->ID.'" class="button">Regenerate Thumbnails</a>';
	}
	
	function metaboxShortcodeGlossar($p) {
		$obj = new individole_metabox_shortcode_glossar();
		return $obj->create();
	}
	
	function metaboxSocialPostsContent() {
		$obj = new individole_metabox_social_posts_content();
		return $obj->create();
	}
	
	function createMinifiedFile($args) {
		$obj = new individole_create_minified_file();
		return $obj->create($args);
	}
	
	function createMinifyURL($args) {
		$obj = new individole_create_minify_url($args);
		return $obj->create();
	}
	
	function createModules($args) {
		$obj = new individole_modules__modules();
		return $obj->create($args);
	}
	
	function createModule($args) {
		$obj = new individole_modules__module();
		return $obj->create($args);
	}
	
	function createModuleButton($args) {
		$obj = new individole_modules_button();
		return $obj->create($args);
	}
	
	function createModuleChart($args) {
		$obj = new individole_modules_chart();
		return $obj->create($args);
	}
	
	function createModuleColumns($args) {
		$obj = new individole_modules_columns();
		return $obj->create($args);
	}
	
	function createModuleEditorialMasonry($args) {
		$obj = new individole_modules_editorial_masonry();
		return $obj->create($args);
	}
	
	function createModuleEmbed($args) {
		$obj = new individole_modules_embed();
		return $obj->create($args);
	}
	
	function createModulePDFEmbed($args) {
		$obj = new individole_modules_pdf_embed();
		return $obj->create($args);
	}
	
	function createModuleFlex($args) {
		$obj = new individole_modules_flex();
		return $obj->create($args);
	}
	
	function createModuleGallery($args) {
		$obj = new individole_modules_gallery();
		return $obj->create($args);
	}
	
	function createModuleGaps($args=array()) {
		$obj = new individole_modules__gaps();
		return $obj->create($args);
	}
	
	function createModuleImage($args) {
		$obj = new individole_modules_image();
		return $obj->create($args);
	}
	
	function createModuleImageText($args) {
		$obj = new individole_modules_image_text();
		return $obj->create($args);
	}
	
	function createModuleLine($args) {
		$obj = new individole_modules_line();
		return $obj->create($args);
	}
	
	function createModuleMailchimp($args) {
		$obj = new individole_create_mailchimp($args);
		return $obj->create();
	}
	
	function createModuleSendinblue($args) {
		$obj = new individole_create_sendinblue($args);
		return $obj->create();
	}
	
	function createModuleMap($args) {
		$obj = new individole_modules_map();
		return $obj->create($args);
	}
	
	function createModuleTable($args) {
		$obj = new individole_modules_table();
		return $obj->create($args);
	}
	
	function createModulePageElement($var, $return_id=false) {
		$obj = new individole_modules_page_element();
		return $obj->create($var, $return_id);
	}
	
	function createModulePlaceholder($args, $args2="", $args3="") {
		$obj = new individole_modules_placeholder();
		return $obj->create($args, $args2, $args3);
	}
	
	function createModuleShortcode($args) {
		$obj = new individole_modules_shortcode();
		return $obj->create($args);
	}
	
	function createModuleSnippet($var, $return_id=false) {
		return $this->createModulePageElement($var, $return_id);
	}
	
	function createSalt($len = 8) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#$%^&*()-=_+';
		$l = strlen((string) $chars) - 1;
		$str = '';
		for ($i = 0; $i<$len; ++$i) {
			$str .= $chars[rand(0, $l)];
		}
		return "$5$".$str."$";
	}
	
	function createShippingLabel($args=array()) {
		$obj = new individole_create_shipping_label();
		return $obj->create($args);
	}
	
	function createShippingLabelButton($args=array()) {
		$obj = new individole_create_shipping_label_button();
		return $obj->create($args);
	}
	
	function deleteShippingLabel() {
		$obj = new individole_shipping_label_creator();
		return $obj->deleteLabel();
	}
	
	function saveShippingLabel($args=array()) {
		$obj = new individole_shipping_label_creator();
		return $obj->saveLabel($args);
	}
	
	function createShoppingFeeds($args=array()) {
		$obj = new individole_create_shopping_feeds();
		return $obj->create($args);
	}
	
	function createSnippet($var) {
		return $this->createModulePageElement($var);
	}
	
	function createModuleParallax($args) {
		$obj = new individole_modules_parallax();
		return $obj->create($args);
	}
	
	function createModulePersonio($args) {
		$obj = new individole_modules_personio();
		return $obj->create($args);
	}
	
	function createModuleText($args) {
		$obj = new individole_modules_text();
		return $obj->create($args);
	}
	
	function createModuleVideo($args) {
		$obj = new individole_modules_video();
		return $obj->create($args);
	}
	
	function createModuleVideoHTML5($args) {
		$obj = new individole_modules_video_html5();
		return $obj->create($args);
	}
	
	function createMenuByShortcode($args) {
		if(!isset($args['id']) && isset($args[0])) {
			$args['id'] = $args[0];
		}
		
		if(isset($args['id'])) {
			$args2 = array(
				'menu' 		=> $args['id'],
				'class' 	=> (isset($args['class']) ? $args['class'] : ""),
			);
			
			$menu = $this->createMenu(array_merge($args, $args2));
			
			if(isset($menu['menu'])) {
				if(isset($args['ul'])) {
					return $menu['ul'];
					
				} else {
					return $menu['menu'];
				}
			}
		}
	}
	
	function createNavMenusMetaboxPages($x, $args) {
		$obj = new individole_create_nav_menus_metabox_pages($args);
		return $obj->create($x);
	}
	
	function createNBSP() {
		return '&nbsp;';
	}
	
	function createNoFollow($post_status="") {
		if($post_status == "" || $post_status == "publish") {
			return;
			
		} else {
			return ' rel="nofollow"';
		}
	}
	
	function createNowrap($args, $args2) {
		$return = '<span class="nowrap">'.$args2.'</span>';
		
		return $return;
	}
	
	function createOffline($id) {
		
	}
	
	function createOwl($args) {
		$obj = new individole_create_owl($args);
		return $obj->create();
	}
	
	function createOwlNav($id, $args=array()) {
		$obj = new individole_create_owl_nav();
		return $obj->create($id, $args);
	}
	
	function createPagination($args) {
		$obj = new individole_create_pagination($args);
		return $obj->create();
	}
	
	function createParollerData($args) {
		$obj = new individole_create_paroller_data();
		return $obj->create($args);
	}
	
	function createPagePasswordForm($args=array()) {
		return $this->createPasswordForm($args);
	}
	
	function createPHPFromArray($args) {
		$obj = new individole_create_php_from_array();
		return $obj->create($args);
	}
	
	function createPasswordForm($args=array()) {
		$obj = new individole_create_password_form();
		return $obj->create($args);
	}
	
	function createPlaceholder($args) {
		$obj = new individole_create_placeholder($args);
		return $obj->create();
	}
	
	function createPlusMinusNumber($args) {
		$obj = new individole_create_plus_minus_number();
		return $obj->create($args);
	}
	
	function createPostMetaIndividole($args) {
		//update_option("individole_createPostMetaIndividole_args", $args);
		//update_option("individole_createPostMetaIndividole_post", $_POST);
	}
	
	function createPostStatus($p) {
		$return = $this->createDraftSymbol($p->post_status);
		return $return;
	}
	
	function createPostTitle($p, $post_title="") {
		$return = '';
		
		if($p->post_status == "trash") {
			$return .= '<b style="color:red;">TRASH!!!</b> ';
		}
		
		if($post_title != "") {
			$return .= $post_title;
		
		} else {
			$return .= $p->post_title;
		}
		
		$return .= $this->createDraftSymbol($p->post_status);
		
		return $return;
	}
	
	function post_title($p, $post_title="") {
		return $this->createPostTitle($p, $post_title);
	}
	
	function post_status($p) {
		return $this->createPostStatus($p);
	}
	
	function createRadioColor($var) {
		$return = '<div class="individole_radio_color bg_color_'.$var.'"></div>';
		return $return;
	}
	
	function createRadioColorFull($var) {
		$return = '<div class="individole_radio_color_full bg_color_'.$var.'"></div>';
		return $return;
	}
	
	function createRepublishCode($post_id, $args) {
		$obj = new individole_create_republish_code();
		return $obj->create($post_id, $args);
	}
	
	function createResponsiveObject($args, $args2="", $args3="") {
		//$this->debug($args);
		//$this->debug($args2);
		//$this->debug($args3);
		
		if(!empty($args)) {
			$class = '';
			if($args3 == "phone-ls") {
				$class = 'phone_ls_show';
			
			} else if($args3 == "phone") {
				$class = 'phone_show';
			
			} else if($args3 == "tablet-ls") {
				$class = 'tablet_ls_show';
			
			} else if($args3 == "tablet") {
				$class = 'tablet_show';
			
			} else if($args3 == "-phone-ls") {
				$class = 'phone_ls_hide';
			
			} else if($args3 == "-phone") {
				$class = 'phone_hide';
			
			} else if($args3 == "-tablet-ls") {
				$class = 'tablet_ls_hide';
			
			} else if($args3 == "-tablet") {
				$class = 'tablet_hide';
			}
			
			if($class != "") {
				$return = '<span class="'.$class.'">'.implode(" ", $args).'</span>';
				
				return $return;
			}
		}
	}
	
	function createReUploadURL($attachment_id) {
		$url = admin_url( "upload.php?page=enable-media-replace/enable-media-replace.php&action=media_replace&attachment_id=" . $attachment_id);
		$action = "media_replace";
		$reupload_url = wp_nonce_url( $url, $action );
		
		return $reupload_url;
	}
	
	function createRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen((string) $characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	function createRobotsTXT() {
		$obj = new individole_create_robots_txt();
		return $obj->create();
	}
	
	function createRowgrid($args) {
		$obj = new individole_create_rowgrid();
		return $obj->create($args);
	}
	
	function createRSS() {
		$obj = new individole_create_rss();
		return $obj->create();
	}
	
	function createSatoshiPayButton($args=array()) {
		$obj = new individole_create_satoshipay_button();
		return $obj->create($args);
	}
	
	function createSavePostMessages() {
		if($this->io("show_purged_urls") == 1 && isset($_SESSION['purged_urls'])) {
			echo '<div id="message" class="updated notice notice-success is-dismissible">'.$_SESSION['purged_urls'].'<button type="button" class="notice-dismiss"></button></div>';
			
			
			
			unset($_SESSION['purged_urls']);
		}
		
	}
	
	function createSearchField($args) {
		$placeholder = 'Search for:';
		if($placeholder_value = $this->getOptionWord("search_placeholder")) {
			$placeholder = $placeholder_value;
		}
		
		$return = '
			<form action="/" id="searchform" method="get" data-ajax="false">
				<div class="input">
					<input type="text" class="search_input" id="s" name="s" value="" placeholder="'.$placeholder.'" autocomplete="off" data-1p-ignore />
				</div>
			</form>
		';
		
		return $return;
	}
	
	function createSendMail404Stats() {
		if(function_exists("mail") && $this->io("sendmail404_mail") != "") {
			$data = get_option("individole_404");
			
			if(!empty($data)) {
				$data = $this->sortArrayByKey($data, "count");
				
				$mail_data = array();
				foreach($data AS $url => $v) {
					$mail_data[] = '
					URL: '.$v['count'].'x --> '.$url;
				}
				
				$mail_404 = sizeof($mail_data).' URLs
				'.implode("", $mail_data);
				
				mail($this->io("sendmail404_mail"), "404: ".str_replace("www.", "", $_SERVER['HTTP_HOST']), $mail_404);
			}
		}
	}
	
	function createServiceWorker($force=false) {
		//$_SESSION['serviceworker'] = 1;
		
		if(isset($_SESSION['serviceworker'])) {
			$service_worker_file = ABSPATH.'service-worker.js';
			
			if(file_exists($service_worker_file)) {
				$return = '
					<script type="text/javascript">
						if("serviceWorker" in navigator) {
							window.addEventListener("load", function() {
								navigator.serviceWorker.register("/service-worker.js", {
									scope: "./"
								});
								
								navigator.serviceWorker.ready.then(function(registration) {
									clog("Service worker successfully registered on scope ", registration.scope);
								});
							});
							
							if(navigator.onLine == true) {
								
							} else {
								document.addEventListener("DOMContentLoaded", function(){
									var hide_offline = document.getElementsByClassName("hide_offline");
									while(hide_offline.length > 0){
										clog(hide_offline[0]);
										hide_offline[0].parentNode.removeChild(hide_offline[0]);
									}
								});
							}
							
						} else {
							clog("Service worker NOT supported");
						}
					</script>
				';
			
				$return = $this->minifyJS($return);
				
				return $return;
			}
		}
	}
	
	function createShareOptions($args) {
		$obj = new individole_create_share_options($args);
		return $obj->create();
	}
	
	function createShortcodeInstruction($key="", $text="") {
		if($key != "" && $text != "") {
			$this->shortcode_instructions[] = '<div id="shortcode_instruction_'.$key.'"><b>'.$key.':</b><br>'.$text.'</div>';
		}
	}
	
	function createSocialLinks($args, $args2="", $args3="") {
		$obj = new individole_create_share_options(array(
			'type' 		=> 'social_link',
			'args'		=> $args,
			'args2'		=> $args2,
			'args3'		=> $args3,
		));
		
		return $obj->create();
	}
	
	function createSocialLinks2($args, $args2="", $args3="") {
		$obj = new individole_create_share_options(array(
			'type' 		=> 'social_link',
			'args'		=> $args,
			'args2'		=> $args2,
			'args3'		=> 'social_links_2',
		));
		
		return $obj->create();
	}
	
	function createSitemapXML($post_id=0) {
		$obj = new individole_create_sitemap_xml();
		return $obj->create($post_id);
	}
	
	function createSoundcloud($args, $args2="", $args3="") {
		$obj = new individole_create_soundcloud();
		return $obj->create($args, $args2, $args3);
	}
	
	function createSpacer($args) {
		$obj = new individole_create_spacer($args);
		return $obj->create();
	}
	
	function createSrcSet($normal, $retina, $subdomain=false) {
		$obj = new individole_create_srcset();
		return $obj->create($normal, $retina, $subdomain);
	}
	
	function createStructuredData($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("", $args);
	}
	
	function createStructuredDataBook($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("Book", $args);
	}
	
	function createStructuredDataJob($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("JobPosting", $args);
	}
	
	function createStructuredDataOrganization($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("Organization", $args);
	}
	
	function createStructuredDataLocalBusiness($type="", $args=array()) {
		$obj = new individole_create_structured_data();
		return $obj->create($type, $args);
	}
	
	function createStructuredDataNewsArticle($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("NewsArticle", $args);
	}
	
	function createStructuredDataProduct($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("Product", $args);
	}
	
	function createStructuredDataRecipe($args) {
		$obj = new individole_create_structured_data();
		return $obj->create("Recipe", $args);
	}
	
	function createStyleguide($args, $args2="", $args3="") {
		$obj = new individole_create_styleguide();
		return $obj->create($args, $args2, $args3);
	}
	
	function createBackendIframeToolWrapper($content) {
		$obj = new individole_admin_create_iframe_tool_wrapper();
		return $obj->create($content);
	}
	
	function createDateRange($strDateFrom,$strDateTo) {
		$aryRange=array();
	
		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
	
		if ($iDateTo>=$iDateFrom)
		{
			array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
			while ($iDateFrom<$iDateTo)
			{
				$iDateFrom+=86400; // add 24 hours
				array_push($aryRange,date('Y-m-d',$iDateFrom));
			}
		}
		return array_unique($aryRange);
	}
	
	function createMonthsRange($strDateFrom,$strDateTo) {
		$aryRange=array();
	
		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
	
		if ($iDateTo>=$iDateFrom)
		{
			array_push($aryRange,date('Y-m',$iDateFrom)); // first entry
			while ($iDateFrom<$iDateTo)
			{
				$iDateFrom+=86400; // add 24 hours
				array_push($aryRange,date('Y-m',$iDateFrom));
			}
		}
		return array_unique($aryRange);
	}
	
	function createYearsRange($strDateFrom,$strDateTo) {
		$aryRange=array();
	
		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
	
		if ($iDateTo>=$iDateFrom)
		{
			array_push($aryRange,date('Y',$iDateFrom)); // first entry
			while ($iDateFrom<$iDateTo)
			{
				$iDateFrom+=86400; // add 24 hours
				array_push($aryRange,date('Y',$iDateFrom));
			}
		}
		return array_unique($aryRange);
	}
	
	function createOL($args="", $args2="", $args3="") {
		return $this->createULOL("ol", $args, $args2, $args3);
	}
	
	function createTable($args) {
		$obj = new individole_create_table();
		return $obj->create($args);
	}
	
	function createTableCountries() {
		$obj = new individole_create_table_countries();
		return $obj->create();
	}
	
	function createTagCloud($args) {
		$obj = new individole_create_tagcloud();
		return $obj->create($args);
	}
	
	function createTodo($args, $args2="", $args3="") {
		if($this->isAdmin() || $this->io("module_show_placeholders") == 1) {
			if(isset($args[0]) & !empty($args[0])) {
				$return = implode(" ", $args);
			
			} else {
				$return = "TODO";
			}
			
			if($args3 == "todo-p" || $args3 == "todop") {
				$todo_tag = 'p';
				
			} else {
				$todo_tag = 'span';
			}
			
			return '<'.$todo_tag.' class="individole_todo">'.$return.'</'.$todo_tag.'>';
		}
	}
	
	function createToolInput($args=array()) {
		$obj = new individole_create_tool_input();
		return $obj->create($args);
	}
	
	function createToolSettings($args) {
		$obj = new individole_create_tool_settings();
		return $obj->create($args);
	}
	
	function createUL($args="", $args2="", $args3="") {
		return $this->createULOL("ul", $args, $args2, $args3);
	}
	
	function createULOL($type="ul", $args="", $args2="", $args3="") {
		$return = '';
		
		//$this->debug($type);
		//$this->debug($args);
		//$this->debug($args2);
		//$this->debug($args3);
		
		$text = '';
		if(!empty($args)) {
			$text = trim((string) $args);
		
		} else if(!empty($args2)) {
			$text = trim((string) $args2);
		}
		
		//$this->debug('<textarea>'.$text.'</textarea>');
		
		if(!empty($text)) {
			$text = str_replace(array("<br>", "<br/>", "<br />"), "\n", $text);
			
			$li = trim((string) $text);
			$li = explode("\n", $li);
			
			$items = array();
			foreach($li AS $v) {
				if(!empty(trim((string) $v))) {
					//$this->debug($v);
					
					$v = trim((string) $v);
					$v = do_shortcode($v);
					
					$items[] = '<li>'.$v.'</li>';
				}
			}
			
			
			
			$return = '<'.$type.'>'.implode("", $items).'</'.$type.'>';
		}
		
		return $return;
	}
	
	function createVCard($raw_data) {
		$obj = new individole_create_vcard();
		return $obj->create($raw_data);
	}
	
	function createVectorMap($args) {
		$obj = new individole_create_vector_map();
		return $obj->create($args);
	}
	
	function createVideoEmbed($args, $args2="", $args3="") {
		$obj = new individole_create_video_embed();
		return $obj->create($args, $args2, $args3);
	}
	
	function createVideoIcon($args=array()) {
		$video_icon = '<i class="fas fa-play video_icon video_icon_play"></i>';
		if($this->io("module_gallery_icon_video")) {
			if($this->io("module_gallery_icon_video") != "") {
				$video_icon = '<i class="'.$this->io("module_gallery_icon_video").' video_icon video_icon_play"></i>';
			
			} else {
				$video_icon = '';
			}
		}
		
		return $video_icon;
	}
	
	function webAppGetSettings($args=array()) {
		global $post;
		
		$return = array(
			'title'			=> '',
			'fullscreen'	=> '',
			'background'	=> '000000',
			'add_to_home'	=> $this->io("mobile_device_web_app_to_home_popup"),
		);
		
		if($post && !isset($args['manifest'])) {
			$return['title'] = trim(get_post_meta($post->ID, $this->io("praefix_seo").'_0_web_app_title', true));
		}
		
		if(empty($return['title'])) {
			$return['title'] = $this->io("apple_mobile_web_app_title");
		}
		
		if(empty($return['title'])) {
			$return['title'] = $_SERVER['HTTP_HOST'];
		}
		
		if($post) {
			$return['fullscreen'] = trim(get_post_meta($post->ID, $this->io("praefix_seo").'_0_web_app_fullscreen', true));
		}
		
		if(empty($return['fullscreen']) || $return['fullscreen'] == 0) {
			$return['fullscreen'] = $this->io("mobile_device_web_app_fullscreen");
			$return['background'] = $this->io("mobile_device_web_app_background");
			
		} else {
			if($post) {
				$return['background'] = trim(get_post_meta($post->ID, $this->io("praefix_seo").'_0_web_app_background', true));
			}
		}
		
		return $return;
	}
	
	function createWebAppManifest() {
		$manifest = ABSPATH.'manifest.json';
		
		if(isset($_POST["individole_mobile_device_web_app_to_home_popup"])) {
			$web_app = $this->webAppGetSettings(array());
			
			$txt = '{
			"short_name": "'.$web_app['title'].'",
			"name": "'.$web_app['title'].'",
			"description": "'.$this->getMetaDescription().'",
			"icons": [
				{
					"src": "images/icon.png",
					"type": "image/png",
					"sizes": "192x192"
				}
			],
			"start_url": "'.$this->wpmlGetHomeURL().'",
			"background_color": "'.$web_app['background'].'",
			"theme_color": "'.$web_app['background'].'",
			"display": "standalone"
			}';
			
			$file = fopen($manifest, 'w');
			fwrite($file, $txt);
			fclose($file);
			
		} else {
			if(file_exists($manifest)) {
				unlink($manifest);
			}
		}
	}
	
	function createWordIndex($args=array()) {
		$obj = new individole_wordindex();
		return $obj->create($args);
	}
	
	function createWordingValue($args) {
		// $this->debug('createWordingValue');
		// $this->debug($args, 1);
		
		if(isset($args[0]) && is_numeric($args[0]) && isset($args[1]) && isset($args[2])) {
			if($args[0] == 0 || $args[0] > 1) {
				return $args[2];
				
			} else {
				return $args[1];
			}
			
		} else {
			if(isset($args[1])) {
				return $args[1];
			}
			
			return;
		}
	}
	
	function createWPNonce($args) {
		//$this->debug($args);
		
		$url = '';
		if(is_array($args) && isset($args[0])) {
			$url = $args[0];
		
		} else if(is_string($args)) {
			$url = $args;
		}
		
		if($url != "") {
			$url_parse = parse_str((string) $url, $params);
			//$this->debug($params);
			
			$action = '';
			if(isset($params['action'])) {
				$action = $params['action'];
			}
			
			return wp_nonce_url($url, $action);
		}
	}
	
	function cronAddSchedules($schedules) {
		$obj = new individole_cron_schedules();
		return $obj->create($schedules);
	}
	
	function cronInit() {
		$obj = new individole_cron_events();
		$obj->create();
	}
	
	function curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function wp_footer() {
		echo $this->setFooterJavascript();
		echo '<wp_footer>';
		echo implode("", $this->wp_footer_content);
		wp_footer();
		echo '</wp_footer></body></html>';
	}
	
	function has_wp_footer() {
		$check_wp_footer_file = get_stylesheet_directory().'/page.php';
		$check_wp_footer = file_get_contents($check_wp_footer_file);
		// $this->debug($check_wp_footer_file);
		// $this->debug($check_wp_footer);
		
		if($this->containsWith($check_wp_footer, '$individole->wp_footer();')) {
			return true;
		}
	}
	
	function debug($values="", $echo = 0) {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
		
		} else if($this->io("show_debug") == 1 && ($echo >= 2 || ($this->isDennis() && !isset($_POST['action'])))) {
			ob_start();
			if(is_string($values) && $echo == 3) {
				$values = '<textarea style="width:100%; height: 150px;">'.$values.'</textarea>';
			}
			(is_string($values)) ? $values = array('STRING' => $values) : "";
			new dBug($values);
			$debug = ob_get_contents();
			ob_end_clean();
		
			$bt = debug_backtrace();
		
			if(isset($bt[1])) {
				$bt = $bt[1];
		
				$bt_line = '???';
				if(isset($bt['line'])) {
					$bt_line = $bt['line'];
				}
		
				(isset($bt['class'])) 			? $bt_class = $bt['class'] 						: $bt_class = '';
				(isset($bt['function'])) 			? $bt_function = $bt['function'] 						: $bt_function = '';
		
				$backtrace_output = array();
				$backtrace_output[] = 'CALL: '.str_replace($this->path['base'], "", @$bt['file']).' on line '.$bt_line;
				$backtrace_output[] = 'CLASS: "'.$bt_class.'"';
				$backtrace_output[] = 'FUNCTION: "'.$bt_function.'"';
		
				if($echo == 2 || $echo == 1 || (is_admin())) {
					echo '<div class="debug_direct noprint">'.$debug.'</div>';
		
				} else {
					$this->debug_final[] = '
						<div class="debug noprint"><table cellspacing="2" cellpadding="3" class="dBug_array"><tbody><tr><td class="dBug_arrayHeader" colspan="2" onclick="dBug_toggleTable(this)">'.implode("<br>", $backtrace_output).'</td></tr></tbody></table></div>
						<div class="debug noprint">'.$debug.'</div>
					';
				}
			}
		}
	}
	
	function decodeArray($array) {
		$out_array = array();
		foreach($array as $key => $value){
			$out_array[$key] = rawurldecode((string) $value);
		}
		
		return $out_array;
	}
	
	function deeplCreateButton($source, $target, $language) {
		if($source != $target) {
			$return = '
				<div class="button button_single" data-deepl_btn_source="'.$source.'" data-deepl_btn_target="'.$target.'" data-deepl_language="'.$language.'">Translate with DeepL</div>
			';
			
			return $return;
		}
	}
	
	function deleteAPC() {
		if($this->isAPC()) {
			apc_clear_cache();
						
		} else if($this->isAPCU()) {
			apcu_clear_cache();
		}
		
		if(function_exists("opcache_reset")) {
			opcache_reset();
		}
		
		$object_cache_file = ABSPATH.'wp-content/object-cache.php';
		if(file_exists($object_cache_file)) {
			// $this->debug($object_cache_file, 2);
			unlink($object_cache_file);
			
			$object_cache_folder = ABSPATH.'wp-content/cache/object';
			$this->deleteFolder($object_cache_folder);
		}
	}
	
	function deleteAPCDataACFOptions($post_type) {
		if($this->startsWith($post_type, "options")) {
			$this->deleteAPCData("acf_options");
		}
	}
	
	function deleteFolder($dir) {
		if(is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if($object != "." && $object != "..") {
					if(is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object)) {
						$this->deleteFolder($dir. DIRECTORY_SEPARATOR .$object);
					
					} else {
						unlink($dir. DIRECTORY_SEPARATOR .$object);
					}
				}
			}
			
			rmdir($dir);
		}
	}
	 
	function deleteAPCData($apc_key="", $debug=0) {
		global $wpdb;
		
		if($apc_key == "all") {
			$this->translations = array();
			
			$sql = 'DELETE FROM `'.TABLE_PREFIX.'options` WHERE `option_name` LIKE "_transient_%";';
			$wpdb->query($sql);
			$sql = 'DELETE FROM `'.TABLE_PREFIX.'options` WHERE `option_name` LIKE "_site_transient_%";';
			$wpdb->query($sql);
			
			//$sql = 'DELETE FROM `'.TABLE_PREFIX.'individole_transients` WHERE 1';
			//$wpdb->query($sql);
			
			$this->deleteAPC();
			
			//flush_rewrite_rules();
			
		} else if($apc_key != "") {
			//$apc_key = $this->formatAPCKey($apc_key);
			
			update_option("individole_deleteAPCData", 'apc_key!="":'.$apc_key);
			
			if($this->isAPC()) {
				update_option("individole_deleteAPCData", 'apc_delete:'.$apc_key);
				apc_delete($apc_key);
			}
			
			if($this->isAPCU()) {
				update_option("individole_deleteAPCData", 'apcu_delete:'.$apc_key);
				apcu_delete($apc_key);
			}
			
			$sql = 'DELETE FROM `'.TABLE_PREFIX.'options` WHERE `option_name` LIKE "_transient_%'.$apc_key.'";';
			$wpdb->query($sql);
			$sql = 'DELETE FROM `'.TABLE_PREFIX.'options` WHERE `option_name` LIKE "_site_transient_%'.$apc_key.'";';
			$wpdb->query($sql);
			
			//$this->update_option("deleteAPCDataPrefix", $key);
			
			//delete_transient($apc_key);
		}
	}
	
	function deleteAPCDataPrefix($prefix="") {
		global $wpdb;
		
		if(isset($_POST['delete_transients_prefix'])) {
			$prefix 	= $_POST['delete_transients_prefix'];
		}
		
		if($prefix != "") {
			$prefix		= $this->formatAPCKey($prefix);
			
			$sql		=  'SELECT `option_name` FROM '.TABLE_PREFIX.'options WHERE `option_name` LIKE "%_transient_'.$prefix.'%"';
			$keys		= $wpdb->get_results( $sql, ARRAY_A );
			
			$this->update_option("deleteAPCDataPrefix", $wpdb->last_query);
			
			if(!is_wp_error($keys)) {
				if(!empty($keys)) {
					foreach($keys AS $key) {
						$key = str_replace(array(
							"_transient_".DB_NAME.":A:",
							"_transient_".DB_NAME.":"
						), "", $key['option_name']);
						
						//$this->debug($key, 2);
						
						$this->update_option("deleteAPCDataPrefix", $key);
						$this->deleteAPCData($key);
					}
				}
			}
		}
	}
	
	function deleteAPCDataAll() {
		$this->deleteAPCData("all");
	}
	 
	function deregisterAdminScripts() {
		wp_deregister_script('wp-embed');
	}
	
	function deletePrintFiles() {
		$files = glob($this->path['base_images'].'/individole_print/*');
		foreach($files as $file){ // iterate files
			if(is_file($file)) {
				unlink($file);
			}
		}
	}
	
	function disableAutoResponsive() {
		return 1;
	}
	
	function disableHTTPAPICalls($ret, array $request, string $url) {
		if (\preg_match('!^https?://api\.wordpress\.org/core/browse-happy/!i', $url) || \preg_match('!^https?://api\.wordpress\.org/core/serve-happy/!i', $url)) {
			return new \WP_Error('http_request_failed', \sprintf('Request to %s is not allowed.', $url));
		}
	
		return $ret;
	}
	
	function disableCaptionInsert() {
	 	return true;
	}
	
	function disableCreationOfImageSizes($sizes) {
		return array();
	}
	
	function disableEmbedsCodeInit() {
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		add_filter( 'embed_oembed_discover', '__return_false' );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		add_filter( 'tiny_mce_plugins', array($this, 'disableEmbedsTinyMCEPlugin' ));
		remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
	}

	function disableEmbedsTinyMCEPlugin($plugins) {
		return array_diff($plugins, array('wpembed'));
	}

	function disableEmbedsRewrites($rules) {
		foreach($rules as $rule => $rewrite) {
			if(false !== strpos($rewrite, 'embed=true')) {
				unset($rules[$rule]);
			}
		}
		return $rules;
	}
	
	function disableHeartbeat() {
		//wp_deregister_script('heartbeat');
	}
	
	function disableRestEndpoints($endpoints) {
		if(isset($endpoints['/wp/v2/users'])) {
			unset($endpoints['/wp/v2/users']);
		}
		
		if(isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
			unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
		}
		
		return $endpoints;
	}
	
	function disableRealMimeCheck( $data, $file, $filename, $mimes ) {
		$wp_filetype = wp_check_filetype( $filename, $mimes );

		$ext = $wp_filetype['ext'];
		$type = $wp_filetype['type'];
		$proper_filename = $data['proper_filename'];

		return compact( 'ext', 'type', 'proper_filename' );
	}
	
	function doDecrypt($string, $force_encrypt_7=0) {
		if(trim((string) $string) != "") {
			$return = $string;
			
			if(!function_exists("mcrypt_encrypt") || defined("ENCRYPT_7") || $force_encrypt_7 == 1) {
				//$this->debug("doDecrypt 1");
				
				$c = base64_decode((string) $string);
				$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
				$iv = substr($c, 0, $ivlen);
				$hmac = substr($c, $ivlen, $sha2len=32);
				$ciphertext_raw = substr($c, $ivlen+$sha2len);
				$original_plaintext = @openssl_decrypt($ciphertext_raw, $cipher, ENCRYPT_KEY, $options=OPENSSL_RAW_DATA, $iv);
				
				if(!$original_plaintext) {
					$return = $string;
				
				} else {
					$calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPT_KEY, $as_binary=true);
				
					if(hash_equals($hmac, $calcmac)) {
						$return = $original_plaintext;
					}
				}
			
			} else {
				//$this->debug("doDecrypt 2 (deprecated)");
				
				if(!function_exists("mcrypt_decrypt")) {
					if(function_exists("mail")) {
						$individole->mailDennis("mcrypt_decrypt problem ".$_SERVER['HTTP_HOST'], "");
					}
				
				} else {
					$return = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(ENCRYPT_KEY), base64_decode((string) $string), MCRYPT_MODE_CBC, md5(md5(ENCRYPT_KEY))), "\0");
				}
			}
			
			return $return;
			
		} else {
			return $string;
		}
	}
	
	function doEncrypt($string, $force_encrypt_7=0, $args=array()) {
		if(trim((string) $string) != "") {
			if(!function_exists("mcrypt_encrypt") || defined("ENCRYPT_7") || $force_encrypt_7 == 1) {
				(isset($args['hashtype'])) ? $hashtype = $args['hashtype'] : $hashtype = 'sha256';
								
				$ivlen 				= openssl_cipher_iv_length($cipher="AES-128-CBC");
				$iv 					= openssl_random_pseudo_bytes($ivlen);
				$ciphertext_raw 	= openssl_encrypt($string, $cipher, ENCRYPT_KEY, $options=OPENSSL_RAW_DATA, $iv);
				$hmac 				= hash_hmac($hashtype, $ciphertext_raw, ENCRYPT_KEY, $as_binary=true);
				$return 				= base64_encode( $iv.$hmac.$ciphertext_raw );
			
			} else {
				if(!function_exists("mcrypt_encrypt")) {
					$return = '';
					if(function_exists("mail")) {
						$individole->mailDennis("mcrypt_encrypt problem ".$_SERVER['HTTP_HOST'], "");
					}
				
				} else {
					$return = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(ENCRYPT_KEY), $string, MCRYPT_MODE_CBC, md5(md5(ENCRYPT_KEY))));
				}
			}
			
			return $return;
			
		} else {
			return $string;
		}
	}
	
	function doHashCompare($password_string, $password_hash) {
		$return = password_verify($password_string, $password_hash);
		
		return $return;
	}
	
	function doHashEncrypt($password_string) {
		$return = password_hash($password_string, PASSWORD_BCRYPT, array('cost' => 12));
		
		return $return;
	}
	
	function doHashShort($string, $hashtype="ripemd128") {
		return hash_hmac($hashtype, (string) $string, ENCRYPT_KEY);
	}
	
	function doHashShortCompare($hash1, $hash2) {
		return hash_equals($this->doHashShort((string) $hash1), $hash2);
	}
	
	//!DISABLED
	function doRedirectAfterLogin() {
		 global $redirect_to;
		 if (!isset($_GET['redirect_to'])) {
			  $redirect_to = '/wp-admin/index.php';
		 }
	}
	
	function doRedirects() {
		if(is_404()) {
			global $wp_query;
			global $post;
			
			// check existing page, if 404 is a custom post type 404
			if(file_exists($this->path['base'].'/'.$this->acf.'/individole-configs-cpt.php')) {
				if(!isset($individole)) {
					$individole = new individole();
					$this->_init();
				}
				
				global $config_cpt_theme;
				if(!isset($config_cpt_theme)) {
					require($this->path['base'].'/'.$this->acf.'/individole-configs-cpt.php');
				}
				
				if(isset($wp_query->query['post_type']) && isset($config_cpt_theme[$wp_query->query['post_type']])) {
					// $this->debug($config_cpt_theme);
					
					$path = trim($_SERVER['REQUEST_URI'], '/');
					$path = strtok($path, '?');
					// $this->debug($path);
					
					$page = get_page_by_path($path);
					// $this->debug($page);
					
					if(!$page && $this->isWPML()) {
						$path = ltrim($path, $this->l.'/');
						// $this->debug($path);
						
						$page = get_page_by_path($path);
						// $this->debug($page);
					}
					
					if($page) {
						$post = $page;
						
						$wp_query->set('post_type', 'page');
						$wp_query->set('page_id', $page->ID);
						$wp_query->is_page = true;
						$wp_query->is_single = false;
						$wp_query->is_404=false;
						status_header(200);
					}
				}
			}
		}
		
		unset($_SESSION['individole_page_elements']);
		
		$redirect_url = '';
		if($this->isCPT("page_elements")) {
			if($this->isAdmin()) {
				$_SESSION['individole_page_elements'] = true;
				
			} else {
				if($this->isWPML()) {
					$redirect_url = icl_get_home_url();
					
				} else {
					$redirect_url = get_home_url();
				}
			}
		}
		
		if($redirect_url != "") {
			wp_redirect($redirect_url);
			exit;
		}
	}
	
	function dpExcludeDuplicatePM($meta_excludelist) {
		return array_merge( $meta_excludelist, [ '_icl_lang_duplicate_of', 'post_count' ] );
	}
	
	function ecwidCreateAddress($args) {
		$obj = new individole_ecwid_create_address();
		return $obj->create($args);
	}
	
	function ecwidCreateBuyButton($id=0, $args=array()) {
		$obj = new individole_ecwid_create_buy_button();
		return $obj->create($id, $args);
	}
	
	function ecwidCreateBrutto($value, $tax=19) {
		$return = $value + ($value * ($tax / 100));
		
		return $return;
	}
	
	function ecwidCreateCheckout($args=array()) {
		$obj = new individole_ecwid_create_checkout();
		return $obj->create($args);
	}
	
	function ecwidCreateDHLLabel() {
		$obj = new individole_ecwid_create_dhl_label();
		return $obj->create();
	}
	
	function ecwidCreateInvoiceNumber($args) {
		$obj = new individole_ecwid_create_invoice_number();
		return $obj->create($args);
	}
	
	function ecwidCreateOrderPositions($data) {
		$obj = new individole_ecwid_create_order_positions();
		return $obj->create($data);
	}
	
	function ecwidCreateOrderTable($args=array()) {
		$obj = new individole_ecwid_create_order_table();
		return $obj->create($args);
	}
	
	function ecwidCreateOrderTrackingNumbers($order, $tracking_numbers=array()) {
		$obj = new individole_ecwid_create_order_tracking_numbers();
		return $obj->create($order, $tracking_numbers);
	}
	
	function ecwidCreatePDF($args=array()) {
		$obj = new individole_ecwid_create_pdf();
		return $obj->create($args);
	}
	
	function ecwidDoWebhook($headers, $content) {
		$obj = new individole_ecwid_do_webhook();
		return $obj->create($headers, $content);
	}
	
	function ecwidGetAttributes($args) {
		$obj = new individole_ecwid_get_attributes();
		return $obj->create($args);
	}
	
	function ecwidGetCarriers() {
		$obj = new individole_ecwid_get_carriers();
		return $obj->create();
	}
	
	function ecwidGetCategories() {
		$obj = new individole_ecwid_get_categories();
		return $obj->create();
	}
	
	function ecwidGetCombinations($args) {
		$obj = new individole_ecwid_get_combinations();
		return $obj->create($args);
	}
	
	function ecwidGetDHLProductPM($id) {
		$obj = new individole_ecwid_get_dhl_product_pm();
		return $obj->create($id);
	}
	
	function ecwidGetDHLProducts($args=array()) {
		$obj = new individole_ecwid_get_dhl_products();
		return $obj->create($args);
	}
	
	function ecwidGetNextInvoiceNumber() {
		$obj = new individole_ecwid_get_next_invoice_number();
		return $obj->create();
	}
	
	function ecwidGetOptions($data, $args=array()) {
		$obj = new individole_ecwid_get_options();
		return $obj->create($data, $args);
	}
	
	function ecwidGetOptionsReplaces($args) {
		$obj = new individole_ecwid_get_options_replaces();
		return $obj->create($args);
	}
	
	function ecwidGetOptionsSelected($args) {
		$obj = new individole_ecwid_get_options_selected();
		return $obj->create($args);
	}
	
	function ecwidGetShopChoices($args=array()) {
		$obj = new individole_ecwid_get_shop_choices();
		return $obj->create($args);
	}
	
	function ecwidGetShipping() {
		if($ecwid_shipping = get_option("individole_ecwid_shipping")) {
			$this->ecwid_shipping = $this->unserialize($ecwid_shipping);
			return $this->ecwid_shipping;
			
		} else {
			return false;
		}
	}
	
	function ecwidGetShippingGroup($product) {
		$obj = new individole_ecwid_get_shipping_group();
		return $obj->create($product);
	}
	
	function ecwidGetStatus($status, $args=array()) {
		$obj = new individole_ecwid_get_status();
		return $obj->create($status, $args);
	}
	
	function ecwidGetPrices($args, $tax=19, $digits=2) {
		$obj = new individole_ecwid_get_prices();
		return $obj->create($args, $tax, $digits);
	}
	
	function ecwidSetOptionsReplaces($data) {
		$obj = new individole_ecwid_set_options_replaces();
		return $obj->create($data);
	}
	
	function ecwidGetDesign($single_value="") {
		$obj = new individole_ecwid_get_design();
		return $obj->create($single_value);
	}
	
	function ecwidGetSettings($single_value="") {
		$obj = new individole_ecwid_get_settings();
		return $obj->create($single_value);
	}
	
	function ecwidGetShippingOptions() {
		$obj = new individole_ecwid_get_shipping_options();
		return $obj->create();
	}
	
	function ecwidGetStatistics($args) {
		$obj = new individole_ecwid_get_statistics();
		return $obj->create($args);
	}
	
	function ecwidGetTrackingNumbers($args) {
		$obj = new individole_ecwid_get_tracking_numbers();
		return $obj->create($args);
	}
	
	function ecwidIncludeScripts() {
		$obj = new individole_ecwid_include_scripts();
		return $obj->create();
	}
	
	function ecwidIsActive() {
		$obj = new individole_ecwid_is_active();
		return $obj->create();
	}
	
	function ecwidSendMail($args) {
		$obj = new individole_ecwid_send_mail();
		return $obj->create($args);
	}
	
	function ecwidSendOrderNotification() {
		$obj = new individole_ecwid_send_order_notification();
		return $obj->create();
	}
	
	function ecwidUpdateCustomerAccount($data) {
		$obj = new individole_ecwid_update_customer_account();
		return $obj->create($data);
	}
	
	function ecwidUpdateOrderData($data, $data_2) {
		$obj = new individole_ecwid_update_order_data();
		return $obj->create($data, $data_2);
	}
	
	function enableDraftsParents( $args ) {
		$args['post_status'] = 'draft,publish,pending,private';
		return $args;
	}
	
	function endsWith($string, $string_to_find) {
		$length = strlen((string) $string_to_find);
		$start  = $length * -1;
		return (substr($string, $start) === $string_to_find);
	}
	
	function enqueueScriptsAsync($url) {
		//return $url;
		
		if ( strpos( $url, '#asyncload') === false ) {
			return $url;
		} else if ( is_admin() ) {
			return str_replace( '#asyncload', '', $url );
		} else {
			return str_replace( '#asyncload', '', $url )."' async='async";
		}
	}
	
	function enqueueScriptsLogin() {
		wp_enqueue_style('individole_login', $this->version('/_individole/_css/css/login.css'));
		wp_enqueue_script('individole_login',$this->version('/_individole/_javascript/login.js'));
		
		$domain = $this->getDomainPlain();
		// $this->debug($domain, 2);
		
		$files = array(
			$this->path['base'].'/_images/login-logo-'.$domain.'.svg',
			$this->path['base'].'/_images/login-logo-'.$domain.'.png',
			$this->path['base'].'/_images/login-logo-'.$domain.'.gif',
			$this->path['base'].'/_images/login-logo-'.$domain.'.jpg',
			
			$this->path['base'].'/_images/login-logo.svg',
			$this->path['base'].'/_images/login-logo.png',
			$this->path['base'].'/_images/login-logo.gif',
			$this->path['base'].'/_images/login-logo.jpg',
		);
		
		$logo = '';
		foreach($files AS $file) {
			if(file_exists($file)) {
				$logo = $this->version($file);
				break;
			}
		}
		
		if($logo != "") {
			echo '
				<style type="text/css">
					#login h1 a, .login h1 a {
						background-image: url('.$logo.');
					}
				</style>
			';
		}
	}
	
	function enqueueScripts() {
		wp_deregister_script('jquery');
		wp_deregister_script('jquery-migrate');
		wp_deregister_script('jquery-core');
		wp_deregister_script('comment-reply');
		
		if(!defined("GUTENBERG")) {
			wp_dequeue_style('wp-block-library');
			wp_dequeue_style('wp-block-library-theme');
			wp_dequeue_style('wc-blocks-style');
			wp_dequeue_style('global-styles');
			wp_dequeue_style('wpml-blocks');
			wp_dequeue_style('classic-theme-styles');
		}
		
		if($this->io("library_jquery_cdn") == 0) {
			wp_enqueue_script('jquery', $this->url['individole'].'/_libraries/_jquery/jquery-'.$this->io("library_jquery").'.min.js', false, $this->io("library_jquery"));
			
			if($this->io("library_jquery_disable_migrate") != 1) {
				wp_enqueue_script('jquery-migrate', $this->url['individole'].'/_libraries/_jquery/jquery-migrate.'.$this->jquery_migrate_version.'.min.js', false, $this->jquery_migrate_version);
			}
			
		} else {
			wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/'.$this->io("library_jquery").'/jquery.min.js', false, $this->io("library_jquery"));
			
			if($this->io("library_jquery_disable_migrate") != 1) {
				wp_enqueue_script('jquery-migrate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/'.$this->jquery_migrate_version.'/jquery-migrate.min.js', false, $this->jquery_migrate_version);
			}
		}
		
		if($this->isAdmin()) {
			wp_enqueue_script('jquery_ui', $this->url['individole'].'/_libraries/_jquery/ui/minified/jquery-ui.min.js', false, '1.10');
		}
				
		add_action( 'pre-plupload-upload-ui', 'ob_start' );
		add_action( 'post-plupload-upload-ui', 'ob_end_clean' );
	}
	
	function enqueueAdminScripts($page) {
		//$this->debug($page);
		
		wp_enqueue_script('select2', WP_PLUGIN_URL.'/advanced-custom-fields-pro/assets/inc/select2/4/select2.full.min.js', array('jquery'), '4.0');
		wp_enqueue_style('select2', WP_PLUGIN_URL.'/advanced-custom-fields-pro/assets/inc/select2/4/select2.min.css', '', '4.0');
		
		wp_enqueue_script('medium_editor', $this->url['individole'].'/_libraries/_medium_editor/js/medium-editor.min.js', array('jquery'), $this->medium_editor_version);
		wp_enqueue_style('medium_editor_css', $this->url['individole'].'/_libraries/_medium_editor/css/medium-editor.css', '', $this->medium_editor_version);
		wp_enqueue_style('medium_editor_css2', $this->url['individole'].'/_libraries/_medium_editor/css/individole.css', '', $this->medium_editor_version);
		
		// wp_enqueue_script('nested_sortable', $this->version('/_individole/_javascript/minified/nested-sortable.min.js'), array( 'jquery','jquery-ui-sortable'));
		
		wp_enqueue_script('sortable_lists', $this->version('/_individole/_javascript/jquery-sortable-lists.js'), '');
		// wp_enqueue_script('sortable_lists', $this->version('/_individole/_javascript/minified/jquery-sortable-lists.min.js'), '');
		
		wp_enqueue_script('functions', $this->version('/_individole/_javascript/functions.js'));
		wp_enqueue_script('functions_backend', $this->version('/_individole/_javascript/functions_backend.js'));
		
		$file_backend_project = get_stylesheet_directory().'/_javascript/functions_backend.js';
		if(file_exists($file_backend_project)) {
			wp_enqueue_script('functions_backend_project', $this->version($file_backend_project));
		
		} else {
			$file_backend_project = get_stylesheet_directory().'/_javascript/functions_admin.js';
			if(file_exists($file_backend_project)) {
				wp_enqueue_script('functions_backend_project', $this->version($file_backend_project));
			}
		}
		
		if(is_admin()) {
			wp_enqueue_media();
		}
	}
	
	function enqueue_media_uploader() {
		//wp_enqueue_media();
	}
	
	function explode($delimiter, $array) {
		return array_filter(explode($delimiter, $array), 'strlen');
	}
	
	function facebook($args) {
		$obj = new individole_facebook($args);
		return $obj;
	}
	
	function faSVG($value) {
		$obj = new individole_misc_fa_svg();
		return $obj->create($value);
	}
	
	function float($num) {
		$dotPos = strrpos((float) $num, '.');
		$commaPos = strrpos((float) $num, ',');
		$sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
			((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
	  
		if (!$sep) {
			return floatval(preg_replace("/[^0-9]/", "", (string) $num));
		}
	
		return floatval(
			preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
			preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen((string) $num)))
		);
	}
	
	function forceAdminColor() {
		return "fresh";
	}
	
	function forceFacebookScrape($post_id) {
		$obj = new individole_facebook_scrape();
		
		$url = get_permalink($post_id);
			
		return $obj->scrape($url);
	}
	
	function forceGDLib() {
		return array('WP_Image_Editor_GD', 'WP_Image_Editor_Imagick');
		// return array('WP_Image_Editor_Imagick', 'WP_Image_Editor_GD');
	}
	
	function currencyToValue($money){
		$cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
		$onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);
		
		$separatorsCountToBeErased = strlen((string) $cleanString) - strlen((string) $onlyNumbersString) - 1;
	
		$stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
		$removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);
		
		$return = str_replace(',', '.', $removedThousandSeparator);
		$return = bcadd($return, 0, 2);
		
		return $return;
	}
	
	function formatCurrency($value, $args=array()) {
		$obj = new individole_format_currency();
		return $obj->create($value, $args);
	}
	
	function formatDate($args) {
		$obj = new individole_format_date();
		return $obj->create($args);
	}
	
	function formatHEX($color) {
		$color = '#'.str_replace("#", "", $color);
		
		return $color;
	}
	
	function formatLink($link) {
		$obj = new individole_format_link();
		return $obj->create($link);
	}
	
	function formatNumberZero($number, $zeros=1) {
		$obj = new individole_format_number_leading_zero();
		return $obj->create($number, $zeros);
	}
	
	function formatPhone($phone) {
		$obj = new individole_format_phone();
		return $obj->create($phone);
	}
	
	function formatNumber($number) {
		$number = number_format($this->float($number), 0, ",", ".");
		
		return $number;
	}
	
	function formatPrice($price) {
		if(!is_numeric($price)) {
			preg_match('/\d+(\.|,)?\d*/', $price, $matches);
			//$this->debug($matches);
				
			if(isset($matches[0])) {
				$price = $matches[0];
			}
		}
			
		$price = number_format($this->float($price), 2, ".", "");
		
		return $price;
	}
	
	function formatQuotation($text) {
		$obj = new individole_format_quotation();
		return $obj->create($text);
	}
	
	function formatQuote($text) {
		$obj = new individole_format_quote();
		return $obj->create($text);
	}
	
	function formatTitleTag($str) {
		$str 		= do_shortcode($str);
		$str 		= str_replace('"', "'", $str);
		$str 		= str_replace('&nbsp;', " ", $str);
		$str 		= trim((string) $str);
		$str 		= $this->removeLineBreaks($str);
		$str 		= strip_tags((string) $str);
		
		return $str;
	}
	
	function formatYoutubeURL($url) {
		$url = str_replace("https://", "//", $url);
		$url = str_replace("www.youtube.com", "www.youtube-nocookie.com", $url);
		
		return $url;
	}
	
	function formsCreateTable() {
		$obj = new individole_forms_create_table();
		return $obj->create();
	}
	
	function formsGetCSS() {
		$obj = new individole_forms_css();
		return $obj->create();
	}
	
	function formsSaveData($args) {
		$obj = new individole_forms_save_data();
		return $obj->create($args);
	}
	
	function frontendCreateACFEdit($args) {
		$obj = new individole_frontend_create_ACF_edit();
		return $obj->create($args);
	}
	
	function ga($category="", $label="", $pageview="") {
		$obj = new individole_misc_ga();
		return $obj->create($category, $label, $pageview);
	}
	
	function getACFData($post_id, $args=array()) {
		return $this->acfGetData($post_id, $args);
	}
	
	function getACFFieldIDs() {
		$apc_key = 'acf_field_IDs';
		$apc_data = $this->getAPCData($apc_key);
		
		if(!empty($apc_data) && !isset($_POST['delete_transients'])) {
			$return = $apc_data;
		
		} else {
			$wp_args = array(
				'post_type'					=> array('acf-field-group', 'acf-field'),
				'posts_per_page'			=> 999999,
			);
			
			$wp_query = $this->WP_Query($wp_args);
			
			//$this->debug($wp_query);
			
			$return = array();
			if($wp_query->post_count > 0) {
				foreach($wp_query->posts AS $p) {
					$return[$p->post_name] = $p->ID;
				}
			}
			
			$this->setAPCData($apc_key, $return, 3600);
		}
		
		//$this->debug($return);
		
		return $return;
	}
	
	function getACFFieldsDefaults() {
		$field_types = array(
			'_accordion',
			'button_group',
			'checkbox',
			'color_picker',
			'date_picker',
			'date_time_picker',
			'email',
			'file',
			'gallery',
			'google_map',
			'_group',
			'image',
			'link',
			'message',
			'number',
			'oembed',
			'output',
			'page_link',
			'password',
			'post_object',
			'radio',
			'range',
			'relationship',
			'repeater',
			'select',
			'separator',
			'tab',
			'taxonomy',
			'text',
			'textarea',
			'time_picker',
			'true_false',
			'url',
			'user',
			'wysiwyg',
		);
		
		$defaults = array();
		foreach($field_types AS $field_type) {
			if(class_exists('acf_field_'.$field_type)) {
				$ref_class = new ReflectionClass('acf_field_'.$field_type);
				$new_instance = $ref_class->newInstance();
				
				// $this->debug($new_instance);
				
				// if($field_type == "button_group") {
				// 	$this->debug($new_instance);
				// }
				//
				
				if($this->isDennis() && !property_exists($new_instance, 'defaults')) {
					$this->debug($field_type);
					$this->debug($new_instance);
				}
				
				$defaults[$field_type] = $new_instance->defaults;
				acf_register_field_type('acf_field_'.$field_type);
			}
		}
		
		if(!empty($defaults)) {
			// $this->debug($defaults);
			
			update_option('individole_acf_field_defaults', $defaults);
		}
	}
	
	function getACFOption($option) {
		return $this->getACFOptions($option);
	}
	
	function getACFOptions($option, $switch_to_default_language=0) {
		if($switch_to_default_language == 0 && $this->io("special_settings_redirect_default_language") == 1) {
			$switch_to_default_language = 1;
		}
		
		if(isset($this->acf_special_options[$option])) {
			$acf_options = $this->acf_special_options[$option];
		
		} else {
			if($switch_to_default_language == 1 && $this->isWPML()) {
				global $sitepress;
				
				add_filter('acf/settings/current_language', function(){
					global $sitepress;
					return $sitepress->get_default_language();
				}, 100);
				
				$acf_options = get_field($option, 'option', false);
				
				add_filter('acf/settings/current_language', function(){
					return ICL_LANGUAGE_CODE;
				}, 100);
				
			} else {
				$acf_options = get_field($option, 'option', false);
			}
		}
		
		$this->acf_special_options[$option] = $acf_options;
		
		if(!isset($acf_options[0])) {
			return array();
		}
		
		return $acf_options[0];
	}
	
	function getACFPMColumns() {
		$q = '
		SELECT `COLUMN_NAME`
		FROM `INFORMATION_SCHEMA`.`COLUMNS`
		WHERE `TABLE_NAME`="'.TABLE_PREFIX.'individole_postmeta"
		';
		
		$result = mysqli_query($this->mysql, $q);
		
		if(mysqli_num_rows($result) > 0) {
			$columns = array();
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$columns[$row['COLUMN_NAME']] = true;
			}
			
			return $columns;
			
		} else {
			return array();
		}
	}
	
	function getActivePosts() {
		$active_posts = array();
		
		global $post;
		
		if($post) {
			$active_posts[] = $post->ID;
			
			$active_posts = array_merge($active_posts, get_ancestors($post->ID, $post->post_type));
		}
		
		$active_posts = array_unique($active_posts);
		
		return $active_posts;
	}
	
	function getAdminLanguage() {
		$lang = explode("-", get_bloginfo('language'));
		return $lang[0];
	}
	
	function getAdminPosts($args) {
		$obj = individole_get_admin_posts::getInstance();
		return $obj->create($args);
	}
	
	function getAllTaxonomies($args) {
		$obj = new individole_get_all_taxonomies();
		return $obj->create($args);
	}
	
	function replaceArrayPlus(&$value, $key ) {
		//$value = str_replace('+', '%2B', $value);
	}
	
	function formatAPCKey($apc_key) {
		if($this->isAdmin()) {
			$apc_key = DB_NAME.':A:'.$apc_key;
		
		} else {
			$apc_key = DB_NAME.':'.$apc_key;
		}
		
		$apc_key = str_replace("individole_", "i_", $apc_key);
		
		return $apc_key;
	}
	
	function getAPCData($apc_key, $prevent_apcu=0) {
		$apc_key = $this->formatAPCKey($apc_key);
		
		$data = "";
		if($this->isAPC() && apc_exists($apc_key)) {
			$type = 'APC';
			$data = apc_fetch($apc_key);
			
		} else if($this->isAPCU() && apcu_exists($apc_key) && $prevent_apcu == 0) {
			$type = 'APCU';
			$data = apcu_fetch($apc_key);
			
		} else {
			$type = 'TRANSIENT';
			$data = get_transient($apc_key);
		}
		
		// $this->debug('getAPCData<br><b>'.$type.'</b><br>&#8627; '.$apc_key.' / '.strlen((string) $apc_key).')');
		
		if(is_array($data)) {
			array_walk_recursive($data, array($this, 'replaceArrayPlus'));
		}
		
		$data = urldecode_deep($data);
		
		return $data;
	}
	
	function getAPIDataAwork($args) {
		$obj = new individole_api_data_awork();
		return $obj->create($args);
	}
	
	function getAPIDataCalendly($args) {
		$obj = new individole_api_data_calendly();
		return $obj->create($args);
	}
	
	function getAPIDataHelloHQ($args) {
		$obj = new individole_api_data_hellohq();
		return $obj->create($args);
	}
	
	function getAPIDataDeepl($language="", $string="", $language_source="") {
		$obj = new individole_api_data_deepl();
		return $obj->create($language, $string, $language_source);
	}
	
	function getAPIDataDHL($args=array()) {
		$obj = new individole_api_data_dhl();
		return $obj->create($args);
	}
	
	function getAPIDataEcwid($args=array()) {
		$obj = new individole_api_data_ecwid();
		return $obj->create($args);
	}
	
	function getAPIDataFacebook($args) {
		$obj = new individole_api_data_facebook($args);
		return $obj->create();
	}
	
	function getAPIDataFacebookGraph($args) {
		$obj = new individole_api_data_facebook($args);
		return $obj->create();
	}
	
	function getAPIDataGoogleTranslate($args) {
		$obj = new individole_api_data_google_translate();
		return $obj->create($args);
	}
	
	function getAPIDataGooglePlaces($args=array()) {
		$obj = new individole_api_data_google_places();
		return $obj->create($args);
	}
	
	function getAPIDataInstagram($args) {
		$obj = new individole_api_data_instagram($args);
		return $obj->create();
	}
	
	function getAPIDataInstagramUserID($username) {
		$obj = new individole_api_data_instagram_user_id();
		return $obj->create($username);
	}
	
	function getAPIDataINXMail($args) {
		$obj = new individole_api_data_inxmail();
		return $obj->create($args);
	}
	
	function getAPIDataMailjet($args) {
		$obj = new individole_api_data_mailjet($args);
		return $obj->create();
	}
	
	function getAPIDataMailchimp($args) {
		$obj = new individole_api_data_mailchimp();
		return $obj->create($args);
	}
	
	function getAPIDataMailchimpListInfo($list_id) {
		$obj = new individole_api_data_mailchimp_listinfo();
		return $obj->create($list_id);
	}
	
	function getAPIDataPinterest($args) {
		$obj = new individole_api_data_pinterest($args);
		return $obj->create();
	}
	
	function getAPIDataSendgrid($args) {
		$obj = new individole_api_data_sendgrid($args);
		return $obj->create();
	}
	
	function getAPIDataShopify($args) {
		$obj = new individole_api_data_shopify();
		return $obj->create($args);
	}
	
	function getAPIDataSoundcloud($args) {
		$obj = new individole_api_data_soundcloud($args);
		return $obj->create();
	}
	
	function getAPIDataStripe($args) {
		$obj = new individole_api_data_stripe();
		return $obj->create($args);
	}
	
	function getAPIDataTwitter($args) {
		$obj = new individole_api_data_twitter($args);
		return $obj->create();
	}
	
	function getAPIDataUPS($args=array()) {
		$obj = new individole_api_data_ups();
		return $obj->create($args);
	}
	
	function getAPIDataVimeo($url) {
		$obj = new individole_api_data_vimeo();
		return $obj->create($url);
	}
	
	function getAPIDataYoutube($args) {
		$obj = new individole_api_data_youtube($args);
		return $obj->create();
	}
	
	function getAllTransients(){
		$obj = new individole_get_all_transients();
		return $obj->create();
	}
	
	function getArchivePage($post_type) {
		$archive_pages = get_option("individole_archive_pages");
		// $this->debug($archive_pages);
		
		if(isset($archive_pages[$post_type]) && $archive_pages[$post_type] > 0) {
			return $archive_pages[$post_type];
		}
	}
	
	function getAttachmentData( $attachment_id ) {
		$obj = new individole_get_attachment_data();
		return $obj->create($attachment_id);
	}
	
	function getBase64ByID($id) {
		$file_path = get_attached_file($id);
		
		$ext = end(explode(".", $file_path));
		$image = base64_encode(file_get_contents($file_path));
		
		$return = 'data:image/'.$ext.';base64,'.$image;
		
		return $return;
	}
	
	function getBestImageSource($args){
		$obj = new individole_get_best_image_source($args);
		return $obj->create();
	}
	
	function getBestImageSourceShadowbox($args=array(), $source=array()){
		if(isset($source['ratio'])) {
			if($source['ratio'] >= 125) {
				$args['w'] = 1000;
				$args['h'] = 1500;
				
			} else {
				$args['w'] = 1500;
				$args['h'] = 1500;
			}
			
		} else {
			$args['w'] = 2000;
			$args['h'] = 1500;
		}
		
		$data = $this->getBestImageSource($args);
		
		return $data['file'];
	}
	
	function getBrowser() {
		$obj = new individole_get_browser();
		return $obj->create();
	}
	
	function getMoreText($text="") {
		if($text != "") {
			
		} else {
			if($this->iWord("link_text_default") != "") {
				$text = $this->iWord("link_text_default");
				
			} else if($this->iWord("read_more") != "") {
				$text = $this->iWord("read_more");
			
			} else if($this->iWord("more") != "") {
				$text = $this->iWord("more");
			}
		}
		
		return $text;
	}
	
	function getAllText($text="") {
		if($text != "") {
			
		} else {
			if($this->iWord("show_all") != "") {
				$text = $this->iWord("show_all");
				
			} else if($this->iWord("show_more") != "") {
				$text = $this->iWord("show_more");
			}
		}
		
		return $text;
	}
	
	function getCanonical(){
		$obj = new individole_get_canonical();
		return $obj->create();
	}
	
	function getClassBoxColumns($args=array()) {
		$return = 'box_columns';
		if(isset($args['column_gaps'])) {
			$return .= ' col_gaps_'.$args['column_gaps'];
		}
	
		return $return;
	}
	
	function getClassCols($rows, $args=array(), $device="") {
		$obj = new individole_get_class_cols();
		return $obj->create($rows, $args, $device);
	}
	
	function getColumn($columns) {
		switch($columns)  {
			case 1:
				return 12;
				
			case 2:
				return 6;
			
			case 3:
				return 4;
			
			case 4:
				return 3;
			
			case 5:
				return "2_4";
			
			case 6:
				return 2;
			
			default:
				return 1;
		}
	}
	
	function getColumns($args) {
		$columns = 0;
		if(isset($args['view'])) {
			// $this->debug($args['view']);
			preg_match_all('/(\d+)/', $args['view'], $matches);
			
			if(isset($matches[0])) {
				// $this->debug($matches[0]);
				
				$columns = $matches[0][sizeof($matches[0])-1];
			}
		}
		
		return $columns;
	}
	
	function getColumnCount($columns) {
		return $this->getColumn($columns);
	}
	
	function getConfigCPT() {
		global $config_cpt;
		
		$this->cpt = $config_cpt;
	}
	
	function getCookieConsent() {
		$obj = new individole_get_cookie_consent();
		return $obj->create();
	}
	
	function getCoordinatesAround($args) {
		$obj = new individole_get_gmaps_coordinates_around();
		return $obj->create($args);
	}
	
	function getColorSteps($s="000000", $e="FFFFFF", $steps=3) {
		$obj = new individole_get_color_steps();
		return $obj->create($s, $e, $steps);
	}
	
	function getColumnWidth($args, $offset=false) {
		$obj = new individole_get_column_width();
		return $obj->create($args, $offset);
	}
	
	function getContentWidth($cols=12) {
		$return = ($this->col_w * $cols) + ($this->col_gap * ($cols-1)) - (($this->io("admin_grid_difference") / (12/$cols)) * 2);
		return $return;
	}
	
	function getCountries($l="") {
		$obj = new individole_get_countries();
		return $obj->create($l);
	}
	
	function getCountryISO3FromISO2($iso2) {
		$data = array("BD" => "BGD", "BE" => "BEL", "BF" => "BFA", "BG" => "BGR", "BA" => "BIH", "BB" => "BRB", "WF" => "WLF", "BL" => "BLM", "BM" => "BMU", "BN" => "BRN", "BO" => "BOL", "BH" => "BHR", "BI" => "BDI", "BJ" => "BEN", "BT" => "BTN", "JM" => "JAM", "BV" => "BVT", "BW" => "BWA", "WS" => "WSM", "BQ" => "BES", "BR" => "BRA", "BS" => "BHS", "JE" => "JEY", "BY" => "BLR", "BZ" => "BLZ", "RU" => "RUS", "RW" => "RWA", "RS" => "SRB", "TL" => "TLS", "RE" => "REU", "TM" => "TKM", "TJ" => "TJK", "RO" => "ROU", "TK" => "TKL", "GW" => "GNB", "GU" => "GUM", "GT" => "GTM", "GS" => "SGS", "GR" => "GRC", "GQ" => "GNQ", "GP" => "GLP", "JP" => "JPN", "GY" => "GUY", "GG" => "GGY", "GF" => "GUF", "GE" => "GEO", "GD" => "GRD", "GB" => "GBR", "GA" => "GAB", "SV" => "SLV", "GN" => "GIN", "GM" => "GMB", "GL" => "GRL", "GI" => "GIB", "GH" => "GHA", "OM" => "OMN", "TN" => "TUN", "JO" => "JOR", "HR" => "HRV", "HT" => "HTI", "HU" => "HUN", "HK" => "HKG", "HN" => "HND", "HM" => "HMD", "VE" => "VEN", "PR" => "PRI", "PS" => "PSE", "PW" => "PLW", "PT" => "PRT", "SJ" => "SJM", "PY" => "PRY", "IQ" => "IRQ", "PA" => "PAN", "PF" => "PYF", "PG" => "PNG", "PE" => "PER", "PK" => "PAK", "PH" => "PHL", "PN" => "PCN", "PL" => "POL", "PM" => "SPM", "ZM" => "ZMB", "EH" => "ESH", "EE" => "EST", "EG" => "EGY", "ZA" => "ZAF", "EC" => "ECU", "IT" => "ITA", "VN" => "VNM", "SB" => "SLB", "ET" => "ETH", "SO" => "SOM", "ZW" => "ZWE", "SA" => "SAU", "ES" => "ESP", "ER" => "ERI", "ME" => "MNE", "MD" => "MDA", "MG" => "MDG", "MF" => "MAF", "MA" => "MAR", "MC" => "MCO", "UZ" => "UZB", "MM" => "MMR", "ML" => "MLI", "MO" => "MAC", "MN" => "MNG", "MH" => "MHL", "MK" => "MKD", "MU" => "MUS", "MT" => "MLT", "MW" => "MWI", "MV" => "MDV", "MQ" => "MTQ", "MP" => "MNP", "MS" => "MSR", "MR" => "MRT", "IM" => "IMN", "UG" => "UGA", "TZ" => "TZA", "MY" => "MYS", "MX" => "MEX", "IL" => "ISR", "FR" => "FRA", "IO" => "IOT", "SH" => "SHN", "FI" => "FIN", "FJ" => "FJI", "FK" => "FLK", "FM" => "FSM", "FO" => "FRO", "NI" => "NIC", "NL" => "NLD", "NO" => "NOR", "NA" => "NAM", "VU" => "VUT", "NC" => "NCL", "NE" => "NER", "NF" => "NFK", "NG" => "NGA", "NZ" => "NZL", "NP" => "NPL", "NR" => "NRU", "NU" => "NIU", "CK" => "COK", "XK" => "XKX", "CI" => "CIV", "CH" => "CHE", "CO" => "COL", "CN" => "CHN", "CM" => "CMR", "CL" => "CHL", "CC" => "CCK", "CA" => "CAN", "CG" => "COG", "CF" => "CAF", "CD" => "COD", "CZ" => "CZE", "CY" => "CYP", "CX" => "CXR", "CR" => "CRI", "CW" => "CUW", "CV" => "CPV", "CU" => "CUB", "SZ" => "SWZ", "SY" => "SYR", "SX" => "SXM", "KG" => "KGZ", "KE" => "KEN", "SS" => "SSD", "SR" => "SUR", "KI" => "KIR", "KH" => "KHM", "KN" => "KNA", "KM" => "COM", "ST" => "STP", "SK" => "SVK", "KR" => "KOR", "SI" => "SVN", "KP" => "PRK", "KW" => "KWT", "SN" => "SEN", "SM" => "SMR", "SL" => "SLE", "SC" => "SYC", "KZ" => "KAZ", "KY" => "CYM", "SG" => "SGP", "SE" => "SWE", "SD" => "SDN", "DO" => "DOM", "DM" => "DMA", "DJ" => "DJI", "DK" => "DNK", "VG" => "VGB", "DE" => "DEU", "YE" => "YEM", "DZ" => "DZA", "US" => "USA", "UY" => "URY", "YT" => "MYT", "UM" => "UMI", "LB" => "LBN", "LC" => "LCA", "LA" => "LAO", "TV" => "TUV", "TW" => "TWN", "TT" => "TTO", "TR" => "TUR", "LK" => "LKA", "LI" => "LIE", "LV" => "LVA", "TO" => "TON", "LT" => "LTU", "LU" => "LUX", "LR" => "LBR", "LS" => "LSO", "TH" => "THA", "TF" => "ATF", "TG" => "TGO", "TD" => "TCD", "TC" => "TCA", "LY" => "LBY", "VA" => "VAT", "VC" => "VCT", "AE" => "ARE", "AD" => "AND", "AG" => "ATG", "AF" => "AFG", "AI" => "AIA", "VI" => "VIR", "IS" => "ISL", "IR" => "IRN", "AM" => "ARM", "AL" => "ALB", "AO" => "AGO", "AQ" => "ATA", "AS" => "ASM", "AR" => "ARG", "AU" => "AUS", "AT" => "AUT", "AW" => "ABW", "IN" => "IND", "AX" => "ALA", "AZ" => "AZE", "IE" => "IRL", "ID" => "IDN", "UA" => "UKR", "QA" => "QAT", "MZ" => "MOZ");
		
		if(isset($data[$iso2])) {
			return $data[$iso2];
		
		} else {
			return $iso2;
		}
	}
	
	function getLanguageCodes($l="en") {
		$obj = new individole_get_language_codes();
		return $obj->create($l);
	}
	
	function getCPTConfig($args=array()) {
		$obj = new individole_cpt_config();
		return $obj->create($args);
	}
	
	function getCPTConfigPostTypes() {
		$return = array();
		
		if(!defined("DISABLE_PAGE_ELEMENTS")) {
			$return[] = 'page_elements';
		}
		
		if(!defined("DISABLE_FORMULARE")) {
			$return[] = 'formulare';
		}
		
		if($this->isECWID()) {
			$return[] = 'dhl_products';
		}
		
		$return[] = 'acf';
		
		return $return;
	}
	
	function getCronjob($cronjob) {
		if(!isset($this->cronjobs)) {
			$this->getCronjobs();
		}
		
		if(isset($this->cronjobs[$cronjob])) {
			return $this->cronjobs[$cronjob]['schedule'];
		}
	}
	
	function getCronjobs() {
		$obj = new individole_get_cronjobs();
		return $obj->create();
	}
	
	function getCSSColorCodes() {
		$css_variables_file = get_stylesheet_directory().'/_css/variables.scss';
		
		$colors = array();
		if(file_exists($css_variables_file)) {
			$css_variables = explode("\n", file_get_contents($css_variables_file));
			
			foreach($css_variables AS $css_variable) {
				if($this->startsWith($css_variable, '$color-')) {
					//$this->debug($css_variable);
					
					preg_match('/\$color-([^:]+)/', $css_variable, $variable);
					preg_match('/(#|rgba)([^;]+)/', $css_variable, $matches);
					
					if(!empty($matches)) {
						$colors[$variable[1]] = $matches[0];
						$colors[str_replace("-", "_", $variable[1])] = $matches[0];
					}
				}
			}
		}
		
		return $colors;
	}
	
	function getCurrentHOST() {
		$return = $this->getHTTPScheme().str_replace(array('https://', 'http://'), "", $_SERVER['HTTP_HOST']);
		return $return;
	}
	
	function getCurrentLoggedUsers($args=array()) {
		$obj = new individole_get_current_logged_users();
		return $obj->create($args);
	}
	
	function getCurrentPostID() {
		if(is_admin() && isset($_GET['post'])) {
			$page_id = $_GET['post'];
			
		} else {
			$page_id = get_the_id();
		}
		
		return $page_id;
	}
	
	function getCurrentURL() {
		$url = explode('?', $_SERVER['REQUEST_URI']);
		
		$return = $this->getHTTPScheme().$_SERVER['HTTP_HOST'].urldecode((string) $url[0]);
		
		return $return;
	}
	
	function getCurrentUser() {
		global $current_user;
		
		$this->user = 0;
		if(isset($current_user->data->ID)) {
			$this->user = $current_user->data->ID;
			//$this->setCurrentUserCaps();
		
		} else if(isset($current_user->ID)) {
			$this->user = $current_user->ID;
			//$this->setCurrentUserCaps();
		}
		
		//$this->debug($current_user);
	}
	
	function getCurrentYear() {
		return date("Y");
	}
	
	function getDomain($url){
		$parse = parse_url($url);
		
		if(isset($parse['host'])) {
			return $parse['scheme'].'://'.$parse['host'];
		}
	}
	
	function getDomainPlain($url="") {
		if($url == "") {
			$url = $_SERVER['HTTP_HOST'];
		
		} else {
			$url = 'https://'.str_replace(array("http://", "https://"), "", $url);
			$url = parse_url($url);
			$url = $url['host'];
		}
		
		$host_names = explode(".", $url);
		
		return $host_names[count($host_names)-2];
	}
	
	function getDOMContent($htmlString="", $tagName="div", $identifierType="class", $identifier="") {
		if($htmlString != "") {
			//$this->debug($tagName);
			//$this->debug($identifierType);
			//$this->debug($identifier);
			
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($htmlString);
			$xpath = new DOMXPath($dom);
			$div = $xpath->query('//' . $tagName . '[@' . $identifierType . '="' . $identifier . '"]');
			$div = $div->item(0);
			$result = $dom->saveXML($div);
			return $result;
		}
	}
	
	function getExcerpt($args) {
		if(isset($args['words'])) {
			$words = $args['words'];
		} else {
			$words = 10;
		}
		
		$excerpt_more		= apply_filters('excerpt_more', ' ' . '[...]');
		
		$text				= $args['text'];
		
		if(isset($args['raw']) && $args['raw'] == 1) {
			
		} else {
			$text			= do_shortcode( $text );
			$text			= apply_filters('the_content', $text);
		}
		
		$text				= str_replace(']]>', ']]>', $text);
		$text				= wp_trim_words( $text, $words, $excerpt_more );
		//$text				= apply_filters('the_excerpt', $text);
		
		return $text;
	}
	
	function getMediatypeByMime($mime) {
		$obj = new individole_get_mediatype_by_mime();
		return $obj->create($mime);
	}
	
	function getFilesize($attachment_id) {
		$file_path	= get_attached_file($attachment_id);
		
		if(file_exists($file_path)) {
			$filesize	= filesize($file_path);
			
			if($filesize < 1000) {
				$filesize = $filesize.'b';
				
			} else if($filesize < 1000000) {
				$filesize = round($filesize/1000).'kb';
				
			} else if($filesize < 1000000000) {
				$filesize = round($filesize/1000/1000, 1).'Mb';
				
			} else {
				$filesize = round($filesize/1000/1000/1000, 2).'Gb';
				
			}
			
			return $filesize;
		}
	}
	
	function getFiletypeByMime($mime) {
		$obj = new individole_get_filetype_by_mime();
		return $obj->create($mime);
	}
	
	function getDirectorySize($path){
		 $bytestotal = 0;
		 $path = realpath($path);
		 if($path!==false && $path!='' && file_exists($path)){
			  foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
					$bytestotal += $object->getSize();
			  }
		 }
		 return ($bytestotal/1024/1024).'MB';
	}
	
	function getFontPreview($key) {
		$obj = new individole_get_font_preview();
		return $obj->create($key);
	}
	
	function createFontAwesomeCode($str) {
		$match = preg_match('/<i class="(.+)?"><\/i>/', trim((string) $str));
		
		if($match == 1) {
			return $str;
		
		} else {
			return '<i class="'.$str.'"></i>';
		}
	}
	
	function getFrontendOptions() {
		$current_options = $this->getUsersOptions();
		
		if(isset($current_options[$this->user])) {
			// $this->debug($current_options[$this->user], 2);
			
			$this->frontend_options = array();
			foreach($current_options[$this->user] AS $k => $v) {
				if($v == 1) {
					$this->frontend_options[$k] = true;
				}
			}
			
		} else {
			$this->frontend_options = array();
		}
	}
	
	function getGlobalOption($name) {
    	add_filter('acf/settings/current_language', array($this, 'acfSetLanguage'), 100);
		$option = get_field($name, 'option');
		remove_filter('acf/settings/current_language', array($this, 'acfSetLanguage'), 100);
		
		if(isset($option[0])) {
			return $option[0];
		}
	}
	
	function getGoogleFonts() {
		// $this->debug('getGoogleFonts('.$refresh.')');
		
		$google_fonts = get_option('individole_google_fonts');
		// $this->debug($google_fonts);
		
		if(isset($_GET['refresh']) && !empty($google_fonts)) {
			return $google_fonts;
		}
		
		$curl_url = 'https://www.googleapis.com/webfonts/v1/webfonts?capability=WOFF2&key='.$this->io('google_api_key');
		
		$ch = curl_init($curl_url);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		
		// $this->debug($response);
		
		$google_fonts_choices 		= array();
		$google_fonts_choices[''] 	= '--- Select font ---';
		$google_fonts_files 			= array();
		$google_fonts_data 			= array();
		
		if(!empty($response)) {
			//Data are stored in $data
			$data = json_decode($response, true);
			
			// $this->debug($curl_url);
			// $this->debug($response);
			// $this->debug($data);
			
			if(isset($data['items'])) {
				foreach($data['items'] AS $v) {
					// $this->debug($v);
					
					foreach($v['variants'] AS $variant) {
						$variant_output = $variant;
						if($variant == 'regular') {
							$variant_output = '400 (regular)';
							
						} else if($variant == 'italic') {
							$variant_output = '400italic';
						}
						
						$key = sanitize_file_name($v['family']).'-'.$variant;
						$google_fonts_choices[$key] = $v['family'].' '.$variant_output;
						$google_fonts_files[$key] = $v['files'][$variant];
						
						if($this->containsWith($variant, 'italic')) {
							$font_style = 'italic';
						
						} else {
							$font_style = 'normal';
						}
						
						if($variant == 'italic' || $variant == 'regular') {
							$font_weight = 'normal';
							
						} else if(is_numeric($variant)) {
							$font_weight = $variant;
							
						} else {
							$font_weight = intval(preg_replace('/\D/', '', $variant));
						}
						
						$google_fonts_data[$key] = array(
							'variant'		=> $variant,
							'font-family'	=> $v['family'],
							'font-weight'	=> $font_weight,
							'font-style'	=> $font_style,
						);
					}
				}
			}
		}
		
		$return = array(
			'choices'	=> $google_fonts_choices,
			'files'		=> $google_fonts_files,
			'data'		=> $google_fonts_data,
		);
		
		$this->update_option('individole_google_fonts', $return);
		
		return $return;
	}
	
	function getGoogleShoppingCategories() {
		$obj = new individole_get_google_shopping_categories();
		return $obj->create();
	}
	
	function getHouseNumbers($string) {
		$string = trim((string) $string);
		$string = str_replace(" - ", "-", $string);
		
		$rows = explode("\n", $string);
		
		// $this->debug(nl2br((string) $string));
		
		if(!empty($rows)) {
			$result_rows = array();
			foreach($rows AS $string) {
				$string = trim((string) $string);
				$string = trim((string) $string, ',');
				// $this->debug($string);
				
				$pattern_1 = '([\d-]+[\S]|[\d]+)';
				$pattern_2 = '([\d]+([-|\/|a-z|\S]+))|(\d+)';
				
				preg_match_all('/'.$pattern_2.'/', $string, $matches);
				// $this->debug($matches);
				
				$street = '';
				$number = '';
				
				if(isset($matches[0][0])) {
					$number = $matches[0][sizeof($matches[0])-1];
					$street = str_replace($number, "", $string);
					$street = trim((string) $street);
					$street = ltrim((string) $street, ', ');
					$street = ltrim((string) $street, ',');
					$street = rtrim((string) $street, ' ,');
					$street = rtrim((string) $street, ',');
				}
				
				$result_rows[] = '
					<tr>
						<td>'.$string.'</td>
						<td>'.$street.'</td>
						<td>'.$number.'</td>
					</tr>
				';
			}
			
			$return = '
				<table class="table_admin_output">
					'.implode("", $result_rows).'
				</table>
			';
			
			return $return;
		}
	}
	
	function getHostFromURL($url, $remove_www=0) {
		$parseUrl = parse_url(trim((string) $url));
		
		//$this->debug($parseUrl);
		
		if(isset($parseUrl['host'])) {
			$return = $parseUrl['host'];
			
		} else {
			$path_explode = explode('/', $parseUrl['path'], 2);
			$return = array_shift($path_explode);
		}
		
		if($remove_www == 1) {
			$return = str_replace('www.', '', $return);
		}
		
		return $return;
	}
	
	function getHomeID() {
		if($this->isWPML()) {
			return get_option('page_on_front');
		
		} else {
			return get_option('page_on_front');
		}
	}
	
	function getHomeURL() {
		if($this->isWPML()) {
			return icl_get_home_url();
		
		} else {
			return get_home_url();
		}
	}
	
	function getHTTPScheme() {
		//$this->debug($_SERVER);
		
		if($this->isHTTPS()) {
			$return = 'https://';
		
		} else {
			$return = 'http://';
		}
	
		return $return;
	}
	
	function getImageURLs($args) {
		$obj = new individole_get_image_urls();
		return $obj->create($args);
	}
	
	function getImageSizes($not_cropped_only=false) {
		global $_wp_additional_image_sizes;
		
		$sizes = array();
		
		foreach(get_intermediate_image_sizes() as $_size) {
			if(in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				if($not_cropped_only == false || (get_option( "{$_size}_crop" ) == false)) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				}
				
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				if($not_cropped_only == false || ($_wp_additional_image_sizes[ $_size ]['crop'] == false)) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}
		}
		
		return $sizes;
	}
	
	function getIndividoleOption($option){
		if(empty($this->individole_options)) {
			$this->individole_options = $this->getIndividoleOptions();
		}
		
		if(isset($this->individole_options[$option])) {
			if(is_array($this->individole_options[$option])) {
				return $this->individole_options[$option];
			}
			
			return stripslashes((string) $this->individole_options[$option]);
			
		} else {
			return false;
		}
	}
	
	function getIndividoleOptions() {
		require($this->path['individole'].'/_tools/_values/options_values.php');
		
		$defaults = array();
		foreach($c AS $k => $v) {
			if(isset($v['default'])) {
				$defaults[$k] = $v['default'];
			}
		}
		
		$saved_values = $this->unserialize(get_option("individole_options"));
		
		if(is_array($saved_values)) {
			$this->individole_options = array_merge($defaults, $saved_values);
			
		} else {
			$this->individole_options = $defaults;
		}
		
		foreach($this->tools_separated AS $old_tool) {
			$old_tool_settings = get_option("individole_".$old_tool);
			if($old_tool_settings) {
				$this->individole_options = array_merge($this->individole_options, unserialize($old_tool_settings));
			}
		}
		
		return $this->individole_options;
	}
	
	function getIndividoleSocial($option){
		if(empty($this->individole_socials)) {
			$this->individole_socials = $this->getIndividoleSocials();
		}
		
		//$this->debug($option.'_'.$this->l);
		//$this->debug($this->individole_socials);
		//$this->debug($this->individole_socials[$option.'_'.$this->l]);
		
		if($this->isWPML() && isset($this->individole_socials[$option.'_'.$this->l])) {
			return stripslashes((string) $this->individole_socials[$option.'_'.$this->l]);
			
		} else if(isset($this->individole_socials[$option])) {
			return stripslashes((string) $this->individole_socials[$option]);
			
		} else {
			return false;
		}
	}
	
	function getIndividoleSocials() {
		require($this->path['tools'].'/_values/social_values.php');
		
		$defaults = array();
		foreach($c AS $k => $v) {
			if(isset($v['default'])) {
				$defaults[$k] = $v['default'];
			}
		}
		
		$defaults['share_options'] = explode(",", $defaults['share_options']);
		$defaults['social_link_options'] = explode(",", $defaults['social_link_options']);
		
		//$this->debug($defaults);
		
		$saved_values = get_option("individole_social");
		
		//$this->debug($saved_values);
		
		if($saved_values && !empty($saved_values)) {
			$saved_values = $this->unserialize(get_option("individole_social"));
			
			$saved_values = maybe_unserialize($saved_values);
			
			if(is_array($saved_values)) {
				$this->individole_socials = array_merge($defaults, $saved_values);
			
			} else {
				$this->individole_socials = $defaults;
				$this->individole_socials['unsaved'] = 1;
			}
			
		} else {
			$this->individole_socials = $defaults;
			$this->individole_socials['unsaved'] = 1;
		}
		
		return $this->individole_socials;
	}
	
	function getkeypath($arr, $lookup) {
	 	//if(is_array($arr) && is_array($lookup)) {
		if(is_array($arr) && $lookup > 0) {
	 		if (array_key_exists($lookup, $arr)) {
	 			 return array($lookup);
	 			 
	 		} else {
	 			 foreach($arr as $key => $subarr) {
	 				  if(is_array($subarr)) {
	 						$ret = $this->getkeypath($subarr, $lookup);
			
	 						if($ret) {
	 							 $ret[] = $key;
	 							 return $ret;
	 						}
	 				  }
	 			 }
	 		}
	 	}
	 	
	 	return null;
	}
	
	function getLevel0IDsFromIDs($ids) {
		//$this->debug($ids);
		
		if(!empty($ids)) {
			$temp_ids = array();
			foreach($ids AS $id) {
				$ancestors = get_ancestors($id, get_post_type($id));
				
				if(!empty($ancestors)) {
					$temp_ids[] = $ancestors[sizeof($ancestors)-1];
				
				} else {
					$temp_ids[] = $id;
				}
				
				//$this->debug($id);
				//$this->debug($ancestors);
			}
			
			array_unique($temp_ids);
			$ids = $temp_ids;
		}
		
		return $ids;
	}
	
	function getLinksFromIDs($ids) {
		$obj = new individole_get_links_from_ids();
		return $obj->create($ids);
	}
	
	function getLocale() {
		// $this->setLocales($this->l);
		
		if(defined("LOCALE")) {
			$return = LOCALE;
			
		} else {
			$array = array(
				'de' => 'de_DE.UTF-8',
				'en' => 'en_GB.UTF-8',
				'fr' => 'fr_FR.UTF-8',
				'sv' => 'sv_SE.UTF-8',
				'pt' => 'pt_PT.UTF-8',
				'no' => 'no_NO.UTF-8',
				'zh' => 'zh_CN.UTF-8',
				'tr' => 'tr_TR.UTF-8',
				'es' => 'es_ES.UTF-8',
				'ru' => 'ru_RU.UTF-8',
				'nl' => 'nl_NL.UTF-8',
			);
			
			if(isset($array[$this->l])) {
				$return = $array[$this->l];
				
			} else {
				$return = get_locale();
			}
		}
		
		return str_replace("_", "-", $return);
	}
	
	function getLocalFonts() {
		$obj = new individole_get_local_fonts();
		return $obj->create();
	}
	
	function getLocalFontsCSS($args) {
		$obj = new individole_get_local_fonts_css();
		return $obj->create($args);
	}
	
	function getLocalFontsChoices($args) {
		$obj = new individole_get_local_fonts_choices();
		return $obj->create($args);
	}
	
	function getLocalFontsFontface($font_css) {
		$obj = new individole_get_local_fonts_fontface();
		return $obj->create($font_css);
	}
	
	function getLocalFontsPreview($fonts) {
		$obj = new individole_get_local_fonts_preview();
		return $obj->create($fonts);
	}
	
	function getMailchimpLists($args=array()) {
		$newsletter = new individole_misc_newsletter();
		return $newsletter->get_mailchimp_lists();
	}
	
	function getMailStyles() {
		$obj = new individole_mail_get_styles();
		return $obj->create();
	}
	
	function getMetaArticleSection() {
		$obj = new individole_meta_article_section();
		return $obj->create();
	}
	
	function getMetaAuthor($page_id=0,$facebook=false) {
		$obj = new individole_meta_author();
		return $obj->create($page_id, $facebook);
	}
	
	function getMetaAuthorFacebook($page_id=0) {
		if($page_id == 0) {
			$page_id = get_the_id();
		}
		
		return $this->getMetaAuthor($page_id, true);
	}
	
	function getMetaCanonical(){
		global $page_id;
		
		$meta_canonical = trim(get_post_meta($page_id, $this->io("praefix_seo").'_0_canonical', true));
		
		//$this->debug($meta_canonical);
		
		if($meta_canonical != "") {
			return trim((string) $meta_canonical);
		
		} else {
			return false;
		}
	}
	
	function getMetaDescription($page_id=0, $reset=false, $facebook=false) {
		$obj = new individole_meta_description();
		return $obj->create($page_id, $reset, $facebook);
	}
	
	function getMetaDescriptionFacebook($page_id=0, $reset=false) {
		$obj = new individole_meta_description_facebook();
		return $obj->create($page_id, $reset);
	}
	
	function getMetaDescriptionTwitter($page_id=0, $reset=false) {
		$obj = new individole_meta_description_twitter();
		return $obj->create($page_id, $reset);
	}
	
	function getMetaImage($page_id=0, $reset=false) {
		$obj = new individole_meta_image();
		return $obj->create($page_id, $reset);
	}
	
	function getMetaImageData($image_id) {
		$obj = new individole_meta_image_data();
		return $obj->create($image_id);
	}
	
	function getMetaImageID($page_id=0, $reset=false, $no_default=0) {
		$obj = new individole_meta_image_id();
		return $obj->create($page_id, $reset, $no_default);
	}
	
	function getMetaKeywords($page_id=0, $reset=false) {
		$obj = new individole_meta_keywords();
		return $obj->create($page_id, $reset);
	}
	
	function getMetaLocale() {
		if(defined("LOCALE")) {
			return LOCALE;
			
		} else {
			return get_locale();
		}
	}
	
	function getMetaNoIndex() {
		global $post;
		
		if($post) {
			if($post->post_status != "publish") {
				return true;
				
			} else {
				$noindex = get_post_meta($post->ID, $this->io("praefix_seo").'_0_noindex', true);
				
				if($noindex == 1) {
					return true;
				
				} else {
					return false;
				}
			}
			
		} else {
			if(is_tag() && $this->io("noindex_tags") == 1) {
				return true;
				
			} else {
				return false;
			}
		}
	}
	
	function getMetaSitename($page_id=0) {
		$obj = new individole_meta_sitename();
		return $obj->create($page_id);
	}
	
	function getMetaTitle($page_id=0, $suffix=1, $reset=0, $save=1, $facebook=false) {
		$obj = new individole_meta_title();
		return $obj->create($page_id, $suffix, $reset, $save, $facebook);
	}
	
	function getMetaTitleFacebook($page_id=0, $suffix=1, $reset=0, $save=1) {
		if($page_id == 0) {
			$page_id = get_the_id();
		}
		
		return $this->getMetaTitle($page_id, 1, 0, 1, true);
	}
	
	function getMetaTitleTwitter($page_id=0, $suffix=1, $reset=0, $save=1) {
		if($page_id == 0) {
			$page_id = get_the_id();
		}
		
		return $this->getMetaTitle($page_id, 1, 0, 1, true);
	}
	
	function getMetaType($page_id=0) {
		$obj = new individole_meta_type();
		return $obj->create($page_id);
	}
	
	function getMicrotime($source="") {
		if($this->do_microtime == 1) {
			list($usec, $sec) = explode(" ",microtime());
		
			$microtime = ((float)$usec + (float)$sec*1000);
			
			if($source == "") {
				$source = array();
				if(__CLASS__) {
					$source[] = __CLASS__;
				}
				
				if(__FUNCTION__) {
					$source[] = __FUNCTION__;
				}
				
			} else if (is_object($source)) {
				$bt = debug_backtrace();
				//$bt = $bt[1];
				//$this->debug($bt);
				
				$source = array();
				$source[] = $bt[1]['class'].'@'.$bt[0]['line'];
				
				//$this->debug($source);
				
			} else if(is_string($source)) {
				$source = array($source);
			
			} else {
				$source = array("unknown");
			}
			
			$this->microtime[implode("---", $source)] = $microtime;
		}
	}
	
	function getMimeTypeByFilename($filename, $return_ending=false) {
		$obj = new individole_get_mimetype_by_filename();
		return $obj->create($filename, $return_ending);
	}
	
	function getModules($args) {
		global $post;
		
		if(isset($args['page_id'])) {
			if($args['page_id'] > 0) {
				$page_id = $args['page_id'];
				
			} else {
				return 'createModules: missing correct page_id!';
			}
			
		} else if($post) {
			$page_id = $post->ID;
				
		} else {
			$page_id = get_the_id();
		}
		
		$return = array();
		
		$fields = get_field("page_content", $page_id, true);
		
		//$this->debug($args);
		//$this->debug($page_id);
		//$this->debug($fields);
		
		if(!empty($fields)) {
			foreach($fields AS $data_value) {
				$layout = $data_value['acf_fc_layout'];
				
				$return[] = $layout;
			}
		}
		
		return $return;
	}
	
	function addPostsClausesDateMax($clauses) {
		$date_max = "";
		if(isset($GLOBALS['date_max'])) {
			$date_max = $GLOBALS['date_max'];
			
		} else {
			global $post;
			
			if($post) {
				$date_max = $post->post_date;
			}
		}
		
		if($date_max != "") {
			$clauses['where'] .= ' AND ('.TABLE_PREFIX.'posts.post_date < "'.$date_max.'")';
		}
	
		return $clauses;
	}

	function addPostsClausesPrevNext($clauses) {
		$obj = new individole_add_posts_clauses_prevnext();
		return $obj->create($clauses);
	}
	
	function getNextPostLink($post_id = null, $args=array(), $prev = false) {
		$obj = new individole_get_next_post_link();
		return $obj->create($post_id, $args, $prev);
	}
	
	function getPrevPostLink($post_id = null, $args=array()) {
		$obj = new individole_get_next_post_link();
		return $obj->create($post_id, $args, true);
	}
	
	function getNextPostID($post_id = null, $args=array()) {
		$next_post = $this->getNextPostLink($post_id, array_merge($args, array('return_data' => 1)), false);
		
		if(isset($next_post['id'])) {
			return $next_post['id'];
		}
	}
	
	function getPrevPostID($post_id = null, $args=array()) {
		$prev_post = $this->getNextPostLink($post_id, array_merge($args, array('return_data' => 1)), true);
		
		if(isset($prev_post['id'])) {
			return $prev_post['id'];
		}
	}
	
	function getOption($key, $var, $lang="") {
		$obj = new individole_get_option();
		$content = $obj->create($key, $var, $lang);
		
		if(!is_null($content)) {
			return $content;
		}
	}
	
	function getOptions($reset=0) {
		$obj = new individole_get_options();
		return $obj->create($reset);
	}
	
	function getProjectShortname() {
		if(!empty($this->io("module_flex_praefix"))) {
			$return = $this->io("module_flex_praefix");
			
		} else {
			$return = parse_url(site_url());
			$return = $return['host'];
			$return = explode(".", $return);
			$return = $return[sizeof($return)-2];
		}
		
		return $return;
	}
	
	function get_option($option) {
		$obj = new individole_get_option_wordpress();
		return $obj->create($option);
	}
	
	function iColor($var, $lang="") {
		return $this->getOptionColor($var, $lang);
	}
	
	function getOptionColor($var, $lang="") {
		$return = $this->getOption('colors', $var, $lang);
		return $return;
	}
	
	function iFile($var, $lang="", $type="", $retina=false, $debug=false, $alt="", $id="") {
		return $this->getOptionFile($var, $lang, $type, $retina, $debug, $alt, $id);
	}
	
	function iFileURL($var, $lang="", $type="", $retina=false, $debug=false, $alt="", $id="") {
		$file = $this->iFile($var, $lang, $type, $retina, $debug, $alt, $id);
		return wp_get_attachment_url($file);
	}
	
	function getOptionFile($var, $lang="", $type="", $retina=false, $debug=false, $alt="", $id="") {
		if(is_array($var)) {
			$var = $var[0];
		}
		
		$return = $this->getOption('files', $var, $lang);
		
		if(isset($return[0])) {
			if($type == 'img' || $type == 'css') {
				$url_normal = '';
				$url_retina = '';
				
				if(isset($return[0]['normal'])) {
					if($return[0]['normal'] != "") {
						$image_data_normal	= wp_get_attachment_metadata($return[0]['normal']);
						
						$url_normal 		= $this->versionImage('/'.$image_data_normal['file']);
						$w_normal			= $image_data_normal['width'];
						$h_normal			= $image_data_normal['height'];
					}
					
					if($return[0]['retina'] != "") {
						$image_data_retina	= wp_get_attachment_metadata($return[0]['retina']);
						
						$url_retina			= $this->versionImage('/'.$image_data_retina['file']);
						$w_retina			= $image_data_retina['width'];
						$h_retina			= $image_data_retina['height'];
					}
					
					if($url_normal != "" && $url_retina != "") {
						$return = '<img src="'.$url_normal.'" srcset="'.$url_normal.' 1x, '.$url_retina.' 2x" '.$alt.' />';
						
					} else if($url_normal != "") {
						$return = '<img src="'.$url_normal.'" '.$alt.' />';
					}
					
				} else {
					$image_data			= wp_get_attachment_metadata($return);
					
					$url_normal 		= $this->versionImage('/'.$image_data['file']);
					
					$return = '<img src="'.$url_normal.'" '.$alt.' />';
				}
				
				$alt = $this->createAltTitleTag($alt);
				
			} else {
				if(isset($return[0]['normal'])) {
					if($retina == true && $return[0]['retina'] != "") {
						$id = $return[0]['retina'];
					
					} else {
						$id = $return[0]['normal'];
					}
					
				} else {
					$id = $return;
				}
				
				if($type != "") {
					$image_data = wp_get_attachment_image_src($id, 'full');
					
					if(!$image_data) {
						$return = array(
							'url'	=> '',
							'w'		=> 0,
							'h'		=> 0,
						);
						
					} else {
						$url			= $image_data[0];
						$w				= $image_data[1];
						$h				= $image_data[2];
						
						// $this->debug($id);
						// $this->debug($image_data);
						
						if($type == "path") {
							$return	= get_attached_file($id);
							
						} else if($type == "url") {
							$return	= $url;
							
						} else if($type == "w") {
							$return	= $w;
							
						} else if($type == "h") {
							$return	= $h;
							
						} else if($type == "array") {
							$return = array(
								'url'	=> $url,
								'w'		=> $w,
								'h'		=> $h,
							);
						}
					}
					
				} else {
					$return = $id;
				}
			}
			
			return $return;
		}
	}
	
	function iGallery($var, $lang="") {
		return $this->getOptionGallery($var, $lang);
	}
	
	function getOptionGallery($var, $lang="") {
		$return = $this->getOption('galleries', $var, $lang);
		
		if(!is_array($return)) {
			$return = $this->unserialize($return);
		}
			
		return $return;
	}
	
	function implode($divider, $array, $divider_last="") {
		if(empty($divider_last)) {
			return implode($divider, $array);
		
		} else {
			$first = join(', ', array_slice($array, 0, -1));
			$last  = array_slice($array, -1);
			$both  = array_filter(array_merge(array($first), $last), 'strlen');
			
			return join($divider_last, $both);
		}
	}
	
	function iNumber($var, $lang="") {
		return $this->getOptionNumber($var, $lang);
	}

	function getOptionNumber($var, $lang="") {
		$return = $this->getOption('numbers', $var, $lang);
		
		return $return;
	}
	
	function iPage($var, $lang="", $url=false) {
		return $this->getOptionPage($var, $lang, $url);
	}
	
	function getOptionPage($var, $lang="", $url=false) {
		$return = $this->getOption('pages', $var, $lang);
		
		$return = nl2br(html_entity_decode((string) $return));
			
		if($url == "title") {
			$return = get_the_title($return);
			
		} else if($url == true) {
			$return = get_permalink($return);
		}
			
		return $return;
	}
	
	function iPageURL($var) {
		return $this->getOptionPageURL($var);
	}
	
	function getOptionPageURL($var) {
		return $this->getOptionPage($var, "", "url");
	}
	
	function isAjax() {
		if (defined('DOING_AJAX') && DOING_AJAX) {
			return true;
		}
	}
	
	function isDeepL() {
		$deepl = maybe_unserialize(get_option("individole_deepl"));
		
		if(isset($deepl['deepl_status']) && ($deepl['deepl_status'] == "free" || $deepl['deepl_status'] == "pro") && $deepl['deepl_auth_key'] != "") {
			if($deepl['deepl_status'] == "free") {
				$deepl['deepl_api_domain'] = 'https://api-free.deepl.com/v2';
			
			} else {
				$deepl['deepl_api_domain'] = 'https://api.deepl.com/v2';
			}
			
			return $deepl;
		}
	}
	
	function isDHL() {
		if($this->ecwid_shipping) {
			if($this->ecwid_shipping['dhl_status'] != "off" && ($this->ecwid_shipping['dhl_label_status'] == "on" || ($this->ecwid_shipping['dhl_label_status'] == "dev" && $this->isSuperAdmin()))) {
				return true;
			}
		}
	}
	
	function isECWID() {
		if($ecwid = $this->ecwidGetSettings()) {
			return $this->ecwidGetSettings();
		}
	}
	
	function isIndividoleTool($tool) {
		if(is_admin()) {
			global $current_screen;
			
			if(property_exists($current_screen, 'base')) {
				if($this->endsWith($current_screen->base, 'individole-'.$tool)) {
					return true;
				}
			}
		}
	}
	
	function isIndividoleBaseStuff() {
		if(isset($this->isIndividoleBaseStuff)) {
			return $this->isIndividoleBaseStuff;
		}
		
		$status = $this->io("individole_base_stuff");
		
		//old acf options vs. new individole options
		if($status == 1 || ($status == 2 && $this->isDennis())) {
			$this->isIndividoleBaseStuff = $status;
		
		} else {
			$this->isIndividoleBaseStuff = false;
		}
		
		// $this->debug('isIndividoleBaseStuff - status: '.$this->isIndividoleBaseStuff);
		
		return $this->isIndividoleBaseStuff;
	}
	
	function isLocked($id) {
		$lock = get_post_meta( $id, '_edit_lock', true );
		// $this->debug($lock);
		$user_is_editing = '';
		if($lock) {
			$lock = explode( ':', $lock );
			$time = $lock[0];
			$user = isset( $lock[1] ) ? $lock[1] : get_post_meta( $id, '_edit_last', true );
			
			// $this->debug($user);
			
			if($userdata = get_userdata($user)) {
				$time_window = apply_filters( 'wp_check_post_lock_window', 150 );
				
				if($time && $time > time() - $time_window && get_current_user_id() != $user) {
					return $userdata;
				}
			}
		}
	}
	
	function is_plugin_active($plugin) {
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if(is_plugin_active($plugin)) {
			return true;
		}
	}
	
	function iSentence($var, $lang="") {
		return $this->getOptionSentence($var, $lang);
	}
	
	function getOptionSentence($var, $lang="") {
		$return = $this->getOption('sentences', $var, $lang);
		
		$return = do_shortcode($return);
			
		return $return;
	}
	
	function iStatus($var, $lang="") {
		return $this->getOptionStatus($var, $lang);
	}
	
	function getOptionStatus($var, $lang="") {
		$return = $this->getOption('status', $var, $lang);
		
		return $return;
	}
	
	function iText($var, $lang="", $edit=false) {
		return $this->getOptionText($var, $lang, $edit);
	}
	
	function getOptionText($var, $lang="", $edit=false) {
		if(is_array($var) && isset($var['var'])) {
			$var = $var['var'];
		
		} else if(is_array($var) && isset($var[0])) {
			$var = $var[0];
		}
		
		$return = $this->getOption('texts', $var, $lang);
		
		$return = wpautop(do_shortcode($return));
		
		return $return;
	}
	
	function in_array($needle, $haystack, $key="") {
		if($key == "") {
			return in_array($needle, $haystack);
		
		} else {
			return array_search($needle, array_column($haystack, $key));
		}
	}
	
	function iWord($var, $lang="", $nl2br=true, $edit=false) {
		if(is_array($var) && isset($var[0])) {
			$var = $var[0];
		}
		
		return $this->getOptionWord($var, $lang, $nl2br, $edit);
	}
	
	function getOptionWord($var, $lang="", $nl2br=true, $edit=false) {
		$return = $this->getOption('words', $var, $lang);
		
		if($nl2br == true) {
			$return = nl2br((string) $return);
		}
				
		$return = do_shortcode($return);
				
		return $return;
	}
	
	function getPageHierarchy($args) {
		global $post;
		
		(isset($args['posts_per_page'])) 	? $args_posts_per_page = $args['posts_per_page'] 	: $args_posts_per_page = -1;
		(isset($args['post_parent'])) 		? $args_post_parent = $args['post_parent'] 			: $args_post_parent = 0;
		(isset($args['current_level'])) 	? $current_level = $args['current_level'] + 1 		: $current_level = 0;
		(isset($args['orderby'])) 			? $args_orderby = $args['orderby'] 					: $args_orderby = 'menu_order';
		(isset($args['order'])) 			? $args_order = $args['order'] 						: $args_order = 'ASC';
		
		//$this->debug('getPageHierarchy()');
		//$this->debug($args);
		
		$apc_time = 60 * 60 * 24;
		$apc_key 	= array();
		$apc_key[] 	= 'iphcpt';
		
		if(is_array($args['post_type'])) {
			$apc_key[] 	= implode('-', $args['post_type']);
			
		} else {
			$apc_key[] 	= $args['post_type'];
		}
		
		$apc_key[] 	= $this->l;
		
		if($this->isAdmin()) {
			if($this->isFrontendHideDraft()) {
				$apc_key[] 	= 'ad';
				
			} else {
				$apc_key[] 	= 'a';
			}
		}
		
		$apc_key[]	= 'pp'.$args_post_parent;
		$apc_key[] 	= 'or'.$args_orderby.$args_order;
		$apc_key[] 	= 'li'.$args_posts_per_page;
		
		if(isset($args['level_0'])) {
			$apc_key[] = 'l0';
		}
		
		if(isset($args['level_1'])) {
			$apc_key[] = 'l1';
		}
		
		if(isset($args['level_2'])) {
			$apc_key[] = 'l2';
		}
		
		if(isset($args['post__not_in']) && !empty($args['post__not_in'])) {
			$apc_key[] = 'pni'.implode("-", $args['post__not_in']);
		}
		
		if(isset($args['pm_hide'])) {
			$apc_key[] = $args['pm_hide'];
			
			$GLOBALS['addPostsClausesPMHide'] = $args['pm_hide'];
			add_filter('posts_clauses', array($this, 'addPostsClausesPMHide'));
		}
		
		//$this->debug($apc_key);
		
		$apc_key = implode("_", $apc_key);
		
		//$this->debug($apc_key);
		//$this->debug($args);
		
		if(!isset($args['refresh']) && isset($args['return_data'])) {
			$apc_data = $this->getAPCData($apc_key);
			
			if($apc_data) {
				//$this->debug('apc_data:');
				//$this->debug($apc_data);
				
				return $apc_data;
			}
		}
		
		$wp_args = array(
			'post_type' 		=> $args['post_type'],
			'posts_per_page'	=> $args_posts_per_page,
			'post_parent'		=> $args_post_parent,
			'orderby' 			=> $args_orderby,
			'order' 			=> $args_order,
			'post_status'		=> (isset($args['post_status'])) ? $args['post_status'] : $this->setPostStatus(),
		);
		
		if(isset($args['post__not_in']) && !empty($args['post__not_in'])) {
			$wp_args['post__not_in'] = $args['post__not_in'];
		}
		
		if(isset($args['post__in']) && !empty($args['post__in'])) {
			$wp_args['post__in'] = $args['post__in'];
		}
		
		if(isset($args['page_id'])) {
			$wp_args['page_id'] = $args['page_id'];
		}
		
		if(isset($args['p'])) {
			$wp_args['p'] = $args['p'];
		}
		
		$wp_query = $this->WP_Query($wp_args);
		
		remove_filter('posts_clauses', array($this, 'addPostsClausesPMHide'));
		
		// $this->debug($wp_args);
		// $this->debug(nl2br((string) $wp_query->request));
		
		$active 	= array();
		$data 	= array();
		foreach($wp_query->posts AS $p) {
			$data[$p->ID] = array();
			$data[$p->ID]['count'] = 0;
			
			if(isset($args['count'])) {
				$wp_args_count = array(
					'post_type' 		=> $args['count']['post_type'],
					'posts_per_page'	=> -1,
					'meta_query' 		=> array(
						array(
							'key' 		=> $args['count']['field'],
							'value' 		=> $p->ID,
							'compare'	=> "LIKE",
							'type' 		=> 'STRING',
						),
					),
				);
				
				$wp_query_count = $this->WP_Query($wp_args_count);
				
				$data[$p->ID]['count'] = $wp_query_count->found_posts;
			}
			
			$children = array();
			if(!isset($args['hide_childs']) && !isset($args['level_0'])) {
				$do_children = 1;
				
				if(isset($args['level_1']) && $current_level == 1) {
					$do_children = 0;
					
				} else if(isset($args['level_2']) && $current_level == 2) {
					$do_children = 0;
				
				} else if(isset($args['level_3']) && $current_level == 3) {
					$do_children = 0;
				}
				
				if($do_children == 1) {
					//$this->debug("do_children!");
					
					$wp_args_children = array(
						'post_type' 		=> $args['post_type'],
						'posts_per_page'	=> -1,
						'post_parent'		=> $p->ID,
						'orderby' 			=> 'menu_order',
						'order' 			=> 'ASC',
						'current_level'		=> $current_level,
						'return_data'		=> 1,
					);
					
					if(isset($args['current_level'])) {
						$wp_args_children['current_level'] = $args['current_level'] + 1;
					}
					
					if(isset($args['post__not_in']) && !empty($args['post__not_in'])) {
						$wp_args_children['post__not_in'] = $args['post__not_in'];
					}
					
					if(isset($args['pm_hide']) && $args['pm_hide'] != "") {
						$wp_args_children['pm_hide'] = $args['pm_hide'];
					}
					
					if(isset($args['level_1'])) {
						$wp_args_children['level_1'] = 1;
					}
					
					if(isset($args['level_2'])) {
						$wp_args_children['level_2'] = 1;
					}
					
					if(isset($args['level_3'])) {
						$wp_args_children['level_3'] = 1;
					}
					
					if(isset($args['single_active'])) {
						$wp_args_children['single_active'] = 1;
					}
					
					if(isset($args['count'])) {
						$wp_args_children['count'] = $args['count'];
					}
					
					if(isset($args['hide_active'])) {
						$wp_args_children['hide_active'] = 1;
					}
					
					if(isset($args['direct_links'])) {
						$wp_args_children['direct_links'] = $args['direct_links'];
					}
					
					if(isset($args['permalinks'])) {
						$wp_args_children['permalinks'] = $args['permalinks'];
					}
					
					if(isset($args['anchors'])) {
						$wp_args_children['anchors'] = $args['anchors'];
					}
					
					if(isset($args['prepend_links'])) {
						$wp_args_children['prepend_links'] = $args['prepend_links'];
					}
					
					$children = $this->getPageHierarchy($wp_args_children);
				}
			}
			
			if(isset($args['direct_links']) && isset($args['direct_links'][$p->ID])) {
				$permalink = get_permalink($args['direct_links'][$p->ID]);
				
			} else if(isset($args['permalinks']) && isset($args['permalinks'][$p->ID])) {
				$permalink = $args['permalinks'][$p->ID];
				
			} else {
				$permalink = get_permalink($p);
			}
			
			$data[$p->ID]['ID']			 		= $p->ID;
			$data[$p->ID]['post_parent'] 		= $p->post_parent;
			$data[$p->ID]['post_title'] 		= $p->post_title;
			$data[$p->ID]['post_status'] 		= $p->post_status;
			$data[$p->ID]['post_modified'] 		= $p->post_modified_gmt;
			$data[$p->ID]['permalink'] 			= $this->addServiceWorkerQueryParameter($permalink);
			
			if(!empty($children)) {
				$data[$p->ID]['children'] = $children;
			}
				
			$anchestors = get_ancestors($p->ID, 'page');
			
			if($post && $p->ID == $post->ID) {
				if(!isset($args['hide_active'])) {
					if(!isset($args['single_active'])) {
						$this->page_hierarchy_active = get_ancestors($p->ID, 'page');
					}
				
					$this->page_hierarchy_active[] = $p->ID;
				}
				
				$this->page_hierarchy_selected = $p->ID;
			}
			
			if(isset($args['active'])) {
				if(!isset($args['hide_active'])) {
					$this->page_hierarchy_active = get_ancestors($args['active'], 'page');
					$this->page_hierarchy_active[] = $args['active'];
				}
				$this->page_hierarchy_selected = $args['active'];
			}
			
			if(!isset($args['hide_active']) && isset($args['first_active']) && sizeof($data) == 1) {
				$this->page_hierarchy_active[] = $p->ID;
			}
		}
		
		$this->setAPCData($apc_key, $data, $apc_time);
		
		if(isset($args['return_data'])) {
			return $data;
			
		} else {
			$list = '';
			
			if(!isset($args['hide_list'])) {
				$list = $this->getPageHierarchyList($data, $args);
			}
			
			$return = array(
				'active'		=> $this->page_hierarchy_active,
				'selected'	=> $this->page_hierarchy_selected,
				'data'		=> $data,
				'list'		=> $list,
			);
			
			//$this->debug($return);
			
			if(isset($args['hide_active'])) {
				$this->page_hierarchy_active = array();
			}
			
			$this->page_hierarchy_selected = 0;
					
			return $return;
		}
	}
	
	function getPageHierarchyList($data, $args) {
		(isset($args['item_prepend'])) 		? $item_prepend = $args['item_prepend'] 	: $item_prepend = '';
		(isset($args['item_append'])) 		? $item_append = $args['item_append'] 		: $item_append = '';
		(isset($args['prepend'])) 			? $prepend = $args['prepend'] 					: $prepend = '';
		(isset($args['append'])) 			? $append = $args['append'] 					: $append = '';
		(isset($args['class'])) 			? $class = $args['class'] 						: $class = '';
		
		$list_items = array();
		foreach($data AS $data_id => $data_v) {
			//$this->debug($data_v);
			
			$classes = array();
			
			$children = '';
			if(isset($data_v['children']) && !empty($data_v['children'])) {
				$args_children = $args;
				$args_children['prepend'] = '';
				$args_children['append'] = '';
				$args_children['class'] = 'sub-menu';
				
				if(isset($args['count'])) {
					foreach($data_v['children'] AS $dv) {
						$data_v['count'] = $data_v['count'] + $dv['count'];
					}
				}
				
				$children = '
					<span class="sub-menu-wrap">
						'.$this->getPageHierarchyList($data_v['children'], $args_children).'
					</span>
				';
				
				$classes[] = 'menu-item-has-children';
			}
			
			if(isset($args['count']) && isset($args['count']['show_value'])) {
				$data_v['post_title'] = $data_v['post_title'].'<span class="count">'.$data_v['count'].'</span>';
			}
			
			if(!isset($args['hide_active']) && in_array($data_id, $this->page_hierarchy_active)) {
				$classes[] = 'active';
			}
			
			if($data_id == $this->page_hierarchy_selected) {
				$classes[] = 'selected';
			}
			
			$do = 1;
			if(isset($args['count']) && isset($args['count']['hide_empty']) && $data_v['count'] == 0) {
				$do = 0;
			}
			
			if($do == 1) {
				$data_anchor = '';
				$append_anchor = '';
				if(isset($args['anchors']) && isset($args['anchors'][$data_id])) {
					$data_anchor = 'data-anchor="'.$args['anchors'][$data_id].'"';
					$append_anchor = '#'.$args['anchors'][$data_id];
				}
				
				$list_items[] = '<li class="'.implode(" ", $classes).'">'.$item_prepend.'<a href="'.$data_v['permalink'].$append_anchor.'" '.$data_anchor.'>'.$data_v['post_title'].'</a>'.$item_append.$children.'</li>';
			}
		}
		
		$list = '<ul class="'.$class.'">'.$prepend.implode("", $list_items).$append.'</ul>';
		
		return $list;
	}
	
	function getParagraphs($text, $tag="p") {
		/*
		$paragraphs = preg_split( '|(<\s*p\s*\/?>)|', $text, -1, PREG_SPLIT_NO_EMPTY);
		*/
		
		$paragraphs = preg_split('~(?<=</'.$tag.'>)\s*|(?!\G)\s*(?=<'.$tag.')~', $text, -1, PREG_SPLIT_NO_EMPTY);
		//$paragraphs = preg_split('~(?<=</['.$tags.']>)\s*|(?!\G)\s*(?=<['.$tags.'])~', $text, -1, PREG_SPLIT_NO_EMPTY);
		//$this->debug($paragraphs);
		
		return $paragraphs;
	}
	
	function getParagraphsCharCount($paragraphs) {
		$i_chars = 0;
		foreach($paragraphs AS $paragraph) {
			$i_chars = $i_chars + strlen(strip_tags((string) $paragraph));
		}
		
		return $i_chars;
	}
	
	function getParagraphCountByCharCount($paragraphs, $chars=100, $skip_min=false) {
		$obj = new individole_get_paragraph_count_by_charcount();
		return $obj->create($paragraphs, $chars, $skip_min);
	}
	
	function getPartOfString($content,$start,$end){
		$r = explode($start, $content);
		return $r[1];
	}
	
	function get_permalink($p) {
		if(is_object($p)) {
			$p->filter = 'sample';
		}
		
		return get_permalink($p);
	}
	
	function getPostCountByTaxonomy($id, $post_type, $taxonomy, $args=array()) {
		$wp_args = array(
			'post_type'			=> $post_type,
			'post_status'   	=> 'publish',
			'posts_per_page' 	=> -1,
			'tax_query' 		=> array(
				'relation' 			=> 'AND',
				array(
					'taxonomy' 			=> $taxonomy,
					'field' 				=> 'id',
					'terms' 				=> array( $id )
				)
			),
		);
		
		if(isset($args['post__not_in'])) {
			$wp_args['post__not_in'] = $args['post__not_in'];
		
		} else if(isset($args['meta_query']) && !empty($args['meta_query'])) {
			$wp_args['meta_query'] = $args['meta_query'];
		}
		
		$query = $this->WP_Query($wp_args);
		
		//$this->debug($query->request);
		//if($id == 1570) {
		//	$this->debug($wp_args);
		//	$this->debug($query);
		//}
		
		$return = array(
			'count'		=> (int)$query->post_count,
			'first_id'	=> 0,
		);
		
		if($query->post_count > 0) {
			//$this->debug($query);
			
			$return['first_id'] = (int)$query->posts[0]->ID;
		
		} else {
			if(isset($wp_args['meta_query'])) {
				unset($wp_args['meta_query']);
			}
			
			if(isset($wp_args['post__not_in'])) {
				unset($wp_args['post__not_in']);
			}
			
			$query = $this->WP_Query($wp_args);
			
			if($query->post_count > 0) {
				$return['first_id'] = (int)$query->posts[0]->ID;
			}
		}
		
		return $return;
	}
	
	function getPostExcerpt($post_id=0) {
		$obj = new individole_get_post_excerpt();
		return $obj->create($post_id);
	}
	
	function getPostMetaComplete($post_id, $meta_key) {
		global $wpdb;
		
		$meta_data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key) );
		if( $meta_data != '' ) {
			$meta_data = $this->convertToArray($meta_data);
			return $meta_data[0];
		}

		return false;
	}
	
	function getPostMetas($post_id, $remove_acf_praefix=0) {
		$q = '
		SELECT
			*
		FROM
			'.TABLE_PREFIX.'postmeta
		WHERE
			post_id = '.$post_id.'
		';
		
		$result = mysqli_query($this->mysql, $q);
		
		$return = array();
		if($result) {
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				if($remove_acf_praefix == 1) {
					$row['meta_key'] = str_replace("content_post_0_", "", $row['meta_key']);
					
				} else if($remove_acf_praefix != 0) {
					$row['meta_key'] = str_replace($remove_acf_praefix, "", $row['meta_key']);
				}
				
				$return[$row['meta_key']] = maybe_unserialize($row['meta_value']);
			}
		}
		
		return $return;
	}
	
	function getPostMetaID($post_id, $meta_key) {
		$data = $this->getPostMetaComplete($post_id, $meta_key);
		
		if(isset($data['meta_id'])) {
			return $data['meta_id'];
		}
		
		return false;
	}
	
	function getPostStatus($post_status, $position="default", $date="", $text="") {
		$obj = new individole_get_post_status();
		return $obj->create($post_status, $position, $date, $text);
	}
	
	function getPostStatusClass($post_status) {
		if($this->isAdmin() && $post_status != "publish") {
			return ' individole_post_status_'.$post_status;
			
		} else {
			return "";
		}
	}
	
	function getPostTags($post_id, $prepend="") {
		$obj = new individole_get_post_tags();
		return $obj->create($post_id, $prepend);
	}
	
	function getPostTitle() {
		global $post;
		
		if($post) {
			return $post->post_title;
		}
	}
	
	function getPostTypeTags( $post_type = '', $taxonomy = 'post_tag' ) {
		$obj = new individole_get_posttype_tags();
		return $obj->create($post_type, $taxonomy);
	}
	
	function getRandomFromArray($array, $max=99999) {
		shuffle($array);
		
		$result = array_splice($array, 0, $max);
		
		return $result;
	}
	
	function getRealSiteURL() {
		$q = '
		SELECT
		 	option_value
		FROM
		 	'.TABLE_PREFIX.'options
		WHERE
			option_name = "siteurl"
		';
		$result = mysqli_query($this->mysql, $q);
		$real_siteurl = mysqli_fetch_array($result);
		$real_siteurl = $real_siteurl['option_value'];
		
		return $real_siteurl;
	}
	
	function getRegisteredPostTypes($type="names") {
		if(empty($this->registered_post_types)) {
			$this->registered_post_types['page'] = 'page';
			$args_registered_post_types = array( '_builtin' => false );
			$registered_post_types = get_post_types( $args_registered_post_types, $type );
			
			// $this->debug($registered_post_types);
			
			$exclude = array(
				"acf-",
				"scheduled-action",
				"page_elements",
				"formulare",
			);
			
			foreach($registered_post_types AS $registered_post_type) {
				if($type == "objects") {
					if(!$this->startsWith($registered_post_type->name, $exclude)) {
						$this->registered_post_types[$registered_post_type->name] = $registered_post_type->label.' ('.$registered_post_type->name.')';
					}
					
				} else {
					if(!$this->startsWith($registered_post_type, $exclude)) {
						$this->registered_post_types[$registered_post_type] = $registered_post_type;
					}
				}
			}
			
			ksort($this->registered_post_types);
		}
		
		return $this->registered_post_types;
	}
	
	function getRGB2HEXColorSteps($s=array(), $e=array(), $steps = 3) {
		$obj = new individole_get_RGB2HEXColorSteps();
		return $obj->create($s, $e, $steps);
	}
	
	function array_key_exists_wildcard ( $arr, $nee ) {
	 	$nee = str_replace( '\\*', '.*?', preg_quote( $nee, '/' ) );
		$nee = preg_grep( '/^' . $nee . '$/i', array_keys( $arr ) );
		return array_intersect_key( $arr, array_flip( $nee ) );
	}
	
	function getSearchResults($args=array()) {
		$obj = new individole_get_searchresults($args);
		return $obj->create();
	}
	
	function getSearchWords($args=array()) {
		$obj = new individole_get_searchwords($args);
		return $obj->create();
	}
	
	function getServiceWorker($key="") {
		if(isset($_SESSION['serviceworker'])) {
			if($key != "" && isset($_SESSION['serviceworker'][$key])) {
				return $_SESSION['serviceworker'][$key];
				
			} else {
				return $_SESSION['serviceworker'];
			}
		
		} else {
			return false;
		}
	}
	
	function getShortcodeModuleParams($params) {
		$source_values = explode("\n", trim(urldecode((string) $params)));
		
		$return = array();
		if(!empty($source_values)) {
			foreach($source_values AS $source_value) {
				$source_value = explode("=", $source_value);
				
				if(isset($source_value[1])) {
					$return[trim((string) $source_value[0])] = trim((string) $source_value[1]);
				}
			}
		}
		
		return $return;
	}
	
	function getShortcodes() {
		global $shortcode_tags;
		
		$shortcodes = array();
		if(!empty($shortcode_tags)) {
			foreach($shortcode_tags AS $k => $v) {
				$shortcode_function = '???';
				if(is_array($v) && isset($v[1])) {
					$shortcode_function = $v[1];
				
				} else if(is_string($v)) {
					$shortcode_function = $v;
				}
				
				if(is_array($v)) {
					$shortcodes[$k] = $k.' / $individole->'.$shortcode_function.'()';
				
				} else {
					$shortcodes[$k] = $k.' / '.$shortcode_function.'()';
				}
			}
			
			ksort($shortcodes);
		}
		
		return $shortcodes;
	}
	
	function getShortURL($post_id) {
		if (class_exists('Bitly')) {
			$bitly = new Bitly();
			$bitly_settings = get_option('bitly_settings');
			
			if ( $oathToken = bitly_settings( 'oauthToken' ) )
				$bitly->oauth( $oathToken );
			
			$short_url = urlencode((string) $bitly->get_bitly_link_for_post_id($post_id));
			//$short_url	= urlencode(wp_get_shortlink($post_id));
			
		} else if (class_exists('WP_Bitly')) {
			$return	= urlencode(wp_get_shortlink($post_id));
		
		} else {
			$return = get_permalink($post_id);
		}
		
		return $return;
	}
	
	function getSitemapURLs() {
		$sitemap_urls = array();
		//$this->debug(rtrim(ABSPATH, "/"));
		
		$host = $this->getCurrentHOST();
		
		foreach(glob(rtrim(ABSPATH, "/").'/sitemap*.xml') AS $file) {
			$sitemap_url = str_replace(rtrim(ABSPATH, "/"), $host, $file);
			$sitemap_urls[] = $sitemap_url;
		}
		
		return $sitemap_urls;
	}
	
	function getSitepressSettings() {
		$settings = array();
		
		if(isset($settings['translation-management']['custom_fields_translation'])) {
			//$settings['translation-management']['custom_fields_translation'] = array();
		}
	
		return $settings;
	}
	
	function getSlugFromLocale($locale) {
		$array = array(
			'de_DE'		=> 'de',
			'en_GB'		=> 'en',
			'en_US'		=> 'en',
			'fr_FR'		=> 'fr',
			'sv_SE'		=> 'sv',
			'pt_PT'		=> 'pt',
			'no_NO'		=> 'no',
			'zh_CN'		=> 'zh',
			'tr_TR'		=> 'tr',
			'es_ES'		=> 'es',
			'ru_RU'		=> 'ru',
		);
		
		if(isset($array[$locale])) {
			$result = $array[$locale];
		
		} else {
			$result = $array["en"];
		}
		
		return $result;
	}
	
	function getSlugFromURL($url) {
		$url = strip_tags((string) $url);
		$url = trim((string) $url);
		$url = rtrim((string) $url, '/');
		$url = explode('/', $url);
		
		return end($url);
	}
	
	function getSnippetID($var) {
		return $this->createModulePageElement($var, true);
	}
	
	function getSnippets() {
		$snippets = array();
		
		if($this->isAdmin()) {
			$where_post_status = '
				AND (p.post_status = "publish" OR p.post_status = "draft")
			';
				
		} else {
			$where_post_status = '
				AND p.post_status = "publish"
			';
		}
			
		$q = '
		SELECT
			pm.`post_id`,
			pm.`meta_value`
		FROM
			`'.TABLE_PREFIX.'postmeta` AS pm
		LEFT JOIN
			'.TABLE_PREFIX.'posts AS p
			ON (p.ID = pm.post_id)
		WHERE 1
			AND p.post_type = "page_elements"
			'.$where_post_status.'
			AND pm.`meta_key` = "content_page_elements_0_var"
			AND pm.`meta_value` != ""
		';
		
		$result = mysqli_query($this->mysql, $q);
		
		if($result) {
			while($row = mysqli_fetch_array($result)) {
				$snippets[$row['meta_value']] = $row['post_id'];
			}
			
			ksort($snippets);
		}
		
		return $snippets;
	}
	
	function getSlugsFromURL($url) {
		$url = parse_url($url);
		$url = $url['path'];
		$url = trim((string) $url);
		$url = trim((string) $url, "/");
		$url = '/'.$url.'/';
		
		return $url;
	}
	
	function getIPInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		 $output = NULL;
		 if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			  $ip = $_SERVER["REMOTE_ADDR"];
			  if ($deep_detect) {
					if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
						 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
					if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
						 $ip = $_SERVER['HTTP_CLIENT_IP'];
			  }
		 }
		 $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), "", strtolower(trim($purpose)));
		 $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		 $continents = array(
			  "AF" => "Africa",
			  "AN" => "Antarctica",
			  "AS" => "Asia",
			  "EU" => "Europe",
			  "OC" => "Australia (Oceania)",
			  "NA" => "North America",
			  "SA" => "South America"
		 );
		 if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			  $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			  if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
					switch ($purpose) {
						 case "location":
							  $output = array(
									"city"           => @$ipdat->geoplugin_city,
									"state"          => @$ipdat->geoplugin_regionName,
									"country"        => @$ipdat->geoplugin_countryName,
									"country_code"   => @$ipdat->geoplugin_countryCode,
									"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
									"continent_code" => @$ipdat->geoplugin_continentCode
							  );
							  break;
						 case "address":
							  $address = array($ipdat->geoplugin_countryName);
							  if (@strlen($ipdat->geoplugin_regionName) >= 1)
									$address[] = $ipdat->geoplugin_regionName;
							  if (@strlen($ipdat->geoplugin_city) >= 1)
									$address[] = $ipdat->geoplugin_city;
							  $output = implode(", ", array_reverse($address));
							  break;
						 case "city":
							  $output = @$ipdat->geoplugin_city;
							  break;
						 case "state":
							  $output = @$ipdat->geoplugin_regionName;
							  break;
						 case "region":
							  $output = @$ipdat->geoplugin_regionName;
							  break;
						 case "country":
							  $output = @$ipdat->geoplugin_countryName;
							  break;
						 case "countrycode":
							  $output = @$ipdat->geoplugin_countryCode;
							  break;
					}
			  }
		 }
		 return $output;
	}
	
	function getIPCountryCode() {
		return $this->getIPInfo("Visitor", "Country Code");
	}
	
	function getSSLInfo($url) {
		$orignal_parse 	= parse_url($url, PHP_URL_HOST);
		$get 			= stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
		$read 			= stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
		$cert 			= stream_context_get_params($read);
		$certinfo 		= openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
		
		$certinfo['valid_from'] 	= date(DATE_RFC2822,$certinfo['validFrom_time_t']);
		$certinfo['valid_to'] 		= date(DATE_RFC2822,$certinfo['validTo_time_t']);
		$certinfo['valid_days']		= round((strtotime($certinfo['valid_to']) - time()) / (60 * 60 * 24));
		
		return $certinfo;
	}
	
	function getDaysFromNow($date) {
		$now 				= strtotime(date('Y-m-d H:i:s'));
		$date 			= strtotime($date);
		$datediff 		= $date - $now;
		
		return round($datediff / (60 * 60 * 24));
	}
	
	function getDaysBetween($date_1, $date_2) {
		return ceil((strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24));
	}
	
	function getStopWords($lang="") {
		$obj = new individole_get_stop_words();
		return $obj->create($lang);
	}
	
	function getSVGContent($filename, $fullpath=0, $strip_tags=array()) {
		$obj = new individole_get_svg_content();
		return $obj->create($filename, $fullpath, $strip_tags);
	}
	
	function getSVGContentPlain($filename, $fullpath=0, $strip_tags=array()) {
		$obj = new individole_get_svg_content();
		return $obj->create($filename, $fullpath, array('defs', 'style'));
	}
	
	function getSVGContentInline($filename, $fullpath=0, $strip_tags=array()) {
		$obj = new individole_get_svg_content();
		return $obj->create($filename, $fullpath, array('defs', 'style', 'style_to_css'));
	}
	
	function getSVGContentMask($svg) {
		if(!empty($svg)) {
			$return = $this->getSVGContentStretch($svg, 0, array('style', 'defs'));
			
			if($return) {
				$return = preg_replace('/<\?xml.*?\?>/', '', $return);
				$return = preg_replace('/<!DOCTYPE[^>]+>/', '', $return);
				$return = preg_replace('/<(svg|path)\b([^>]*?)\sfill="[^"]*"/i', '<$1$2', $return);
				$return = preg_replace('/<svg\b([^>]*?)\swidth="[^"]*"/i', '<svg$1', $return);
				$return = preg_replace('/<svg\b([^>]*?)\sheight="[^"]*"/i', '<svg$1', $return);
				$return = addslashes($return);
				
				return $return;
			}
		}
	}
	
	function createSVGMask($args) {
		$obj = new individole_create_svg_mask();
		return $obj->create($args);
	}
	
	function getSVGViewbox($svg) {
		$svg = $this->getSVGContent($svg);
		
		$return = array(
			'w'	=> 0,
			'h'	=> 0,
		);
		
		if($svg) {
			preg_match('/viewBox="[^"]*?(\d+(?:\.\d+)?)\s+(\d+(?:\.\d+)?)\s+(\\d+(?:\.\d+)?)\s+(\d+(?:\.\d+)?)"/', $svg, $matches);
			
			// $this->debug($matches);
			
			if(isset($matches[3])) {
				$return['w'] = $matches[3];
				$return['h'] = $matches[4];
				
			} else {
				if($this->isDennis()) {
					$this->debug('missing viewBox: '.$svg);
					$this->debug($matches);
				}
			}
		}
		
		return $return;
	}
	
	function getSVGContentStretch($filename, $fullpath=0, $strip_tags=array()) {
		$obj = new individole_get_svg_content();
		$obj = $obj->create($filename, $fullpath, $strip_tags);
		
		if(!empty($obj)) {
			return str_replace('<svg', '<svg preserveAspectRatio="none"', $obj);
		}
	}
	
	function getSVGContentAll($filename) {
		$obj = new individole_get_svg_content_all();
		return $obj->create($filename);
	}
	
	function convertToRelativePath($path, $viewBoxWidth, $viewBoxHeight) {
		$relativePath = '';
		
		// Regulärer Ausdruck zum Parsen der Pfadbefehle und Koordinaten
		preg_match_all('/([a-zA-Z])([^a-zA-Z]*)/', $path, $matches, PREG_SET_ORDER);
	
		foreach ($matches as $match) {
			$command = $match[1]; // Der SVG-Befehl
			$coords = array_map('trim', preg_split('/[\s,]+/', trim($match[2]))); // Die Koordinaten
	
			$relativeCoords = array();
	
			// Iteriere über die Koordinaten und skaliere sie entsprechend der viewBox
			foreach($coords as $i => $coord) {
				// $this->debug($coord);
				// $this->debug($viewBoxWidth);
				
				if(is_numeric($coord)) {
					if($i % 2 == 0) {
						// X-Koordinate
						$relativeCoords[] = ($coord / $viewBoxWidth) * 1;
						
					} else {
						// Y-Koordinate
						$relativeCoords[] = ($coord / $viewBoxHeight) * 0.1;
						// $relativeCoords[] = $coord;
					}
				}
			}
	
			// Fügt den Befehl und die skalierten Koordinaten zum relativen Pfad hinzu
			$relativePath .= $command . implode(',', $relativeCoords);
		}
	
		return $relativePath;
	}
	
	function getSVGPathValue($svg) {
		// return 'M0,0 C0.1,0.1 0.4,0 0.6,0 C0.8,0 1,0.2 1,0 L1,0.2 L0,0.2 Z';
		
		// return 'M1000,50H0v-30S120.2,0,250,0c183.7,0,293.2,20,500,20S1000,0,1000,0v50Z';
		// return 'M1,1H0v-0.6S0.1202,0,0.25,0c0.1837,0,0.2932,0.4,0.5,0.4S1,0,1,0v1Z';
		
		// return 'M1,50H0v-30S0.1202,0,0.25,0c0.1837,0,0.2932,20,0.5,20S1,0,1,0v50Z';
		
		$absolutePath = '';
		if(preg_match('/<path[^>]*d="([^"]*)"/', $svg, $matches)) {
			$absolutePath = $matches[1];
		}
		
		// $absolutePath 		= "M1000,50H0v-30S120.2,0,250,0c183.7,0,293.2,20,500,20S1000,0,1000,0v50Z";
		
		if(!empty($absolutePath)) {
			$viewBoxWidth 		= 1000;
			$viewBoxHeight 	= 50;
			
			
			$relativePath = $this->convertToRelativePath($absolutePath, $viewBoxWidth, $viewBoxHeight);
			return $relativePath;
		}
		
		return;
		
		$path = '';
		if(preg_match('/<path[^>]*d="([^"]*)"/', $svg, $matches)) {
			$path = $matches[1];
		}
		
		$viewBoxWidth 	= 1000;
		$viewBoxHeight = 50;
			
		$lastX = 0;
		$lastY = 0;
		$relativePath = '';
		
		// Regulärer Ausdruck zum Parsen der Pfadbefehle und Koordinaten
		preg_match_all('/([a-zA-Z])([^a-zA-Z]*)/', $path, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match) {
			$command = $match[1]; // Der SVG-Befehl
			$coords = array_map('trim', explode(',', preg_replace('/\s+/', ',', trim($match[2]))));
			
			$this->debug($command);
			$this->debug($coords);
			
			switch ($command) {
				case 'M': // Move to
				case 'L': // Line to
				case 'T': // Smooth Quadratic Bezier Curve to
					$x = $coords[0];
					$y = $coords[1];
					$relativeX = $x - $lastX;
					$relativeY = $y - $lastY;
					$relativePath .= strtolower($command) . $relativeX . ',' . $relativeY;
					$lastX = $x;
					$lastY = $y;
					break;
	
				case 'H': // Horizontal line to
					$x = $coords[0];
					$relativeX = $x - $lastX;
					$relativePath .= 'h' . $relativeX;
					$lastX = $x;
					break;
	
				case 'V': // Vertical line to
					$y = $coords[0];
					$relativeY = $y - $lastY;
					$relativePath .= 'v' . $relativeY;
					$lastY = $y;
					break;
	
				case 'C': // Cubic Bezier Curve
					$x1 = $coords[0];
					$y1 = $coords[1];
					$x2 = $coords[2];
					$y2 = $coords[3];
					$x = $coords[4];
					$y = $coords[5];
					$relativePath .= 'c' .
						($x1 - $lastX) . ',' . ($y1 - $lastY) . ' ' .
						($x2 - $lastX) . ',' . ($y2 - $lastY) . ' ' .
						($x - $lastX) . ',' . ($y - $lastY);
					$lastX = $x;
					$lastY = $y;
					break;
	
				case 'S': // Smooth Cubic Bezier Curve to
				case 'Q': // Quadratic Bezier Curve
				case 'A': // Elliptical Arc
					// Diese können entsprechend der obigen Prinzipien erweitert werden.
					break;
	
				case 'Z': // Close Path
					$relativePath .= 'z';
					break;
			}
		}
		
		$this->debug($relativePath);
		
		// return $relativePath;
	}
	
	function getTaxonomyIDs($post_id, $taxonomy) {
		$taxonomy_ids = wp_get_post_categories($post_id, array());
		
		return $taxonomy_ids;
		//$this->debug($term_obj_list);
		//
		//if(!empty($term_obj_list)) {
		//	$temp_term_ids = array();
		//	foreach($term_obj_list AS $v) {
		//		$temp_term_ids[] = $v->term_id;
		//	}
		//
		//	return $temp_term_ids;
		//
		//	//$terms_string = join(',', wp_list_pluck($term_obj_list, 'term_id'));
		//
		//	//return explode(",", $terms_string);
		//}
		//
		//return array();
	}
	
	function getTaxonomyHierarchy($taxonomy="category", $args=array()) {
		//$this->debug("individole->getTaxonomyHierarchy()");
		//$this->debug($args);
		
		(isset($args['parent'])) 		? $parent = $args['parent'] 				: $parent = 0;
		(isset($args['hide_empty'])) 	? $hide_empty = $args['hide_empty'] 	: $hide_empty = 1;
		(isset($args['order'])) 		? $order = $args['order'] 					: $order = "ASC";
		(isset($args['orderby'])) 		? $orderby = $args['orderby'] 			: $orderby = "title";
		(isset($args['post_type'])) 	? $post_type = $args['post_type'] 		: $post_type = "posts";
		(isset($args['sort'])) 			? $sort = $args['sort'] 					: $sort = "name";
		
		$taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;
		
		$apc_key = 'individole_taxonomy_'.$taxonomy.'_'.$orderby.'_'.$order.'_parent-'.$parent.'_hide-empty-'.$hide_empty.'_sort-'.$sort;
		$apc_time = 24 * 60;
		
		if(isset($args['apc'])) {
			$apc_key .= '_'.$args['apc'];
		}
			
		if($this->isWPML()) {
			$apc_key .= '_'.$this->wpmlGetCurrentLanguage(array());
		}
		
		$apc_data = $this->getAPCData($apc_key);
			
		if(!empty($apc_data) && !isset($args['refresh'])) {
			//$this->debug("individole data taxonomy from <b>apc</b>:<br>".$apc_key);
			
			$return = $apc_data;
				
		} else {
			//$this->debug("individole data taxonomy <b>new</b>:<br>".$apc_key);
		
			$args_terms = array(
				'order'			=> $order,
				'orderby'		=> $orderby,
				'parent' 		=> $parent,
				'hide_empty'	=> $hide_empty,
			);
			
			$terms = get_terms($taxonomy, $args_terms);
			
			//$this->debug($args_terms);
			
			$items = array();
			foreach ($terms as $term) {
				$term->permalink 	= get_category_link($term->term_id);
				
				//$this->debug($term->name.'__'.$term->term_id.' / count_original:'.$term->count);
				
				if(isset($args['meta_query']) || isset($args['post__not_in']) || isset($args['count'])) {
					$term_counts 		= $this->getPostCountByTaxonomy($term->term_id, $post_type, $taxonomy, $args);
					$term->count 		= $term_counts['count'];
					$term->first_id 	= $term_counts['first_id'];
				
				} else {
					$term->first_id 	= 0;
				}
				
				//$this->debug('count_filtered:'.$term->count);
				
				$term_meta 			= get_term_meta($term->term_id, "", true);
				
				$term->meta			= array();
				if(!empty($term_meta)) {
					foreach($term_meta AS $t => $v) {
						if(is_string($v)) {
							$term->meta[$t] = $v;
						
						} else if(is_array($v) && sizeof($v) == 1) {
							$term->meta[$t] = $v[0];
							
						} else {
							$term->meta[$t] = $v;
						}
					}
				}
				
				$term_children_args = array(
					'parent'			=> $term->term_id,
					'hide_empty'	=> $hide_empty,
					'post_type'		=> $post_type,
					'taxonomy'		=> $taxonomy,
				);
				
				if(isset($args['meta_query'])) {
					$term_children_args['meta_query'] = $args['meta_query'];
				}
				
				if(isset($args['count'])) {
					$term_children_args['count'] = $args['count'];
				}
				
				$term->children = $this->getTaxonomyHierarchy($taxonomy, $term_children_args);
				
				$items[$term->term_id] = $term;
			}
			
			$return = $this->convertToArray($items);
			$return = $this->sortArrayByKey($return, $sort);
			
			//$this->debug($return);
			
			$this->setAPCData($apc_key, $return, $apc_time);
		}
		
		return $return;
	}
	
	function getTaxonomyHierarchyMultiple($taxonomies, $parent = 0 ) {
		if ( ! is_array( $taxonomies )  ) {
			$taxonomies = array( $taxonomies );
		}
		$results = array();
		foreach( $taxonomies as $taxonomy ){
			$terms = $this->getTaxonomyHierarchy( $taxonomy, $parent );
			if ( $terms ) {
				$results[ $taxonomy ] = $terms;
			}
		}
		return $results;
	}
	
	function getTextFromPDF($file_path){
		if($this->io("pdf_to_text") == 1) {
			include_once($this->path['libraries'].'/_pdftotext/pdftotext.php');
 			
			$parser 	= new PdfToText($file_path);
			$text		= $parser->Text;
					
			return $text;
		}
	}
	
	function getTimeDifference($time1, $time2, $precision = 6, $return_raw = 0, $short = 0) {
		$obj = new individole_get_time_difference();
		return $obj->create($time1, $time2, $precision, $return_raw, $short);
	}
	
	function getUsers() {
		if(!empty($this->users)) {
			return $this->users;
		}
		
		$this->users = get_users();
		
		return $this->users;
	}
	
	function getUsersOptions($refresh=0) {
		if($refresh == 0 && !empty($this->users_options)) {
			return $this->users_options;
		}
		
		$this->users_options = get_option('individole_options_user');
		
		return $this->users_options;
	}
	
	function getTitlesFromIDs($ids) {
		//$this->debug($ids);
		
		if(!empty($ids)) {
			if(!is_array($ids)) {
				$ids = explode(",", $ids);
			}
			
			$ids = array_unique($ids);
			
			//$this->debug($ids);
			
			if(!empty($ids)) {
				$items = array();
				foreach($ids AS $id) {
					$items[] = get_the_title($id);
				}
				
				return $items;
			}
			
		} else {
			return array();
		}
	}
	
	function getWorldmap($args) {
		$obj = new individole_get_worldmap();
		return $obj->create($args);
	}
	
	function getWPBaseURL() {
		$upload_dir = wp_upload_dir();
		$url = $upload_dir['baseurl'];
	 
		if (is_ssl()) {
			$url = str_replace( 'http://', 'https://', $url );
		}
	 
		return $url;
	}
	
	function getWPUploadURL() {
		$upload_dir = wp_upload_dir();
		$url = $upload_dir['baseurl'];
	 
		if (is_ssl()) {
			$url = str_replace( 'http://', 'https://', $url );
		}
	 
		return $url;
	}
	
	function getVATRateEU($country="", $vat_id="", $update=0) {
		$obj = new individole_get_vat_eu_country();
		return $obj->create($country, $vat_id, $update);
	}
	
	function getVATRateEUInit() {
		$this->getVATRateEU('', '', 1);
	}
	
	function getVersionInfo() {
		$return = get_option('individole_version_info');
		$return = maybe_unserialize($return);
		
		return $return;
	}
	
	function getVideoRatio($args) {
		$file = ABSPATH.ltrim(wp_make_link_relative($args['url']), '/');
		
		if(file_exists($file)) {
			include_once($this->path['individole']."/_libraries/_getID3/getid3.php");
			$getID3 = new getID3;
			$file_data = $getID3->analyze($file);
			
			//$this->debug($file);
			//$this->debug($file_data);
			
			if(isset($file_data['video']['resolution_x'])) {
				$return = 100 * ($file_data['video']['resolution_y'] / $file_data['video']['resolution_x']);
				
			} else if(isset($args['fallback'])) {
				$return = $args['fallback'];
				
			} else {
				$return = 56.25;
			}
			
		} else {
			$return = 56.25;
		}
		
		//$this->debug($return);
		
		return $return;
	}
	
	function getWPRoot() {
		$dir = dirname(__FILE__);
		do {
			if( file_exists($dir."/wp-config.php") ) {
				return $dir;
			}
		} while( $dir = realpath("$dir/..") );
		return null;
	}
	
	function getWordIndex($args=array()) {
		$w = new individole_wordindex();
		$wordindex = $w->getWordIndex($args);
		
		return $wordindex;
	}
	
	function getWordIndexByID($post_id=0) {
		if($post_id > 0) {
			$obj = new individole_wordindex();
			$words = $obj->getWordsByID($post_id);
	
			return $words;
		}
	}
	
	function getWordsFromText($text, $min_word_length=3, $utf8_decode=false) {
		$obj = new individole_get_words_from_text();
		return $obj->create($text, $min_word_length, $utf8_decode);
	}
	
	function h1($h2=0) {
		if(!isset($this->h1)) {
			$this->h1 = true;
			return 'h1';
			
		} else if($h2 == 1) {
			return 'h2';
			
		} else {
			return 'div';
		}
	}
	
	function removeTabs($string) {
		return trim(str_replace('	', '', $string));
	}
	
	function removeTagsFromH($string) {
		for($i=1; $i<=6; ++$i) {
			$this->removeTagsFromH = $i;
			
			$string = preg_replace_callback('/<h'.$i.'>.*<\/h'.$i.'>/U', function($matches){
				global $individole;
				
				return strip_tags((string) $matches[0], '<h'.$individole->removeTagsFromH.'>,<span>');
				
			}, $string);
		}
		
		return $string;
	}
	
	function googleOAuth() {
		$obj = new individole_misc_google_oauth();
		return $obj->create();
	}
	
	function hex2rgb($color){
		$color = str_replace('#', '', $color);
		if (strlen((string) $color) != 6){
			return array(0,0,0);
		}
		
		$rgb = array();
		for ($x=0;$x<3;$x++){
			$rgb[$x] = hexdec(substr($color,(2*$x),2));
		}
		return $rgb;
	}
	
	function hideEmail($email, $link=true, $params="", $text="") {
		$obj = new individole_hide_email();
		return $obj->create($email, $link, $params, $text);
	}
	
	function hideEmails($content, $link=false) {
		//$obj = new individole_hide_emails();
		//return $obj->create($content, $link);
		
		$content = preg_replace_callback(
			'^[:_a-z0-9-="]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,15})^',
			function($m) use($link) {
				return $this->hideEmail($m[0], $link);
			}
			, $content);
		
		return $content;
	}
	
	function hideEmailLinks($content) {
		//$obj = new individole_hide_email_links();
		//return $obj->create($content);
		
		$content = preg_replace_callback(
			'/<a href="mailto:([\s+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz\?=]+)"([^>].*?|)>(.*?)<\/a>/i',
			function($m) {
				//$this->debug($m);
				
				return $this->hideEmail($m[1], true, $m[2], $m[3]);
			}
			, $content);
		
		return $content;
	}
	
	function html2plain($str) {
		$str = $this->br2nl($str);
		$str = $this->p2nl($str);
		$str = strip_tags((string) $str);
		
		return $str;
	}
	
	function implodeCommaAnd($array, $args) {
		array_unique($array);
		
		(isset($args['divider'])) 		? $divider = $args['divider'] 				: $divider = ", ";
		(isset($args['divider_last'])) 	? $divider_last = $args['divider_last'] 	: $divider_last = " and ";
		
		$last  = array_slice($array, -1);
		$first = join($divider, array_slice($array, 0, -1));
		$both  = array_filter(array_merge(array($first), $last), 'strlen');
		return join($divider_last, $both);
	}
	
	function importCreateTranslations($args) {
		global $sitepress;
		global $iclTranslationManagement;
		
		$post_id = $args['post_id'];
		
		if(isset($args['image'])) {
			update_post_meta($post_id, 'content_post_0_image', get_post_thumbnail_id($post_id));
		}
		
		$minor_languages 	= $this->wpmlGetAllLanguageCodes();
		$current_lang 		= $sitepress->get_current_language();
		
		if (($key = array_search($current_lang, $minor_languages)) !== false) {
			unset($minor_languages[$key]);
		}

		//!PREPARE translation posts
		$translations = $this->wpmlGetTranslationIDs($post_id);
		$translations_get_updated_ids = 0;
		
		foreach($minor_languages AS $minor_language) {
			if(!isset($translations['ids'][$minor_language])) {
				$translations_get_updated_ids = 1;
				$iclTranslationManagement->make_duplicate($post_id, $minor_language);
			}
		}
		
		if($translations_get_updated_ids == 1) {
			$translations = $this->wpmlGetTranslationIDs($post_id);
		}
		
		$custom_fields = get_post_custom($post_id);
		foreach ($custom_fields AS $key => $value ) {
			if($this->startsWith($key, 'content_post') || $this->startsWith($key, '_content_post')) {
				foreach($minor_languages AS $minor_language) {
					update_post_meta($translations['ids'][$minor_language], $key, $value[0]);
				}
			}
		}
		
		foreach($minor_languages AS $minor_language) {
			if(isset($args['title'])) {
				$title_field 	= $args['title'].$minor_language;
				
				if(isset($custom_fields[$title_field][0])) {
					wp_update_post(
						array (
							'ID'				=> $translations['ids'][$minor_language],
							'post_name'		=> sanitize_title($custom_fields[$title_field][0]),
							'post_title'	=> $custom_fields[$title_field][0],
						)
					);
				}
			}
			
			if(isset($args['text'])) {
				$text_field 	= $args['text'].$minor_language;
				
				if(isset($custom_fields[$text_field][0])) {
					update_post_meta($translations['ids'][$minor_language], "content_post_0_text", $custom_fields[$text_field][0]);
				}
			}
			
			if(isset($args['post_status'])) {
				wp_update_post(
					array (
						'ID'				=> $translations['ids'][$minor_language],
						'post_status'	=> $args['post_status'],
					)
				);
			}
		}
	}
	
	function importBeforeImport($import_id) {
		$this->mailDennis("Import gestartet");
	}

	function importAfterImport($import_id) {
		$this->mailDennis("Import beendet");
	}
	
	function importConvertText($v) {
		$v = str_replace("<div", "<p", $v);
		$v = str_replace("</div>", "</p>", $v);
		$v = str_replace("<p>&nbsp;</p>", "", $v);
		$v = str_replace("<p></p>", "", $v);
		$v = $this->nl2p($v);
		$v = $this->minifyHTML($v);
		
		return $v;
	}
	
	function includeAllFunctions() {
		//!INCLUDE all project related functions
		foreach(glob(get_stylesheet_directory().'/_functions/*') AS $file) {
			if (is_dir($file) && !$this->startsWith(basename($file), "_")) {
				$files = glob($file.'/*.php');
				
				if(is_array($files) && !empty($files)) {
					foreach($files AS $folder_file) {
						include($folder_file);
					}
				}
			} else {
				if($this->endsWith($file, ".php")) {
					include($file);
				}
			}
		}
		
		if(function_exists("addImageSizes")) {
			add_action('init', 'addImageSizes', 99);
		}
	}
	
	function includeShopScripts() {
		return '<script src="'.$this->version('/_individole/_javascript/functions_shop.js').'"></script>';
	}
	
	function includeFormScript() {
		if($this->form == 0) {
			if($this->io("disable_minify") == 1) {
				$this->inline_scripts_include_end[] = '
					<script src="'.$this->version('/_individole/_libraries/_formulare/formulare.js').'"></script>
				';
				
				// $this->inline_scripts_include_end[] = '
				// 	<script src="/wp-content/individole/live/_libraries/_formulare/include.php"></script>
				// ';
				
			} else {
				$this->inline_scripts_include_end[] = '
					<script src="'.$this->version('/_individole/_libraries/_formulare/minified/formulare.min.js').'"></script>
				';
				
				// $this->inline_scripts_include_end[] = '
				// 	<script src="/wp-content/individole/live/_libraries/_formulare/minified/include.php"></script>
				// ';
			}
			
			if(file_exists(get_stylesheet_directory().'/_javascript/functions_formulare.js')) {
				$this->inline_scripts_include_end[] = '
					<script src="'.$this->version('/_javascript/functions_formulare.js').'"></script>
				';
			}
			
			++$this->form;
		}
	}
	
	function includeScrollmagic() {
		$obj = new individole_include_scrollmagic();
		return $obj->create();
	}
	
	function initDatabase() {
		if(defined("MYSQL_SSL_CERT")) {
			$this->mysql = mysqli_init();
			mysqli_ssl_set($this->mysql, NULL,NULL, MYSQL_SSL_CERT, NULL, NULL);
			mysqli_real_connect($this->mysql, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 3306, NULL, MYSQLI_CLIENT_SSL);
		
		} else {
			$this->mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}
		
		mysqli_report(MYSQLI_REPORT_OFF);
		mysqli_query($this->mysql, "SET SESSION group_concat_max_len=80000");
		mysqli_query($this->mysql, "SET SQL_BIG_SELECTS=1");
		mysqli_query($this->mysql, "SET NAMES 'utf8'");
		mysqli_query($this->mysql, "SET CHARACTER SET 'utf8'");
		mysqli_query($this->mysql, "SET GLOBAL max_connections = 1000");
	}
	
	function initSession() {
		//global $wpdb;
		//$wpdb->query('SET SESSION group_concat_max_len = 80000');
		//$wpdb->query('SET SQL_BIG_SELECTS=1');
	}
	
	function intersectMultiArray($array) {
		return call_user_func_array('array_intersect', $array);
	}
	
	function io($option) {
		return $this->getIndividoleOption($option);
	}
	
	function isAccountLogged() {
		return $this->accountIsLogged();
	}
		
	function isAdmin() {
		if(property_exists($this, "isAdmin")) {
			if($this->isAdmin == 1) {
				return true;
				
			} else if($this->isAdmin == 0) {
				return false;
			}
		}
		
		if(is_user_logged_in() && !$this->isServiceWorker()) {
			global $current_user;
			
			if(isset($current_user->allcaps['level_1'])) {
				$this->isAdmin = 1;
				return true;
				
			} else {
				$this->isAdmin = 0;
				return false;
			}
		
		} else {
			$this->isAdmin = 0;
			return false;
		}
	}
	
	function isHtaccessUpToDate() {
		$filetime_htaccess 				= filemtime($_SERVER["DOCUMENT_ROOT"].'/.htaccess');
		$filetime_block_rules 			= filemtime($this->path['individole'].'/individoleBlock.php');
		$filetime_add_htaccess_rules	= filemtime($this->path['individole'].'/_classes/add/individole_add_htaccess_rules.php');
		
		if($filetime_htaccess > $filetime_add_htaccess_rules && $filetime_htaccess > $filetime_block_rules) {
			return true;
		}
	}
	
	function isAdminEdit() {
		if(is_user_logged_in()) {
			if(isset($_POST['action']) && $_POST['action'] == "ajaxCreateDataEdit") {
				return true;
			
			} else {
				if(is_admin()) {
					$screen = $this->screen();
					
					//$this->debug($screen, 1);
					
					if(
						(isset($screen->base) && ($screen->base == 'post' || $screen->base == 'edit' || $screen->base == 'nav-menus'))
						|| (isset($_GET['page']) && ($this->startsWith($_GET['page'], 'individole') || $this->startsWith($_GET['page'], 'acf-options-')))
						|| (isset($screen->base) && strpos($screen->base,'acf-options') !== false)
					) {
						return true;
					}
				}
			}
		}
	}
	
	function isAnimGif($filename) {
		if(!($fh = @fopen($filename, 'rb'))) {
			return false;
		}
		
		$count = 0;
		//an animated gif contains multiple "frames", with each frame having a
		//header made up of:
		// * a static 4-byte sequence (\x00\x21\xF9\x04)
		// * 4 variable bytes
		// * a static 2-byte sequence (\x00\x2C)
		
		// We read through the file til we reach the end of the file, or we've found
		// at least 2 frame headers
		while(!feof($fh) && $count < 2) {
			$chunk = fread($fh, 1024 * 100); //read 100kb at a time
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
		}
	
		fclose($fh);
		return $count > 1;
	}
	
	function isAPC() {
		
		
		if(property_exists($this, "isAPC")) {
			return $this->isAPC;
		
		} else {
			if(function_exists("apc_exists")) {
				$this->isAPC = true;
				return $this->isAPC;
				
			} else {
				$this->isAPC = false;
				return $this->isAPC;
			}
		}
	}
	
	function isAPCU() {
		if(property_exists($this, "isAPCU")) {
			return $this->isAPCU;
		
		} else {
			if(function_exists('apcu_delete') && function_exists('apcu_fetch') && function_exists('apcu_store') && function_exists('apcu_add') && function_exists('apcu_dec') && function_exists('apcu_inc')) {
				$this->isAPCU = true;
				return $this->isAPCU;
				
			} else {
				$this->isAPCU = false;
				return $this->isAPCU;
			}
		}
	}
	
	function isArrayNumeric($array) {
		if(!is_array($array)) {
			return false;
		}
		
		foreach ($array as $a => $b) {
			if (!is_int($a)) {
				return false;
			}
		}
		return true;
	}
	
	function isRedis() {
		if(property_exists($this, "isRedis")) {
			return $this->isRedis;
		
		} else {
			if(extension_loaded("redis")) {
				$this->isRedis = true;
				return $this->isRedis;
				
			} else {
				$this->isRedis = false;
				return $this->isRedis;
			}
		}
	}
	
	function isCookieAllowed() {
		$cookie_consent = $this->getCookieConsent();
		// $this->debug($cookie_consent);
		
		if(!$cookie_consent) {
			return true;
		}
		
		if(isset($cookie_consent['is_active']) && ($cookie_consent['is_active'] == 0 || $cookie_consent['consent_type'] == "light")) {
			return true;
			
		} else {
			return false;
		}
	}
	
	function isCPT($cpt) {
		$obj = new individole_is_cpt();
		return $obj->create($cpt);
	}
	
	function isDennis() {
		if($this->isSuperAdmin()) {
			global $current_user;
			
			if($current_user->data->user_login == "dennis") {
				return true;
				
			} else {
				return false;
			}
		
		} else {
			return false;
		}
	}
	
	function isDevTheme($dev_theme="") {
		$orig_theme = get_option("template");
		$my_theme = wp_get_theme();
		
		if($my_theme->get_template() == $orig_theme.'_dev'.$dev_theme) {
			return true;
		}
	}
	
	function canFrontendEdit() {
		if($this->isAdmin() && isset($this->frontend_options['frontend_edit'])) {
			return true;
		}
	}
	
	function isFormBot($individole_form_send=0) {
		if(session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		
		$form_bot_reason = '';
		
		if(!isset($_SESSION["_ajax_nonce"])) {
			$form_bot_reason = '$_SESSION["_ajax_nonce"] not set';
		
		} else if(!isset($_POST['wpn'])) {
			$form_bot_reason = '$_POST["wpn"] not set';
			
		} else if($_POST['wpn'] != $_SESSION["_ajax_nonce"]) {
			$form_bot_reason = '$_POST["wpn"] != $_SESSION["_ajax_nonce"]';
			
		} else if(!wp_verify_nonce($_POST['wpn'], "_ajax_nonce")) {
			$form_bot_reason = '!wp_verify_nonce($_POST["wpn"], "_ajax_nonce")';
			
		} else if($individole_form_send == 1) {
			if(!isset($_POST['send_id'])) {
				$form_bot_reason = '$_POST["send_id"] not set';
		
			} else if(isset($_POST['send_id']) && !is_numeric($_POST['send_id'])) {
				$form_bot_reason = '!is_numeric($_POST["send_id"]';
			}
		}
		
		session_write_close();
		
		if(empty($form_bot_reason)) {
			return false;
			
		} else {
			if(isset($_POST)) {
				$post_table = $this->array2Table($_POST);
				
				$post_table_injections = array(
					'sample%40email.tst',
					'sample@email.tst',
					'sysdate()',
					'waitfor delay',
				);
				
				$do_mail_debug = 1;
				foreach($post_table_injections AS $post_table_injection) {
					if(!$this->containsWith($post_table, $post_table_injection)) {
						// $do_mail_debug = 0;
					}
				}
				
				if($do_mail_debug == 1) {
					$data = array();
					$data[] = '<h2>exit reason: '.$form_bot_reason.'</h2>';
					$data[] = '<h2>$_POST</h2>';
					$data[] = $this->array2Table($_POST);
					$data[] = '<h2>$_SESSION</h2>';
					$data[] = $this->array2Table($_SESSION);
					$data[] = '<h2>$_SERVER</h2>';
					$data[] = $this->array2Table($_SERVER);
					
					$this->mailDennis('SendMailExit', implode("", $data));
				}
			}
		}
	}
	
	function isFrontendEdit() {
		$obj = new individole_is_frontend_edit();
		return $obj->create();
	}
	
	function isFrontendGrid() {
		$obj = new individole_is_frontend_grid();
		return $obj->create();
	}
	
	function isFrontendHideDraft() {
		$obj = new individole_is_frontend_hide_draft();
		return $obj->create();
	}
	
	function isGoogleMaps() {
		if($this->io("library_google_maps") == true && $this->io("google_api_key") != "") {
			return true;
			
		} else {
			return false;
		}
	}
		
	function isHome() {
		if(get_the_id() == $this->getHomeID()) {
			return true;
		}
	}
	
	function isHTTPS() {
		if((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
			return true;
		}
	}
	
	function isImagePath($image_path) {
		if($this->startsWith($this->getMimeTypeByFilename($image_path), 'image')) {
			return true;
		}
	}
	
	function isLinkExternal($link) {
		$obj = new individole_is_link_external();
		return $obj->create($link);
	}
	
	function isPageNotPost() {
		if(is_tax() || is_tag() || is_category() || is_404()) {
			return true;
		}
	}
	
	function isPageTaxonomy() {
		global $wp_query_base;
		
		//$this->debug("isPageTaxonomy:");
		
		if(isset($wp_query_base->queried_object->taxonomy)) {
			//$this->debug("isPageTaxonomy yes");
			
			return $wp_query_base->queried_object->taxonomy;
		}
	}
	
	function isPasswordProtected($page_id=0) {
		$obj = new individole_is_password_protected();
		return $obj->create($page_id);
	}
	
	function isPurgeCacheButton() {
		if($this->isAdmin() && in_array($this->user, $this->show_purge_cache_button)) {
			return true;
		}
	}
	
	function isServiceWorker() {
		if(isset($_GET['sw'])) {
			return true;
		}
	}
	
	function isSuperAdmin() {
		if(is_user_logged_in()) {
			global $current_user;
			
			if(isset($current_user->caps['administrator']) && $current_user->caps['administrator'] == 1) {
				$this->isSuperAdmin = 1;
				
				return true;
				
			} else {
				$this->isSuperAdmin = 0;
				
				return false;
			}
		
		} else {
			$this->isSuperAdmin = 0;
			
			return false;
		}
	}
	
	function isTemplate($template="", $post_id=0) {
		$obj = new individole_is_template();
		return $obj->create($template, $post_id);
	}
	
	function isUserSubscriber() {
		$current_user = wp_get_current_user();
		
		if (in_array('subscriber', $current_user->roles)) {
			return true;
		}
	}
	
	function isWPRocket() {
		if(defined("WP_ROCKET_VERSION")) {
			$wp_rocket_settings = get_option("wp_rocket_settings");
			
			return $wp_rocket_settings;
		}
	}
	
	function isWPRocketCached($post_id) {
		if(function_exists("rocket_clean_domain")) {
			$path = rocket_clean_exclude_file('/'.get_page_uri($post_id).'/');
			
			if(isset($this->wprocket_excluded[$path])) {
				return true;
			}
		}
	}
	
	function isWPML() {
		if(function_exists('icl_object_id')) {
			return true;
						
		} else {
			return false;
		}
	}
	
	function mailDennis($subject, $text="") {
		$headers  = "From: Wordpress\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		
		$text = '
			<style>
				table {
					width: 100%;
					border-collapse: collapse;
				}
				
				table tr th,
				table tr td {
					padding: 4px;
					border: 1px solid #aaaaaa;
					font-size: 10px;
					line-height: 12px;
					text-align: left;
					vertical-align: top;
				}
				
				table tr th {
					whitespace: nowrap;
					background: #f2f2f2;
				}
				
				table tr td:last-child {
					width: 100%;
				}
			</style>
			'.$text.'
		';
		
		mail("debug@denniskather.de", $subject." / ".$_SERVER['HTTP_HOST'], $text, $headers);
	}
	
	function markSearchWords($str="", $args=array()) {
		$obj = new individole_format_search_words();
		return $obj->create($str, $args);
	}
	
	function maskEmail($email, $revert=false) {
		if($revert == true) {
			return str_replace("--{at}--", "@", $email);
			
		} else {
			return str_replace("@", "--{at}--", $email);
		}
	}
	
	function matomoPrivacyOptout() {
		$obj = new individole_matomo_privacy_optout();
		return $obj->create();
	}
	
	function mergeArraysValues($a1, $a2, $keys=array()) {
		$merged = array();
		
		//$this->debug($a1);
		//$this->debug($a2);
		
		$sums = array();
		foreach (array_keys($a1 + $a2) as $key) {
			if(is_numeric($a1[$key]) && is_numeric($a2[$key]) && (empty($keys) || in_array($key, $keys))) {
				$sums[$key] = (isset($a1[$key]) ? $a1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0);
				
			} else {
				$sums[$key] = $a1[$key];
			}
		}
		
		return $sums;
	}
	
	function mergeMultiArray($array) {
		return call_user_func_array('array_merge', $array);
	}
	
	function minifyX($input) {
		return str_replace(array("\n", "\t", ' '), array($this->minify_X . '\n', $this->minify_X . '\t', $this->minify_X . '\s'), $input);
	}
	
	function minifyV($input) {
		return str_replace(array($this->minify_X . '\n', $this->minify_X . '\t', $this->minify_X . '\s'), array("\n", "\t", ' '), $input);
	}
	
	function minifyComments($input) {
		$obj = new individole_minify_comments();
		return $obj->create($input);
	}
	
	function minifyCSSFallback($input) {
		$obj = new individole_minify_css_fallback();
		return $obj->create($input);
	}
	
	function minifyCSS($input, $force=0) {
		$obj = new individole_minify_css();
		return $obj->create($input, $force);
	}
	
	function minifyHTMLFallback($input, $force=0) {
		$obj = new individole_minify_html_fallback();
		return $obj->create($input, $force);
	}
	
	function minifyHTML($input, $force=0) {
		$obj = new individole_minify_html();
		return $obj->create($input, $force);
	}
	
	function minifyJSFallback($input) {
		$obj = new individole_minify_js_fallback();
		return $obj->create($input);
	}
	
	function minifyJS($input, $force=0) {
		$obj = new individole_minify_js();
		return $obj->create($input, $force);
	}
	
	function minifySVG($input, $force=0) {
		$obj = new individole_minify_svg();
		return $obj->create($input, $force);
	}
	
	function modifyAdminBar() {
		$obj = new individole_modify_admin_bar();
		return $obj->create();
	}
	
	function modifyAdminBarBefore() {
		$obj = new individole_modify_admin_bar_before();
		return $obj->create();
	}
	
	function modifyAdminDashboards() {
		$obj = new individole_dashboard();
		return $obj->create();
	}
	
	function modifyAdminDashboardsAPIConnect() {
		$obj = new individole_dashboard_api_connect();
		return $obj->create();
	}
	
	function modifyAdminDashboardsECWID() {
		$obj = new individole_dashboard_ecwid();
		return $obj->create();
	}
	
	function modifyAdminDashboardsGoogleAnalytics() {
		$obj = new individole_dashboard_google_analytics();
		return $obj->create();
	}
	
	function modifyAdminDashboardsPosts() {
		$obj = new individole_dashboard_posts();
		return $obj->create();
	}
	
	function modifyAdminDashboardsQuicklinks() {
		$obj = new individole_dashboard_quicklinks();
		return $obj->create();
	}
	
	function modifyAdminDashboardsStrings() {
		$obj = new individole_dashboard_strings();
		return $obj->create();
	}
	
	function modifyAdminDashboardsTools() {
		$obj = new individole_dashboard_tools();
		return $obj->create();
	}
	
	function modifyAdminDashboardsToolsSuperadmin() {
		$obj = new individole_dashboard_tools_admin();
		return $obj->create();
	}
	
	function modifyAdminListPagesHeader() {
		$obj = new individole_modify_admin_pages_header();
		return $obj->create();
	}
	
	function modifyAdminListTitle() {
		add_filter('the_title', array($this, 'modifyAdminListTitleValue'), 100, 2);
	}

	function modifyAdminListTitleValue( $title, $id ) {
		echo 'yyy';
		
		$l = $this->wpmlGetLanguageCodeByID($id);
		
		$flag = $this->createAdminFlag($l);
		
		return $title;
	}
	
	function modifyAdminMedia() {
		if(defined("MODIFY_UPLOAD_DATE")) {
			add_action( 'admin_notices', array($this, 'adminNoticeMediaUpladDir'));
			
		} else {
			if(isset($_SESSION["modify_upload_month"])) {
				unset($_SESSION["modify_upload_month"]);
			}
		}
	}
	
	function modifyAdminBackWPUpFTPPage() {
		$obj = new individole_modify_admin_backwpup_ftp_page();
		return $obj->create();
	}
	
	function modifyHeartbeat( $settings ) {
		$settings['interval'] = 60; //Anything between 15-60
		$settings['autostart'] = false;
		return $settings;
	}
	
	function modifySubmitboxMinorActions($post) {
		$content = array();
		
		if($post->post_status != "publish" && $post->post_status != "future") {
			$content[] = '<div class="publish-hint"><b style="color:red;">IMPORTANT:</b><br>Use "Publish" only,<br>if the content is ready to go LIVE!</div>';
		}
		
		echo implode("", $content);
	}
	
	function modifySubmitboxMiscActions($post) {
		$content = array();
		
		$content[] = '
			<div class="misc-pub-section misc-pub-modifiedtime">
				<span id="timestamp">'.$this->translate("submitbox_modified").': '.$post->post_modified_gmt.'</span>
			</div>
		';
		
		echo implode("", $content);
	}
	
	function modifyMimeTypes($post_mime_types) {
		$obj = new individole_modify_mimetypes();
		return $obj->create($post_mime_types);
	}
	 
	function modifyRewriteRulesInit() {
		$GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
	}
	
	function modifyRewriteRulesCollect( $page_rewrite_rules ) {
		$GLOBALS['wpse16902_page_rewrite_rules'] = $page_rewrite_rules;
		return array();
	}
	
	function modifyRewriteRulesPrepend( $rewrite_rules ) {
		return $GLOBALS['wpse16902_page_rewrite_rules'] + $rewrite_rules;
	}
	
	function modifyUpdatePage() {
		$obj = new individole_modify_update_page();
		return $obj->create();
	}
	
	function modifyUploadDir($param) {
		if(isset($_SESSION["modify_upload_month"]) && !empty($_SESSION["modify_upload_month"])) {
			$current 			= date("/Y/m");
			
			$param['subdir'] 	= $_SESSION["modify_upload_month"];
			$param['path'] 		= str_replace($current, $_SESSION["modify_upload_month"], $param['path']);
			$param['url'] 		= str_replace($current, $_SESSION["modify_upload_month"], $param['url']);
		}
	 
		return $param;
	}
	
	function mysqlCount($q) {
		$q = trim((string) $q);
		
		$parts = preg_split('@(?=SELECT|FROM|LEFT JOIN|JOIN|WHERE|ORDER BY|LIMIT)@i', $q);
		$parts = array_filter($parts);
		
		$q_new = array();
		$i = 0;
		foreach($parts AS $part) {
			if($i == 0 && $this->startsWith($part, 'SELECT')) {
				$q_new[] = 'SELECT COUNT(*) AS count';
			
			} else if($this->startsWith($part, 'ORDER BY') || $this->startsWith($part, 'LIMIT')) {
						
			} else {
				$q_new[] = $part;
			}
			
			++$i;
		}
		
		$result_count = mysqli_query($this->mysql, implode(" ", $q_new));
		$return = $result_count->fetch_object()->count;
		
		return $return;
	}
	
	function mysqlRows($q) {
		$result = mysqli_query($this->mysql, $q);
				
		$rows = array();
		
		if($result) {
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$rows[] = $row;
			}
		}
		
		return $rows;
	}
	
	function mysqli_rows($q) {
		return $this->mysqlRows($q);
	}
	
	function nl2li($text, $args=array()) {
		$obj = new individole_nl2_li();
		return $obj->create($text, $args);
	}
	
	function ol($text) {
		return $this->nl2ol($text);
	}
	
	function li2array($html) {
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new DOMXpath($dom);
		
		$li = $xpath->query('(//li)');
		$return = array();
		foreach($li AS $i => $v) {
			$return[] = strip_tags((string) $dom->saveHTML($v));
		}
		
		return $return;
	}
	
	function newsletterCreateAssetURL($url) {
		$assets 			= get_stylesheet_directory().'/_functions/newsletter/assets';
		$assets_abs 	= get_stylesheet_directory_uri().'/_functions/newsletter/assets';
		$assets_abs_nl = dirname(dirname(dirname(get_stylesheet_directory_uri()))).'/nl/assets';
		
		// $individole->debug('$url:'.$url.'<br>$assets:'.$assets.'<br>$assets_abs:'.$assets_abs, 1);
		
		$url_rel			= str_replace($assets_abs_nl, $assets, $url);
		$url_rel			= 'https:'.$this->version($url_rel);
		$url				= str_replace($assets_abs, $assets_abs_nl, $url_rel);
		
		// $this->debug($url, 1);
			
		return $url;
	}
	
	function newsletterCreatePreheader($text) {
		$return = '<!--[if !gte mso 9]><!----><span style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">'.$text.'</span><!--<![endif]-->';
		
		return $return;
	}
	
	function newsletterCreateSpacer($h=10, $row=false) {
		$return = '<img src="'.$this->getCurrentHOST().'/nl/assets/spacer.gif" width="1" height="'.$h.'" style="width: 1px; height: '.$h.'px; background:none;" '.$this->createAltTitleTag('spacer').'>';
		
		if($row == true) {
			$return = '<tr><td class="divider" colspan="10">'.$return.'</td></tr>';
		}
		
		return $return;
	}
	
	function nl2ol($text) {
		if(empty($text)) {
			return;
		}
		
		return '<ol>'.$this->nl2li($text).'</ol>';
	}
	
	function nl2p($text) {
		$obj = new individole_nl2_p();
		return $obj->create($text);
	}
	
	function nl2p_old($string, $line_breaks = false, $xml = true) {
		$obj = new individole_nl2_p_old();
		return $obj->create($string, $line_breaks, $xml);
	}
	
	function ul($text) {
		return $this->nl2ul($text);
	}
	
	function nl2ul($text) {
		if(empty($text)) {
			return;
		}
		
		return '<ul>'.$this->nl2li($text).'</ul>';
	}
	
	function normalizeChars($str) {
		$normalizeChars = array(
			'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ö'=>'Oe', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'Ue', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'oe', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
			'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
		);
		
		return strtr($str, $normalizeChars);
	}
	
	function now() {
		return date('Y-m-d H:i:s');
	}
	
	function numberDot($value, $comma=2) {
		$value = number_format($value, $comma, ",", ".");
		
		return $value;
	}
	
	function orderbyPostTitleInt( $clauses ) {
		global $wpdb;
	
		$clauses['orderby'] = 'LENGTH('.$wpdb->posts.'.post_title), '.$wpdb->posts.'.post_title ASC';
	
		return $clauses;
	}
	
	function orderbyWeek( $clauses ) {
		global $wpdb;
	
		$clauses['orderby'] = 'YEAR('.$wpdb->posts.'.post_date) DESC, WEEK('.$wpdb->posts.'.post_date) DESC, '.$wpdb->posts.'.menu_order ASC';
	
		return $clauses;
	}
	
	function ordinal($number) {
		$language = $this->wpmlGetCurrentLanguage(array());
		
		if($language == "en") {
			$ends = array('th','st','nd','rd','th','th','th','th','th','th');
			
			if ((($number % 100) >= 11) && (($number%100) <= 13)) {
				return $number.'<sup>th</sup>';
				
			} else {
				return $number. '<sup>'.$ends[$number % 10].'</sup>';
			}
			
		} else {
			return $number.'.';
		}
	}
	
	function p2nl($string) {
		$return = $string;
		
		$return = preg_replace('#<p\s*?/?>#i', "\n\n", (string) $return);
		$return = preg_replace('#\n\n#i', "\n", (string) $return);
		
		return $return;
	}
	
	function parseCSS($file){
		$obj = new individole_misc_parse_css();
		return $obj->create($file);
	}
	
	function parseLines($lines) {
		$lines = str_replace(" ", "%20", $lines);
		$lines = str_replace("&", urlencode("&"), $lines);
		$lines = explode("\n", $lines);
		$lines = implode("&", $lines);
		parse_str((string) $lines, $parse);
		
		return $parse;
	}
	
	function parseURL($url) {
		$url = 'http://'.str_replace(array("http://", "https://"), "", $url);
		
		$parse_url = parse_url($url);
		
		return $parse_url;
	}
	
	function prepareContentForOutput($content, $center_h=false, $hide_email=false, $link=false) {
		$obj = new individole_format_page_content();
		return $obj->create($content, $center_h, $hide_email, $link);
	}
	
	function printURL() {
		$this->deletePrintFiles();
		
		$folder = $this->path['base_images'].'/individole_print';
		
		if(!file_exists($folder)) {
			mkdir($folder);
			chmod($folder, 0755);
		}
		
		$filename 					= str_replace('.', '_'.time().'.', $_POST['filename']);
		$filepath 					= $folder.'/'.$filename;
		
		$return 						= array();
		$return['filepath'] 		= $filepath;
		$return['url'] 			= $this->path['abs_images'].'/individole_print/'.$filename;
		$return['error'] 			= '';
		$return['success'] 		= 0;
		
		if(!$this->startsWith($_POST['url'], "http")) {
			$data = base64_decode((string) $_POST['url']);
			
		} else {
			$ch = curl_init();
				
			curl_setopt($ch, CURLOPT_URL, $_POST['url']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			curl_setopt($ch, CURLOPT_HEADER, 1);
				
			$data = curl_exec($ch);
			$error = curl_errno($ch) ? curl_error($ch) : '';
				
			curl_close($ch);
		}
		
		if($this->startsWith($data, array('HTTP/1.1 500', 'HTTP/1.1 404'))) {
			$return['error'] = 'Datei konnte nicht (mehr) abgerufen werden.
Drucken nicht möglich.';
		
		} else {
			if(file_put_contents($filepath, $data)) {
				chmod($filepath, 0777);
				$return['success'] = 1;
			}
		}
		
		echo json_encode($return);
		exit();
	}
	
	function publishMissingPosts() {
		global $wpdb;

		$query = 'SELECT ID FROM '.$wpdb->posts.' WHERE ( ( post_date > 0 && post_date <= "'.current_time( 'mysql', 0 ).'" ) ) AND post_status = "future" LIMIT 0,10';
		
		$scheduledIDs = $wpdb->get_col( $query );
		
		//update_option('individole_publishMissingPosts', $scheduledIDs);
		
		if(!empty($scheduledIDs)) {
			foreach($scheduledIDs AS $scheduledID) {
				wp_publish_post($scheduledID);
			}
		}
	}
	
	function purgeAll() {
		if($this->isWPRocket()) {
			if(function_exists("opcache_reset")) {
				opcache_reset();
			}
			
			if(isset($_POST['purge_cache_language'])) {
				rocket_clean_domain($_POST['purge_cache_language']);
				
			} else {
				$this->purgeMinifiedFiles();
				rocket_clean_domain();
			}
			
			$this->wprocketPurgeCloudflare();
		}
	}
	
	function purgeAllPosts() {
		if($this->isWPRocket()) {
			if(function_exists("opcache_reset")) {
				opcache_reset();
			}
			
			rocket_clean_domain();
			$this->wprocketPurgeCloudflare();
		}
	}
	
	function purgeCloudflareFiles($file) {
		$obj = new individole_purge_cloudflare_files();
		return $obj->create($file);
	}
	
	function purgeCloudFlareURL($urls_to_purge) {
		$obj = new individole_purge_cloudflare_url();
		return $obj->create($urls_to_purge);
	}
	
	function purgeExternalCache($url, $purge='all') {
		$url = $url.'/wp-content/individole/live/_libraries/_purge/purge.php?purge='.$purge.'&time='.time();
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_REFERER, 'https://www.individole.com');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$r = curl_exec($ch);
		curl_close($ch);
		
		//$this->debug(json_decode($r), 2);
	}
	
	function purgeMinifiedFiles() {
		$files = array(
			'/_javascript/functions_footer.min.js',
			'/_javascript/functions_header.min.js',
			'/_javascript/google_maps.min.js',
			'/_css/css/stylesheet.min.css',
		);
		
		foreach($files AS $file) {
			$filepath = get_stylesheet_directory().$file;
			
			if(file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}
	
	function purgePages($post_id = 0) {
		$obj = new individole_purge_pages_action();
		return $obj->create($post_id);
	}
	
	function purgeURL($url) {
		if(!$this->containsWith($url, "?")) {
			$first_purge_url = trailingslashit($url).'index.html';
			
			if(!in_array($first_purge_url, $this->purge_urls)) {
				$this->purge_urls[] = $first_purge_url;
				$this->purge_urls[] = trailingslashit($url).'index.html_gzip';
				$this->purge_urls[] = trailingslashit($url).'index-https.html';
				$this->purge_urls[] = trailingslashit($url).'index-https.html_gzip';
				$this->purge_urls[] = trailingslashit($url).'index-mobile-https.html';
				$this->purge_urls[] = trailingslashit($url).'index-mobile-https.html_gzip';
			}
		}
	}
	
	function queryDbug() {
		$obj = new individole_queryDbug();
		return $obj->create();
	}
	
	function replaceObjectSizes($string, $args=array()) {
		$return = $string;
		
		preg_match_all("#<iframe(.*?)></iframe>#is", $return, $results);
		
		if(empty($results)) {
			return $return;
		}
		
		foreach($results[1] AS $iframe) {
			$iframe = '<iframe'.$iframe.'></iframe>';
			
			preg_match('/(height)="([0-9]+).*"/i', $iframe, $check_height);
			preg_match('/(width)="([0-9]+).*"/i', $iframe, $check_width);
			
			//$this->debug($iframe);
			//$this->debug($check_width);
			//$this->debug($check_height);
			
			$replace = '';
			if(isset($check_width[1]) && isset($check_height[1])) {
				$ratio = ($check_height[2] * 100) / $check_width[2];
				
				$replace = $iframe;
				$replace = str_replace('width="'.$check_width[2].'"', '', $replace);
				$replace = str_replace('height="'.$check_height[2].'"', '', $replace);
				
				$replace = '<div class="embed-container" style="padding-bottom:'.$ratio.'%;">'.$replace.'</div>';
				
				$return = str_replace($iframe, $replace, $return);
			}
		}
		//$this->debug($results);
		
		return $return;
	}
	
	function randString($length=30, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
		$str = '';
		$count = strlen((string) $charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count-1)];
		}
		
		return $str;
	}
	
	function registerJQuery() {
		//wp_deregister_script('wp-embed');
		//wp_deregister_script('jquery');
		//wp_register_script('jquery', $this->url['individole'].'/_libraries/_jquery/jquery-2.2.3.min.js', false, '2.2.3');
		//wp_enqueue_script('jquery');
	}
	
	function removeMediaElement() {
		return '';
	}
	
	function registerNavMenus() {
		register_nav_menus(array('main_nav' => 'The Main Menu'));
	}
	
	function registerStyles() {
		
	}
	
	function relativeURL($url) {
		return parse_url($url, PHP_URL_PATH);
	}
	
	function removeAttributesFromImages( $html ) {
		$html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
		return $html;
	}
	
	function removeAttributesFromHTMLTags( $html, $tags=array() ) {
		if(empty($tags)) {
			return $html;
		}
		
		$html = preg_replace( '/('.implode("|", $tags).')="([^"]+)"/s', "", $html );
		return $html;
	}
	
	function removeEmptyHTMLTags($string) {
		return $this->removeEmptySpaces($string);
	}
	
	function removeHtmlTagsFromArray($array) {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$array[$key] = $this->removeHtmlTagsFromArray($value);
		
			} else {
				$array[$key] = strip_tags((string) $value);
			}
		}
		
		return $array;
	}
	
	function removeHTTP($str) {
		$str = str_replace("http://", "//", $str);
		$str = str_replace("https://", "//", $str);
		
		return $str;
	}
	
	function removeIMGFromHTML($text) {
		return preg_replace("/<img[^>]+\>/i", "(image) ", $text);
	}
	
	function removeIndividoleInitSubmenu() {
		remove_submenu_page( 'individole', 'individole' );
	}
	
	function removeLineBreaks($string, $replace_with=" ") {
		$replacer = '#####';
		
		$string 		= preg_replace( "/\r|\n/", $replacer, $string );
		$string 		= str_replace('<br>', $replacer, $string);
		$string 		= str_replace('<br />', $replacer, $string);
		
		$string 		= str_replace(" #####", "#####", $string);
		$string 		= str_replace("##### ", "#####", $string);
		
		$string 		= str_replace($replacer.$replacer.$replacer.$replacer.$replacer.$replacer, $replacer, $string);
		$string 		= str_replace($replacer.$replacer.$replacer.$replacer.$replacer, $replacer, $string);
		$string 		= str_replace($replacer.$replacer.$replacer.$replacer, $replacer, $string);
		$string 		= str_replace($replacer.$replacer.$replacer, $replacer, $string);
		$string 		= str_replace($replacer.$replacer, $replacer, $string);
		
		$string 		= str_replace($replacer, $replace_with, $string);
		
		return $string;
	}
	
	function removeLinksFromImages($string) {
		 $pattern = '/<a(.*?)><img(.*?)><\/a>/i';
		 $replacement = '<img$2 />';
		 return preg_replace($pattern, $replacement, $string);
	}
	
	function removeMetaBoxesMenus($columns) {
		remove_meta_box('add-post-type-page', 'nav-menus', 'side');
		remove_meta_box('add-post-type-post', 'nav-menus', 'side');
		remove_meta_box('add-category', 'nav-menus', 'side');
		remove_meta_box('pll_lang_switch_box', 'nav-menus', 'side');
		
		return $columns;
	}
	
	function removeMetaBoxesPost() {
		if(/* property_exists($this, "screen") &&  */$this->screen()->base == "post") {
			//$this->debug($this->screen()->base);
			//$this->debug($this->screen()->post_type);
			if(!$this->isSuperAdmin()) {
				remove_meta_box('rocket_post_exclude', $this->screen()->post_type,'side');
			}
			
			remove_meta_box('wpbitly-meta', $this->screen()->post_type,'side');
			//remove_meta_box('postimagediv', $this->screen()->post_type,'side');
			remove_meta_box('commentsdiv', $this->screen()->post_type,'normal');
			remove_meta_box('members-cp', $this->screen()->post_type,'advanced');
			remove_meta_box('icl_div_config', $this->screen()->post_type,'normal');
		}
	}
	
	function removeOembedProviders() {
		// return array();
		
		$file = ABSPATH . WPINC . '/class-wp-oembed.php';
		if(file_exists($file)) {
			require_once($file);
			
		} else {
			require_once( ABSPATH . WPINC . '/class-oembed.php' );
		}
		
		$oembed = _wp_oembed_get_object(); /* schließt auch benutzerdefinierte mit ein */
		$providers = $oembed->providers;
		 
		//$this->debug($oembed, 1);
		 
		foreach($providers as $url => $provider_data) {
			wp_oembed_remove_provider($url);
			  
			//$this->debug($provider_data, 1);
		}
		 
		return array();
	}
	
	function removeParagraphsFromShortcodes($content){
		if(is_admin()) {
			return $content;
		}
		
		$array = array (
			'<p>{' 		=> '{',
			'}</p>' 		=> '}',
			'}<br />' 	=> '}',
			
			']<br />' 	=> ']',
			']<br>' 		=> ']',
		);
		
		// $this->debug($array);
		// $this->debug('<textarea>'.$content.'</textarea>');
		
		$tags = array('p', 'h1', 'h2', 'h3', 'h4');
		$sub_tags = array('b', 'i', 'strong', 'em', 'u');
		foreach($tags AS $tag) {
			$array['<'.$tag.'>  ['] = '[';
			$array['<'.$tag.'> ['] = '[';
			$array['<'.$tag.'>['] = '[';
			
			$array[']  </'.$tag.'>'] = ']';
			$array['] </'.$tag.'>'] = ']';
			$array[']</'.$tag.'>'] = ']';
			
			foreach($sub_tags AS $sub_tag) {
				$array['<'.$tag.'><'.$sub_tag.'>  ['] = '[';
				$array['<'.$tag.'><'.$sub_tag.'> ['] = '[';
				$array['<'.$tag.'><'.$sub_tag.'>['] = '[';
				
				$array[']  </'.$sub_tag.'></'.$tag.'>'] = ']';
				$array['] </'.$sub_tag.'></'.$tag.'>'] = ']';
				$array[']</'.$sub_tag.'></'.$tag.'>'] = ']';
			}
		}
		
		$content = strtr($content, $array);
		
		// $this->debug('<textarea>'.$content.'</textarea>');
		
		return $content;
	}
	
	function removeSession() {
		session_unset();
	}
	
	function removeEmptySpaces($text, $empty_tags = array("strong", "b", "em", "h1", "h2", "h3", "h4", "h5", "p", "span", "ol", "ul", "li")) {
		$text = trim((string) $text);
		//$text = preg_replace('~(?<= ) ~', '\xc2\xa0', $text);
		//$text = force_balance_tags($text);
		
		$text = str_replace(array("<br> </b>","<br/> </b>" ,"<br /> </b>"), "<br>%20</b>", $text);
		
		foreach($empty_tags AS $empty_tag) {
			//$this->debug($empty_tag);
			$text = trim(preg_replace( '#<'.$empty_tag.'[^>]*>\s*+(<br\s*/*>)?\s*</'.$empty_tag.'>#i', '', $text));
			$text = trim(preg_replace( '~\s?<'.$empty_tag.'[^>]*>(\s| |&nbsp;)+</'.$empty_tag.'>\s?~', '', $text));
			
			$text = trim(preg_replace( '/(.*)(<'.$empty_tag.'[^>]+>$)/s', '$1', $text));
			$text = trim(preg_replace( '/(^<\/'.$empty_tag.'>)(.*)/s', '$2', $text));
		}
		
		$text = str_replace("%20", " ", $text);
		
		return $text;
	}
	
	function removeScripts() {
		if($this->isFrontendGrid()) {
			$scripts = array(
				
			);
			
		} else {
			$scripts = array(
				'jquery-ui-core',
				'jquery_ui',
				'plupload',
				'plupload-all',
			);
		}
		
		foreach($scripts AS $script) {
			wp_dequeue_script($script);
			wp_deregister_script($script);
		}
	}
	
	function removeServiceWorker() {
		if(isset($_SESSION['serviceworker'])) {
			unset($_SESSION['serviceworker']);
		}
	}
	
	function removeSpaceBug($str) {
		return preg_replace('~(?<= ) ~', '\xc2\xa0', $str);
	}

	function removeStyles() {
		if(!$this->isFrontendGrid()) {
			wp_deregister_style('aio-tree');
			wp_deregister_style('aio-tree-theme-wordpress');
			wp_deregister_style('ate-status-bar');
			wp_deregister_style('buttons');
			wp_deregister_style('fontawesome');
			wp_deregister_style('font-awesome');
			wp_deregister_style('font-awesome-fa');
			wp_deregister_style('imgareaselect');
			wp_deregister_style('jquery-tooltipster');
			wp_deregister_style('mediaelement');
			wp_deregister_style('rml-font');
			wp_deregister_style('rml-main-style');
			wp_deregister_style('rml-sweetalert');
			wp_deregister_style('wp-media-picker');
			wp_deregister_style('wp-block-library');
			wp_deregister_style('wpml-tm-admin-bar');
			
			if(!$this->isSuperAdmin()) {
				wp_deregister_style('dashicons');
			}
		}
	}
	
	function removeURLParameterFromString($string, $toRemove="") {
		if(!is_array($toRemove)) {
			$toRemove = array($toRemove);
		}
		
		parse_str($string, $queryParams);
		
		foreach ($toRemove as $param) {
			unset($queryParams[$param]);
		}
		
		$return = http_build_query($queryParams);
		
		return $return;
	}
	
	function removeURLParameter($url, $toRemove="") {
		$url_base = strtok($url, "?");
		
		$parsed = [];
		parse_str(substr($url, strpos($url, '?') + 1), $parsed);
		
		if(!is_array($toRemove)) {
			$toRemove = array($toRemove);
		}
		
		foreach($toRemove AS $parameter_to_remove) {
			if(isset($parsed[$parameter_to_remove])) {
				unset($parsed[$parameter_to_remove]);
			}
		}
		
		if(!empty($parsed)) {
			$url_base .= '?' . http_build_query($parsed);
		}
		
		return $url_base;
	}
	
	function removeWBR($content) {
		$result = str_replace("<wbr>", "", trim((string) $content));
		return $result;
	}
	
	function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
	
	function removeWPHead() {
		remove_action('wp_head', 					'_admin_bar_bump_cb');
		remove_action('wp_head',					'rel_canonical');
		remove_action('wp_head',					'feed_links_extra', 3);
		remove_action('wp_head',					'feed_links', 2);
		remove_action('wp_head',					'rsd_link');
		remove_action('wp_head',	  				'rest_output_link_wp_head');
		remove_action('wp_head',	  				'wp_oembed_add_discovery_links');
		remove_action('template_redirect', 			'rest_output_link_header', 11, 0);
		remove_action('wp_head',					'wlwmanifest_link');
		remove_action('wp_head',					'index_rel_link');
		remove_action('wp_head',					'parent_post_rel_link', 10, 0);
		remove_action('wp_head',					'next_post_rel_link', 10, 0);
		remove_action('wp_head',					'previous_post_rel_link', 10, 0);
		remove_action('wp_head',					'start_post_rel_link', 10, 0);
		remove_action('wp_head',					'adjacent_posts_rel_link_wp_head', 10, 0);
		remove_action('wp_head',					'wp_generator');
		remove_action('wp_head', 					'meta_generator_tag', 20);
		remove_action('wp_head',					'generator');
		remove_action('wp_head',					'plupload', 10, 0);
		remove_action('wp_head',					'wp_shortlink_wp_head');
		remove_action('wp_head',					'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts',		'print_emoji_detection_script');
		remove_action('wp_print_styles',			'print_emoji_styles');
		remove_action('admin_print_styles',			'print_emoji_styles');
		remove_filter('the_content_feed',			'wp_staticize_emoji');
		remove_filter('comment_text_rss', 			'wp_staticize_emoji');
		remove_filter('wp_mail', 					'wp_staticize_emoji_for_email');
		add_filter('tiny_mce_plugins', 				array($this, 'disable_emojis_tinymce'));
		add_filter('the_generator', 				'__return_false');
		
		if(!$this->isDennis()) {
			add_filter('show_admin_bar',				'__return_false');
		}
		
		if (!empty($GLOBALS['sitepress'])) {
			remove_action(
				current_filter(),
				array ( $GLOBALS['sitepress'], 'meta_generator_tag', 20 )
			);
		}
	}
	
	function removeWPMLGeneratorTag() {
		return false;
	}
	
	function replaceBrackets($args, $args2=array()) {
		if(is_string($args) && !empty($args2)) {
			$args = array(
				'text'		=> $args,
				'replace'	=> $args2,
			);
		}
		
		if(isset($args['replace']) && isset($args['text'])) {
			preg_match_all('/[\{\[]([^\}\]]*)[\}\]]/', $args['text'], $matches);
			
			//$this->debug($matches);
			
			$return = $args['text'];
			if(isset($matches[1]) && !empty($matches[1])) {
				foreach($matches[1] AS $var) {
					if(isset($args['replace'][$var])) {
						$return = str_replace(array('{'.$var.'}', '['.$var.']'), $args['replace'][$var], $return);
					}
				}
			}
			
			$return = $this->trimIfString($return);
			
		} else if(isset($args['text'])) {
			$return = $args['text'];
		}
		
		return $return;
	}
	
	function replaceStrings($content) {
		$obj = new individole_replace_strings();
		return $obj->create($content);
	}
	
	function replaceUppercase($content) {
		$strings = $this->io("replace_uppercase");
		
		if($strings != "") {
			$strings = explode("\n", $strings);
			foreach($strings AS $string) {
				$string 	= trim((string) $string);
				$new_string = $string;
				$new_string = strtolower((string) $new_string);
				$new_string = ucwords($new_string);
				
				// $this->debug($string.' --- '.$new_string);
				
				// $content = preg_replace('/([^<>]*?)'.$string.'(?=[^>]+?<)/', '<span class="uppercase">'.$new_string.'</span>', $content);
				
				$content = preg_replace('/'.$string.'/', '<span class="uppercase auto">'.$new_string.'</span>', $content);
			}
		}
		
		return $content;
	}
	
	function rgb2hex($rgb) {
		return '#' . sprintf('%02x', $rgb[0]) . sprintf('%02x', $rgb[1]) . sprintf('%02x', $rgb[2]);
	}
	
	function sanitizeFileName($filename) {
		global $config_cpt;
		
		if(isset($_REQUEST['_acfuploader']) && isset($_REQUEST['post_id'])) {
			$p = get_post($_REQUEST['post_id']);
			
			if(isset($config_cpt[$p->post_type]) && isset($config_cpt[$p->post_type]['disable_sanitize'])) {
				$_SESSION['dennis'] = 2;
				
				return $filename;
			}
		}
		
		$filename = strtr($filename, $this->normalizeChars);
		
		$filename = str_replace( $this->umlaut_chars['space'], "-", $filename );
		$filename = str_replace( $this->umlaut_chars['remove'], "", $filename );
		$filename = str_replace( $this->umlaut_chars['ecto'], $this->umlaut_chars['perma'], $filename );
		$filename = str_replace( $this->umlaut_chars['ecto2'], $this->umlaut_chars['perma'], $filename );
		$filename = str_replace( $this->umlaut_chars['in'], $this->umlaut_chars['perma'], $filename );
		$filename = str_replace( $this->umlaut_chars['html'], $this->umlaut_chars['perma'], $filename );
		
		$filename = trim((string) $filename, "_");
		//$filename = strtr($filename, $this->normalizeChars);
		
		// Win Livewriter sends escaped strings
		$filename = html_entity_decode( $filename, ENT_QUOTES, 'UTF-8' );
		// Strip HTML and PHP tags
		$filename = strip_tags( $filename );
		// Preserve escaped octets.
		$filename = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $filename );
		// Remove percent signs that are not part of an octet.
		$filename = str_replace( '%', '', $filename );
		// Restore octets.
		$filename = preg_replace( '|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $filename );
		
		$filename = remove_accents( $filename );
		
		if ( seems_utf8( $filename ) ) {
			
			if ( function_exists( 'mb_strtolower' ) )
				$filename = mb_strtolower( $filename, 'UTF-8' );
			
			//$filename = utf8_uri_encode( $filename, 200 );
		}
		
		$filename = strtolower( $filename );
		$filename = preg_replace( '/&.,+?;/', '', $filename ); // kill entities
		$filename = preg_replace( '/\s+/', '-', $filename );
		$filename = preg_replace( '|-+|', '-', $filename );
		$filename = trim( $filename, '-' );
		$filename = urlencode((string) $filename);
		
		return $filename;
	}
	
	function sanitizePermalink($title, $raw_title = NULL, $context = NULL ) {
		if ( ! is_null( $raw_title ) ) {
			$title = $raw_title;
		}
		
		if(function_exists('getPinyinSlug')) {
			$title = getPinyinSlug($title);
		}
		
		if(seems_utf8( $title ) ) {
			$invalid_latin_chars = array(
				chr(197).chr(146) => 'oe', chr(197).chr(147) => 'oe', chr(197).chr(160) => 's',
				chr(197).chr(189) => 'z', chr(197).chr(161) => 's', chr(197).chr(190) => 'z',
				// Euro Sign €
				chr(226).chr(130).chr(172) => 'EUR',
				// GBP (Pound) Sign £
				chr(194).chr(163) => 'GBP'
			);
			// use for custom strings
			$invalid_latin_chars = apply_filters( 'de_de_latin_char_list', $invalid_latin_chars );
			//$title = $this->utf8_decode( strtr( $title, $invalid_latin_chars) );
			$title = strtr( $title, $invalid_latin_chars);
		}
		
		$title = str_replace( $this->umlaut_chars['ecto'], $this->umlaut_chars['perma'], $title );
		//$title = str_replace( $this->umlaut_chars['in'], $this->umlaut_chars['perma'], $title );
		$title = str_replace( $this->umlaut_chars['html'], $this->umlaut_chars['perma'], $title );
		$title = str_replace( $this->umlaut_chars['remove'], "", $title);
		$title = remove_accents( $title );
		$title = str_replace(array(':', '/', '  ', ' ', '—', '---', '--', ' - ', ' – ', '•'), '-', $title );
		$title = sanitize_title_with_dashes( $title );
		$title = str_replace('%e2%80%a8', '', $title);
		$title = str_replace(array(':', 'LINEBREAK'), '-', $title );
		
		return $title;
	}
	
	function sanitizeTitle($title, $raw_title = NULL, $context = NULL) {
		// return $title;
		
		return $this->sanitizePermalink($title);
	}
	
	function save404() {
		$obj = new individole_save_404();
		return $obj->save();
	}
	
	function savePDFTextToMeta($attachment_id) {
		$obj = new individole_save_pdf_text_to_meta();
		return $obj->create($attachment_id);
	}
	
	function savePost($post_id, $p) {
		if($p->post_type != "nav_menu_item") {
			// $this->debug($post_id, 2);
			// $this->debug($p, 2);
			
			if($p->post_status == "draft" || $p->post_status == "publish") {
				global $config_cpt;
				
				if(isset($config_cpt[$p->post_type]['newsletter'])) {
					$newsletter = new individole_misc_newsletter();
					$newsletter->send_test_mail($p);
					$newsletter->create_campaign($p);
				}
			}
			
			$this->acfPMUpdateData($post_id, $p);
			$this->forceFacebookScrape($post_id);
			$this->wpmlCreatePostmetaSync($post_id, $p);
			$this->savePostAPC($p);
		}
	}
	
	function savePostAPC($p) {
		if(is_numeric($p)) {
			$p = get_post($p);
		}
		
		if($p) {
			if($p->post_type != "nav_menu_item") {
				$this->deleteAPCDataPrefix('iphcpt_'.$p->post_type);
				$this->deleteAPCData('i_cm_cpt_'.$p->post_type.'_p'.$p->post_parent);
				$this->deleteAPCData('i_cm_cpt_'.$p->post_type.'_p'.$p->ID);
			}
		}
	}
	
	function savePostAttachment($post_id) {
		$this->update_option("savePostAttachment", 1);
		
		if(isset($_SESSION["modify_upload_month"]) && !empty($_SESSION["modify_upload_month"])) {
			$this->update_option("savePostAttachment", 2);
			
			$upload_folder = explode('/', $_SESSION["modify_upload_month"]);
			
			$new_date = $upload_folder[1].'-'.$upload_folder[2].'-01 08:00:00';
			
			$this->update_option("savePostAttachment", $new_date);
			
			$q = '
			UPDATE
				'.TABLE_PREFIX.'posts
			SET
				post_date = "'.$new_date.'",
				post_date_gmt = "'.$new_date.'",
				post_modified = "'.$new_date.'",
				post_modified_gmt = "'.$new_date.'"
			WHERE
				ID = '.$post_id.'
			';
			
			$this->update_option("savePostAttachment", $q);
			
			mysqli_query($this->mysql, $q);
		}
	}
	
	function screen() {
		//include_once(ABSPATH . "wp-admin/includes/screen.php");
		
		global $current_screen;
		
		//$this->debug($current_screen);
				
		return $current_screen;
	}

	function searchJoin( $join ) {
		global $wpdb;
		
		if(!is_admin() && is_search()) {
			$join .=' LEFT JOIN '.$wpdb->postmeta. ' pm ON '. $wpdb->posts . '.ID = pm.post_id ';
		}
		
		return $join;
	}
	
	function searchSelect( $select ) {
		global $wpdb;
		
		if(!is_admin() && is_search()) {
			$select .=', pm.meta_value';
		}
		
		return $select;
	}
	
	function searchWhere( $where ) {
		global $pagenow, $wpdb;
		
		if(!is_admin() && is_search()) {
			$where = preg_replace("/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/", "(".$wpdb->posts.".post_title LIKE $1) OR (pm.meta_value LIKE $1)", $where );
		}
		
		return $where;
	}
	
	function searchGroupBy( $groupby ) {
		global $pagenow, $wpdb;
		
		if(!is_admin() && is_search()) {
			$groupby = $wpdb->posts.'.ID';
		}
		
		return $groupby;
	}
	
	function searchDistinct( $where ) {
		global $wpdb;

		if(!is_admin() && is_search()) {
			return "DISTINCT";
		}
		
		return $where;
	}
	
	function sendMail($args) {
		$obj = new individole_send_mail($args);
		return $obj->create();
	}
	
	function sendMicrotime() {
		//$this->debug($this->microtime);
		
		if($this->io("debug_microtime") == 1) {
			$microtime_min = reset($this->microtime);
			$microtime_min = str_replace(",", ".", $microtime_min);
			
			$this->microtimes = array();
			foreach($this->microtime AS $k => $v) {
				$v = str_replace(",", ".", $v);
				$v = ($v - $microtime_min);
				
				$debug_microtime_content[] = '<tr><th>'.$k.'</th><td>'.$v.'</td></tr>';
				
				$this->microtimes[$k] = $v;
			}
			
			$microtime_max = end($this->microtimes);
			$microtime_max = str_replace(",", ".", $microtime_max);
			
			$debug_microtime_content = array();
			foreach($this->microtimes AS $k => $v) {
				$debug_microtime_content[] = '<tr><th nowrap>'.$k.'</th><td>'.($v / 1000).'</td></tr>';
			}
			
			$debug_microtime_content = array_reverse($debug_microtime_content);
			$debug_microtime_content = '<table class="table_default">'.implode("", $debug_microtime_content).'</table>';
			
			if($this->io("debug_microtime_mail") == 1 && $microtime_max > $this->io("debug_microtime_mail_min_time")) {
				$array_sendmail = array(
					'html'				=> true,
					'to_name'			=> 'Dennis Kather',
					'to_mail'			=> 'debug@denniskather.de',
					'subject'			=> 'Debug microtime / '.$microtime_max.' / '.$this->getRealSiteURL(),
					'text_html'		=> $this->createMailBody(array(
						'content'		=> $debug_microtime_content,
					)),
				);
				
				$this->sendMail($array_sendmail);
				
			} else {
				if($this->isDennis() && ($this->io("debug_microtime_force") == 1 || $microtime_max > $this->io("debug_microtime_mail_min_time"))) {
					$this->debug('microtime duration: '.$microtime_max.'<br>microtime limit: '.$this->io("debug_microtime_mail_min_time"));
					$this->debug($debug_microtime_content);
				}
			}
		}
	}
	
	function setACFSettings() {
		acf_update_setting('google_api_key', $this->io("google_api_key"));
		acf_update_setting('save_json', $this->acfJsonSavePoint());
	}
	
	function setACFLabel($key) {
		$obj = new individole_set_acf_label();
		return $obj->create($key);
	}
	
	function setAPCData($apc_key, $apc_data, $apc_time=3600, $urldecode=false, $prevent_apcu=0) {
		$apc_key = $this->formatAPCKey($apc_key);
		
		if($urldecode == true) {
			//$apc_data = urlencode_deep($apc_data);
		}
		$apc_data = urlencode_deep($apc_data);
		
		//$_SESSION['individole_apc'][$apc_key] = $apc_data;
		
		if($this->isAPC()) {
			$type = 'APC';
			apc_store($apc_key, $apc_data, (int)$apc_time);
			
		} else if($this->isAPCU() && $prevent_apcu == 0) {
			$type = 'APCU';
			apcu_store($apc_key, $apc_data, (int)$apc_time);
						
		} else {
			$type = 'TRANSIENT';
			set_transient($apc_key, $apc_data, (int)$apc_time);
		}
		
		//$this->debug('setAPCData<br>&#8627; <b>'.$type.'</b><br>&#8627; ('.$apc_key.' / '.strlen((string) $apc_key).')');
		
		//$this->debug_apc_set[$type][$apc_key] = $apc_data;
		//$this->debug($type);
	}
	
	function setAdminPostType($query) {
		$query->set( 'post_type', 'any');
		
		return $query;
	}
	
	function setAttachmentTitleRemember($file) {
	 	global $current_user;
		
	 	$file_parts = pathinfo( 'a'.$file['name'] );
		update_option('individole_set_attachment_title_user_'.$current_user->ID, substr($file_parts['filename'], 1), false);
		
		update_option("individole_filename", $file);
		
		return $file;
	}
	
	function setAttachmentTitleSave($attachment_id) {
		update_option("setAttachmentTitleSave", $_POST);
		
		// return;
		
		global $current_user;
		
		$title = '';
		if(isset($_FILES["userfile"]["tmp_name"]) && isset($_POST["replace_type"]) && $_POST["replace_type"] == "replace_and_search") {
			$replace_type = $_POST["replace_type"];
			
			$file_parts = pathinfo('a'.$_FILES["userfile"]["name"]);
			
			$attachment_id = $_POST['ID'];
			$title = substr($file_parts['filename'], 1);
		}
		
		update_option("setAttachmentTitleSave", $title);
		
		if($title != "") {
			wp_update_post( array(
				'ID' 				=> $attachment_id,
				'post_title' 	=> $title,
				'post_excerpt' => $title,
			));
			
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
			
			//update_option('individole_set_attachment_title_user_'.$current_user->ID, "1", false);
		}
	}
	
	function setBackendFlexibleModulesSettings() {
		$obj = new individole_set_backend_flexible_modules_settings();
		return $obj->create();
	}
	
	function setBackendToolsMenu() {
		$obj = new individole_set_backend_tools_menu();
		return $obj->create();
	}
	
	function setBackendToolsMenuSubmenu() {
		
	}
	
	function setBackendToolsOutput() {
		$obj = new individole_set_backend_tools_output();
		return $obj->create();
	}
	
	function setBackendToolsSettings() {
		$obj = new individole_set_backend_tools_settings();
		return $obj->create();
	}
	
	function setCookieBot() {
		if($this->io("cookie_bot_id") != "") {
			$this->inline_scripts_include_header[] = '<script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="'.$this->io("cookie_bot_id").'" type="text/javascript" async></script>';
		}
	}
	
	function setCustomAdminCSS() {
		if($this->isSuperAdmin() && isset($_POST['ucHTML'])) {
			// $this->debug('<textarea>'.$_POST['ucHTML'].'</textarea>', 2);
			update_option('underConstructionHTML', $_POST['ucHTML']);
			
			echo '<script>window.location.href = window.location;</script>';
			
			// header("Location: /wp-admin/index.php?page=under-construction");
		}
		
		$obj = new individole_set_custom_admin_css();
		return $obj->create();
	}
	
	function setCustomAdminJavascript() {
		$obj = new individole_set_custom_admin_javascript();
		return $obj->create();
	}
	
	function setDNSPrefetch() {
		$obj = new individole_set_dns_prefetch();
		return $obj->create();
	}
	
	function setFacebookPixel() {
		$obj = new individole_set_facebook_pixel();
		return $obj->create();
	}
	
	function setFacebookInit() {
		$obj = new individole_set_facebook_init();
		return $obj->create();
	}
	
	function setFix100VHData($classes_to_be_substracted="") {
		return ' data-100vh="'.$classes_to_be_substracted.'"';
	}
	
	function setFooterJavascript() {
		$obj = new individole_set_footer_scripts();
		return $obj->create();
	}
	
	function setGoogleAnalytics() {
		$obj = new individole_set_google_analytics();
		return $obj->create();
	}
	
	function setGoogleAnalyticsProperty() {
		$this->updateLogStatus();
		
		$set_ga_stats_profile = 0;
		if(isset($_POST['set_ga_stats_profile']) && ($_POST['set_ga_stats_profile'] == 3 || $_POST['set_ga_stats_profile'] == 4)) {
			$set_ga_stats_profile = $_POST['set_ga_stats_profile'];
			
		} else if(isset($_GET['set_ga_stats_profile']) && ($_GET['set_ga_stats_profile'] == 3 || $_GET['set_ga_stats_profile'] == 4)) {
			$set_ga_stats_profile = $_GET['set_ga_stats_profile'];
		}
		
		if($set_ga_stats_profile > 0) {
			$ga_stats_options = $this->get_option('individole_google');
			$ga_stats_options['google_analytics_version'] = $set_ga_stats_profile;
			
			$ga_stats_options = serialize($ga_stats_options);
			// $this->debug($ga_stats_options);
			
			update_option('individole_google', $ga_stats_options);
		}
	}
	
	function setGoogleConversion() {
		$obj = new individole_set_google_conversion();
		return $obj->create();
	}
	
	function setGooglePhoneTracking() {
		$obj = new individole_set_google_phone_tracking();
		return $obj->create();
	}
	
	function setGoogleMaps($args=array()) {
		$obj = new individole_set_google_maps();
		return $obj->create($args);
	}
	
	function setGoogleTagManager($body=1) {
		$obj = new individole_set_google_tagmanager();
		return $obj->create($body);
	}
	
	function setHeaderCSS($args) {
		$obj = new individole_set_header_css($args);
		return $obj->create();
	}
	
	function setHeaderHTML($args=array()) {
		$return = array();
		
		$html_data = array();
		$html_data[] = 'xmlns="http://www.w3.org/1999/xhtml"';
		$html_data[] = 'lang="'.$this->l.'"';
		$html_data[] = 'xml:lang="'.$this->l.'"';
		
		if(isset($args['html_data'])) {
			$html_data[] = $args['html_data'];
		}
		
		// $this->debug($html_data);
		
		//$return[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		$return[] = '<!DOCTYPE html>';
		$return[] = '<html '.implode(" ", $html_data).'>';
		
		if(isset($args['head'])) {
			$return[] = '<head>';
			$return[] = $this->setHeaderMeta(array());
			
			if($args['head'] !== 1 && $args['head'] !== true) {
				$return[] = $args['head'];
			}
			
			$return[] = '</head>';
		}
		
		if(isset($args['body'])) {
			if(is_array($args['body'])) {
				$return[] = '<body class="'.$this->createBodyClasses($args['body']).'">';
			
			} else {
				$return[] = '<body class="'.$this->createBodyClasses().'">';
			}
		}
		
		return implode("", $return);
	}
	
	function setHeaderIcons() {
		$obj = new individole_set_header_icons();
		return $obj->create();
	}
	
	function setHeaderMeta($args) {
		$obj = new individole_set_header_meta($args);
		return $obj->create();
	}
	
	function setJPEGQuality() {
		$quality = $this->io("image_quality");
			
		if(is_numeric($quality) && $quality > 0) {
			return $quality;
				
		} else {
			return 85;
		}
	}
	
	function setLibraryColumns($cols) {
		//$this->debug($cols);
		
		//unset($cols['author']);
		//unset($cols['date']);
		//unset($cols['comments']);
		//unset($cols['parent']);
		
		if(!$this->isSuperAdmin()) {
			unset($cols['ewww-image-optimizer']);
		}
		
		$cols["misc"] = "Misc";
		return $cols;
	}
	
	function setLibraryColumnsValue($column_name, $id) {
		$obj = new individole_set_library_columns_value();
		return $obj->create($column_name, $id);
	}
	
	function setLinkExternal($link) {
		if($this->isLinkExternal($link)) {
			return ' target="_blank" rel="noopener" ';
		}
	}
	
	function setLinkedInTag() {
		$obj = new individole_set_linkedin_tag();
		return $obj->create();
	}
	
	function setTrackingServices() {
		if(!$this->isAdmin() || $this->io("debug_tracking") == 1) {
			$this->setMatomo();
			$this->setLinkedInTag();
			$this->setGoogleAnalytics();
			$this->setGoogleTagManager(0);
			$this->setGoogleConversion();
			$this->setGooglePhoneTracking();
			$this->setFacebookPixel();
		}
	}
	
	function setLocales($language="") {
		// $this->debug('?'.$this->l, 2);
		
		$array = array(
			'de' => 'de_DE.UTF-8',
			'en' => 'en_GB.UTF-8',
			'fr' => 'fr_FR.UTF-8',
			'sv' => 'sv_SE.UTF-8',
			'pt' => 'pt_PT.UTF-8',
			'no' => 'no_NO.UTF-8',
			'zh' => 'zh_CN.UTF-8',
			'tr' => 'tr_TR.UTF-8',
			'es' => 'es_ES.UTF-8',
			'ru' => 'ru_RU.UTF-8',
			'nl' => 'nl_NL.UTF-8',
		);
		
		if(isset($array[$language])) {
			$result = $array[$language];
			
		} else if(isset($array[$this->l])) {
			$result = $array[$this->l];
			
		} else {
			$result = $array["en"];
		}
				
		// setlocale(LC_ALL, $result);
		
		setlocale(LC_COLLATE, $result);
		setlocale(LC_CTYPE, $result);
		setlocale(LC_MONETARY, $result);
		setlocale(LC_NUMERIC, $result);
		setlocale(LC_TIME, $result);
		setlocale(LC_MESSAGES, $result);
		//
		// setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');
		
		// $this->debug($result);
		
		return $result;
	}
	
	function setMatomo() {
		$obj = new individole_set_matomo();
		return $obj->create();
	}
	
	function setNewFacebook() {
		
	}
	
	function setObjectID($id) {
		if($this->isAdmin() && (!isset($GLOBALS['hide_frontpage_edit']) || (isset($GLOBALS['hide_frontpage_edit']) && $GLOBALS['hide_frontpage_edit'] != 1))) {
			return 'id="individole_'.$this->i_module.'_'.$id.'"';
		}
	}
	
	function setObjectPositionAbsolute($atts = [], $content = null, $tag = "") {
		return '<div class="absolute-'.$tag.'">'.$content.'</div>';
	}
	
	function setPostStatus() {
		if($this->isAdmin()){
			if(isset($_SESSION['individole_hide_draft'])) {
				$return = array('publish');
				
			} else {
				$return = array('draft', 'publish', 'future', 'pending');
			}
			
		} else {
			$return = array('publish');
		}
		
		return $return;
	}
	
	function setPostStatusSearch() {
		$return = $this->setPostStatus();
		array_push($return, "inherit");
		
		return $return;
	}
	
	function setPreload() {
		$obj = new individole_set_preload();
		return $obj->create();
	}
	
	function setPostIDUploadDirectory($args) {
		$obj = new individole_set_post_id_upload_directory();
		return $obj->create($args);
	}
	
	function setProtectedCPTUploadDirectory($args) {
		$obj = new individole_set_protected_upload_directory();
		return $obj->create($args);
	}
	
	function setSearchExact($query){
		$query->set('exact', true);
	}
	
	function setSessionSuperAdmin() {
		if(is_user_logged_in()) {
			global $current_user;
			
			if(isset($current_user->caps['administrator']) && $current_user->caps['administrator'] == 1) {
				$_SESSION['superadmin'] = array(
					'id' 		=> $current_user->data->ID,
					'user' 	=> md5($current_user->data->user_login)
				);
				
			} else {
				return false;
			}
		
		} else {
			return false;
		}
	}
	
	function setTinyMCEExternalPlugins( $plugin_array ) {
		global $tinymce_version;
		
		$base_path = $this->path['libraries'].'/_tinymce_plugins';
		$base_path_abs = $this->url['libraries'].'/_tinymce_plugins';
		
		$plugins = array(
			'individole_tab',
			'individole_nbsp',
			'image',
			//'imagetools',
			'searchreplace',
			'table',
			'charwordcount',
		);
		
		if($this->io("ai_service") != "") {
			$plugins[] = 'ai';
		}
			
		if($this->io("typekit_fonts") != "") {
			$plugins[] = 'typekit';
		}
		
		foreach($plugins AS $plugin) {
			$plugin_array[$plugin] = $base_path_abs.'/'.$plugin.'/plugin.min.js?t='.filemtime($base_path.'/'.$plugin.'/plugin.min.js');
		}
		
		//if($this->isSuperAdmin()) {
		//	update_option("individole_setTinyMCEExternalPlugins", $plugin_array);
		//}
				
		return $plugin_array;
	}
	
	function setTinyMCEFormats($settings, $editor_id) {
		$obj = new individole_set_tinymce_formats();
		return $obj->create($settings, $editor_id);
	}
	
	function setTinyMCEFormatsDefaults() {
		$obj = new individole_set_tinymce_formats_defaults();
		return $obj->create();
	}
	
	function setTinyMCEPaste($in) {
		return $in;
	}
	
	function setTinyMCEPasteSVG() {
		echo '<style>
			svg, img[src*=".svg"] {
				min-width: 150px !important;
				min-height: 150px !important;
			}
		</style>';
	}
	
	function setUploadPath() {
		// $current_upload_path = get_option("upload_path");
		//
		// if($current_upload_path != "") {
		// 	if(isset($_GET['set_upload_paths'])) {
		// 		$host_names = explode(".", $_SERVER['HTTP_HOST']);
		// 		$host = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
		//
		// 		update_option("upload_path", str_replace("/wordpress", "/images", ABSPATH));
		// 		update_option("upload_url_path", $this->getHTTPScheme()."images.".$host);
		// 	}
		//
		// 	add_settings_section('upload_path_settings_section',
		// 		'Individol&eacute; / Upload Path',
		// 		array($this, 'setUploadPathSettings'),
		// 		'media');
		// }
	}
	
	function setUploadPathSettings() {
		$current_upload_path = get_option("upload_path");
		$current_upload_url_path = get_option("upload_url_path");
		
		if($current_upload_path != "") {
			echo '
				<div>upload_path: '.$current_upload_path.'</div>
				<div style="margin-bottom: 15px;">upload_url_path: '.$current_upload_url_path.'</div>
				<a href="?set_upload_paths=1" type="submit" class="button button-primary">SET INDIVIDOLE PATHS (as images-subdomain)</a>
			';
		}
	}
	
	function setWPHead() {
		$obj = new individole_set_header_scripts();
		return $obj->create();
	}
	
	function setWPRocketNeverCache() {
		if(isset($_GET['page']) && $_GET['page'] == 'wprocket' && function_exists('get_rocket_option')) {
			$current = get_rocket_option('cache_reject_uri');
			
			//$this->debug($current, 1);
			
			if(empty($current)) {
				update_rocket_option('cache_reject_uri', '/index.php(.*)');
			}
		}
	}
	
	function shopifyCreateBuyButton($shopify_id) {
		$obj = new individole_shopify_create_buy_button();
		return $obj->create($shopify_id);
	}
	
	function shopifyDoWebhook() {
		$obj = new individole_shopify_do_webhook();
		return $obj->create();
	}
	
	function showAllImageSizesDropdown( $sizes ) {
		$new_sizes = array();
		
		$added_sizes = get_intermediate_image_sizes();
		
		// $added_sizes is an indexed array, therefore need to convert it
		// to associative array, using $value for $key and $value
		foreach( $added_sizes as $key => $value) {
			$new_sizes[$value] = $value;
		}
		
		// This preserves the labels in $sizes, and merges the two arrays
		$new_sizes = array_merge( $new_sizes, $sizes );
		
		return $new_sizes;
	}
	
	function showSVGLibraryPreview($response, $attachment, $meta){
		$obj = new individole_show_svg_library_preview();
		return $obj->create($response, $attachment, $meta);
	}

	function sortArrayByKey(array $array, $key, $asc = true, $flag="", $keep_keys = true) {
		$obj = new individole_sort_array_by_key();
		return $obj->create($array, $key, $asc, $flag, $keep_keys);
	}
	
	function sortMultiArray($a,$subkey) {
		$obj = new individole_sort_multiarray();
		return $obj->create($a,$subkey);
	}
	
	function sortPostIDsByDate($post_ids, $args=array()) {
		$obj = new individole_sort_post_ids_by_date();
		return $obj->create($post_ids, $args);
	}
	
	function startsWith($string, $start_string) {
		$obj = new individole_misc_starts_with();
		return $obj->create($string, $start_string);
	}
		
	function removeHeaders( $headers ) {
		unset($headers['X-Pingback']);
		return $headers;
	}
	
	function stripArrayPraefix($array="", $strip="") {
		if(empty($array) || empty($strip)) {
			return $array;
		
		}
		
		$temp = array();
		foreach($array AS $k => $v) {
			$k = str_replace($strip, "", $k);
			$temp[$k] = $v;
		}
		
		return $temp;
	}
	
	function stripeGetCustomer() {
		$obj = new individole_stripe_get_customer();
		return $obj->create();
	}
	
	function stripeGetPaymentMethods() {
		$obj = new individole_stripe_get_payment_methods();
		return $obj->create();
	}
	
	function stripeCreatePayment($args) {
		$obj = new individole_stripe_create_payment();
		return $obj->create($args);
	}
	
	function stripeCreateForms($args) {
		$obj = new individole_stripe_create_forms();
		return $obj->create($args);
	}
	
	function stripeGetSettings($args=array()) {
		$this->stripe = $this->unserialize(get_option("individole_stripe"));
		
		$this->stripeGetKeys($args);
		
		return $this->stripe;
	}
	
	function stripeGetKeys($args=array()) {
		$obj = new individole_stripe_get_keys();
		return $obj->create($args);
	}
	
	function stripeIncludeScripts() {
		$scripts = array();
		
		if($this->io("stripe_version") == "v3") {
			$scripts[] = '<script src="https://js.stripe.com/v3/"></script>';
			
		} else {
			$scripts[] = '<script src="https://checkout.stripe.com/checkout.js"></script>';
		}
		
		$scripts[] = '<script src="'.$this->version('/_individole/_libraries/_stripe/stripe.js').'"></script>';
				
		return implode("", $scripts);
	}
	
	function stripTagsContent($text, $tags = '', $invert = FALSE) {
		preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim((string) $tags), $tags);
		$tags = array_unique($tags[1]);
		
		if(is_array($tags) AND count($tags) > 0) {
			if($invert == FALSE) {
				return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
			} else {
				return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
			}
		
		} elseif($invert == FALSE) {
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
		
		return $text;
	}
	
	function tcpdfInit($args) {
		$obj = new individole_tcpdf_init();
		return $obj->create($args);
	}
	
	function tcpdfWriteHtml($pdf, $args) {
		$obj = new individole_tcpdf_write_html();
		return $obj->create($pdf, $args);
	}
	
	function termsOrderSet( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ($taxonomy != 'post_tag' || isset($_SESSION['updateTermOrder'])) {
			//unset($_SESSION['updateTermOrder']);
			return;
		}
		
		// Save in comma-separated string format - may be useful for MySQL sorting via FIND_IN_SET().
		update_post_meta( $object_id, 'individole_post_tags_order', implode( ',', $tt_ids ) );
	}
	
	// Reorder terms using our order meta.
	function termsOrderGet( $terms, $post_id, $taxonomy ) {
		if ( $taxonomy != 'post_tag' || ! $terms ) {
			return $terms;
		}
		if ( $ids = get_post_meta( $post_id, 'individole_post_tags_order', true ) ) {
			$ret = $term_idxs = array();
			// Map term_ids to term_taxonomy_ids.
			foreach ( $terms as $term_id => $term ) {
				$term_idxs[$term->term_taxonomy_id] = $term_id;
			}
			// Order by term_taxonomy_ids order meta data.
			foreach ( explode( ',', $ids ) as $id ) {
				if ( isset( $term_idxs[$id] ) ) {
					$ret[] = $terms[$term_idxs[$id]];
					unset($term_idxs[$id]);
				}
			}
			// In case our meta data is lacking.
			foreach ( $term_idxs as $term_id ) {
				$ret[] = $terms[$term_id];
			}
			return $ret;
		}
		return $terms;
	}
	
	function termsOrderEdit( $terms_to_edit, $taxonomy ) {
		global $post;
		if ( ! isset( $post->ID ) || $taxonomy != 'post_tag' || ! $terms_to_edit ) {
			return $terms_to_edit;
		}
		// Ignore passed in term names and use cache just added by terms_to_edit().
		if ( $terms = get_object_term_cache( $post->ID, $taxonomy ) ) {
			$terms = $this->termsOrderGet( $terms, $post->ID, $taxonomy );
			$term_names = array();
			foreach ( $terms as $term ) {
				$term_names[] = $term->name;
			}
			$terms_to_edit = esc_attr( join( ',', $term_names ) );
		}
		return $terms_to_edit;
	}
	
	function toggleDrafts() {
		if(isset($_GET['hide_drafts_on_frontend'])) {
			$_SESSION['individole_hide_draft'] = 1;
		
		} else if(isset($_GET['show_drafts_on_frontend'])) {
			unset($_SESSION['individole_hide_draft']);
		}
	}
	
	function translations() {
		if(!empty($this->translations)) {
			return $this->translations;
		}
		
		$iTranslate_file = $this->path['individole'].'/translations.txt';
		$translations = file_get_contents($iTranslate_file);
		$translations = explode("----------------------------------", $translations);
		//$this->debug($translations);
		
		foreach($translations AS $t) {
			$t = trim((string) $t);
			
			$v = explode(PHP_EOL, $t);
			
			//$this->debug($v);
			
			if($v[0] != "") {
				$this->translations[trim((string) $v[0])] = array();
				
				for($i=1; $i<=sizeof($v)-1; $i++) {
					if(trim((string) $v[$i]) != "") {
						$vl = explode("=", $v[$i]);
						
						$this->translations[trim((string) $v[0])][trim((string) $vl[0])] = trim((string) $vl[1]);
					}
				}
			}
		}
		
		//$this->debug($this->translations);
	}
	
	function t($var, $reset=false, $fallback="", $current_language = false) {
		return $this->translate($var, $reset=false, $fallback="", $current_language = false);
	}
	
	function translate($var, $reset=false, $fallback="", $current_language = false) {
		//$this->debug('translate(): '.$var.' / '.$reset.' / '.$fallback.' / '.$current_language);
		
		$translations = $this->translations();
		
		$apc_key_translations		= 'individole_translations';
		$apc_key_translations_slug	= 'individole_translations_slug';
		
		//$reset = true;
		if($reset == true) {
			$this->debug($var);
		}
		
		if(empty($this->translations_slug)) {
			if($reset == false) {
				//$translations_slug = $this->getAPCData($apc_key_translations_slug);
			}
			
			//$this->debug($translations_slug);
			
			if(!empty($translations_slug)) {
				$this->translations_slug = $translations_slug;
			
			} else {
				if(isset($_COOKIE['kau-boys_backend_localization_language'])) {
					$test = '1';
					$admin_lang = $_COOKIE['kau-boys_backend_localization_language'];
					
					//$this->debug($admin_lang);
					
				} else {
					global $current_user;
					$current_user_meta = get_user_meta($current_user->ID);
					
					//$this->debug($current_user_meta);
					//$this->debug($current_user);
					
					if(isset($current_user_meta['user_lang'][0]) && $current_user_meta['user_lang'][0] > 0) {
						$test = '2';
						$admin_lang = $current_user_meta['user_lang'][0];
					
					} else if(isset($current_user_meta['locale'][0]) && $current_user_meta['locale'][0] != "") {
						$test = '2.5';
						$admin_lang = $current_user_meta['locale'][0];
					
					} else {
						if($this->isWPML()) {
							$test = '4.1';
							$wpml_translations_slug = $this->wpmlGetCurrentLanguage(array());
							
							//$this->debug("wpml");
							
						} else {
							$test = '4.2';
							
							if(defined("RSS_LANG")) {
								$admin_lang = RSS_LANG;
							
							} else {
								$admin_lang = get_locale();
							}
							
							//$this->debug("no wpml");
						}
					}
				}
				
				//$this->debug('admin_lang:'.$test.$admin_lang);
				//$this->debug(strlen((string) $admin_lang));
				
				$array = array(
					'de_DE'		=> 'de',
					'en_GB'		=> 'en',
					'en_US'		=> 'en',
					'fr_FR'		=> 'fr',
					'sv_SE'		=> 'sv',
					'pt_PT'		=> 'pt',
					'no_NO'		=> 'no',
					'zh_CN'		=> 'zh',
					'tr_TR'		=> 'tr',
					'es_ES'		=> 'es',
					'ru_RU'		=> 'ru',
				);
				
				if(isset($admin_lang) && strlen((string) $admin_lang) == 2) {
					$this->translations_slug = $admin_lang;
					
				} else if(isset($admin_lang) && isset($array[$admin_lang])) {
					$this->translations_slug = $array[$admin_lang];
					
				} else {
					$this->translations_slug = 'en';
				}
				
				if(isset($wpml_translations_slug)) {
					$this->translations_slug = $wpml_translations_slug;
				}
				
				//$this->debug($this->translations_slug);
				
				$this->setAPCData($apc_key_translations_slug, $this->translations_slug, (30 * 60));
			}
		}
		
		if($current_language == true) {
			$final_slug = $this->wpmlGetCurrentLanguage(array());
		
		} else {
			$final_slug = $this->translations_slug;
		}
		
		//$this->debug('final_slug: '.$final_slug.' ('.$var.')');
		//$this->debug($this->path['base']);
		
		if(isset($this->translations[$var][$final_slug])) {
			return $this->translations[$var][$final_slug];
			
		} else if(isset($this->translations[$var]['en'])) {
			return $this->translations[$var]['en'];
			
		} else {
			if($fallback != "") {
				return $fallback;
			
			} else {
				if($this->isAdmin()) {
					return 'MISS TRANSLATION ('.$var.')';
				}
			}
		}
	}
	
	function translateAND() {
		return $this->translate("and", false, " ", true);
	}
	
	function triggerScroll($content) {
		return '<div class="trigger" data-trigger_scroll="1">'.$content.'</div>';
	}
	
	function trimIfString($value) {
		if(is_string($value)) {
			$value = $this->removeEmptySpaces($value);
		}
		
		return $value;
	}
	
	function trimIfStringAndDoShortcode($value) {
		if(is_string($value)) {
			$value = $this->trimIfString($value);
			$value = shortcode_unautop($value);
			$value = do_shortcode($value);
			$value = $this->removeEmptySpaces($value);
			$value = $this->removeTagsFromH($value);
		}
		
		return $value;
	}
	
	function truncateHtml($text, $args=array()) {
		return $this->createExcerpt($text, $args);
	}
	
	function truncateText($text, $args=array()) {
		return $this->createExcerpt($text, $args);
	}
	
	function updateLogStatus() {
		if(defined('DOING_AJAX') && DOING_AJAX) {
			return;
		}
			
		$this->getCurrentUser();
		
		
		
		update_user_meta( $this->user, 'individole_last_action', date("Y-m-d H:i:s") );
		
		if(isset($_SESSION["modify_upload_month"])) {
			unset($_SESSION["modify_upload_month"]);
		}
	}
	
	function update_option($option_name, $option_value, $autoload = false) {
		update_option($option_name, $option_value, $autoload);
	}
	
	function update_post_meta($post_id, $field, $value) {
		if(function_exists('update_field')) {
			update_field($field, $value, $post_id);
			
		} else {
			update_post_meta($post_id, $field, $value);
		}
	}
	
	function update_repeater($post_id, $field, $value) {
		$this->update_post_meta($post_id, $field, array($value));
	}
	
	function updatePostOrder() {
		$obj = new individole_update_post_order();
		return $obj->create();
	}
	
	function updateTermOrder() {
		$obj = new individole_update_term_order();
		return $obj->create();
	}
	
	function updateToolSettings($args) {
		$obj = new individole_update_tool_settings();
		return $obj->create($args);
	}
	
	function updateVersionInfo() {
		$obj = new individole_update_version_info();
		return $obj->create();
	}
	
	function upgradeWPRocket() {
		$obj = new individole_update_wp_rocket();
		return $obj->create();
	}
	
	function unserialize($data) {
		if(empty($data)) {
			return null;
		}
		
		try {
			if(is_string($data) && !empty($data)) {
				$result = @unserialize($data);
				if ($result === false && $data !== 'b:0;') {
					throw new Exception('Ungültige serialisierte Daten');
				}
				return $result;
			
			} else {
				throw new Exception('Kein gültiger serialisierter String');
			}
			
		} catch (Exception $e) {
			error_log('Fehler beim Deserialisieren: '. $e->getMessage().' --> '.$data);
			return null;
		}
	}
	
	function utf8_encode($string) {
		if(!empty($string)) {
			if(function_exists('iconv')) {
				$converted = iconv('ISO-8859-1', 'UTF-8', $string);
				
			} else {
				$converted = mb_convert_encoding($string, 'UTF-8', mb_list_encodings());
			}
			
			if(empty($converted)) {
				return $string;
				
			} else {
				return $converted;
			}
			
		} else {
			return $string;
		}
	}
	
	function utf8_decode($string) {
		if(!empty($string)) {
			if(function_exists('iconv')) {
				$converted = iconv('UTF-8', 'ISO-8859-1', $string);
				
			} else {
				$converted = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
			}
			
			if(empty($converted)) {
				return $string;
				
			} else {
				return $converted;
			}
			
		} else {
			return $string;
		}
	}
	
	function arrayValuesToArrayKeys($array) {
		$return = array();
		foreach($array AS $v) {
			$return[$v] = $v;
		}
		
		return $return;
	}
	
	function variables($var="", $args=array()) {
		$obj = new individole_get_variables();
		return $obj->create($var, $args);
	}
	
	function version($file, $abs="", $removeHttp=true) {
		$obj = new individole_get_file_version();
		return $obj->create($file, $abs, $removeHttp);
	}
	
	function versionImage($file, $remove_scheme=true) {
		$obj = new individole_get_file_version_image();
		return $obj->create($file, $remove_scheme);
	}
	
	function versionMinify($file) {
		$file		= str_replace($this->path['base'], "", $file);
		$file		= get_stylesheet_directory().$file;
		$file		= str_replace($this->path['base'], "", $file);
		$file		= ltrim((string) $file, "/");
			
		return $file;
	}
	
	function video($video="", $args=array()) {
		$obj = new individole_create_video();
		return $obj->create($video, $args);
	}
	
	function individole_admin_output() {
		$c = $this->current;
		//$this->debug($c, 1);
		
		echo '
			<div class="wrap">
				<div id="icon-tools" class="icon32"><br></div>
				<h2 style="margin-bottom:20px;">Individol&eacute; / '.$c->label.'</h2>
				'.$c->output().'
			</div>
		';
	}
	
	function wfuUploaderDone($changable_data, $additional_data) {
		$obj = new individole_uploader_done();
		return $obj->create($changable_data, $additional_data);
	}
	
	function wfuUploaderFileUpLoaded($changable_data, $additional_data) {
		$obj = new individole_uploader_file_uploaded();
		return $obj->create($changable_data, $additional_data);
	}
	
	function wfuUploaderStart($changable_data, $additional_data) {
		$obj = new individole_uploader_start();
		return $obj->create($changable_data, $additional_data);
	}
	
	function WP_Query($wp_args, $debug=false) {
		$wp_args['no_found_rows'] 				= true;
		$wp_args['update_post_meta_cache'] 	= false;
		$wp_args['update_post_term_cache'] 	= false;
		$wp_args['cache_results'] 				= true;
		
		if(isset($wp_args['post_type']) && !is_array($wp_args['post_type'])) {
			$wp_args['post_type'] = array($wp_args['post_type']);
		}
		
		if(isset($wp_args['post_status']) && !is_array($wp_args['post_status'])) {
			$wp_args['post_status'] = array($wp_args['post_status']);
		}
		
		if($var = $this->io("pagination_variable")) {
			if(isset($_GET[$var]) && is_numeric($_GET[$var])) {
				$wp_args['paged'] = $_GET[$var];
				// $cache_key[] = $wp_args['paged'];
			}
		}
		
		if($debug == true) {
			add_filter('posts_clauses', array($this, 'addPostsClausesNoCache'));
			// $this->debug('$individole->WP_Query()');
			// $this->debug($wp_args);
		}
		
		$wp_query = new WP_Query($wp_args);
		
		if(isset($wp_args['posts_per_page']) && $wp_args['posts_per_page'] > 0) {
			if(isset($wp_args['found_posts']) || (isset($wp_args['paged']) && $wp_args['paged'] > 0)) {
				$wp_args_2 = $wp_args;
				$wp_args_2['posts_per_page'] = -1;
				
				$wp_query_2 = new WP_Query($wp_args_2);
				
				if($debug == true) {
					$this->debug('$individole->WP_Query_2()');
				}
				
				$wp_query->found_posts = $wp_query_2->post_count;
				$wp_query->max_num_pages = ceil($wp_query_2->post_count / $wp_args['posts_per_page']);
				
			} else {
				$wp_query->found_posts = $wp_query->post_count;
				$wp_query->max_num_pages = 1;
			}
			
		} else {
			$wp_query->found_posts = $wp_query->post_count;
			$wp_query->max_num_pages = 1;
		}
		
		if($debug == true) {
			$this->debug('$wp_args["posts_per_page"]: '.$wp_args['posts_per_page'].'<br>$wp_query->post_count: '.$wp_query->post_count.'<br>$wp_query->found_posts: '.$wp_query->found_posts.'<br>$wp_query->max_num_pages: '.$wp_query->max_num_pages);
		}
		
		remove_filter('posts_clauses', array($this, 'addPostsClausesNoCache'));
		
		return $wp_query;
	}
	
	function wpAllImportDeleteAPCData() {
		if(function_exists("rocket_clean_domain")) {
			rocket_clean_domain();
		}
		
		$this->deleteAPCData("all");
	}
	
	function wpmlAddLanguagePairsAfterLogin($user_login, $user) {
		if($this->isWPML()) {
			$language_codes = $this->wpmlGetAllLanguageCodes();
			
			if(!empty($language_codes)) {
				$language_pairs = array();
				foreach($language_codes AS $language_code) {
					$language_code_assigned = array();
					foreach($language_codes AS $language_code_2) {
						if($language_code_2 != $language_code) {
							$language_code_assigned[$language_code_2] = 1;
						}
					}
						
					$language_pairs[$language_code] = $language_code_assigned;
				}
				
				//$this->debug($language_pairs, 2);
				
				update_user_meta($user->ID, TABLE_PREFIX."language_pairs", $language_pairs);
			}
		}
	}
	
	function wpmlAfterMakeDuplicate(){
		global $current_user;
		
		$current_user->remove_cap('wpml_manage_languages');
	}
	
	function wpmlBeforeMakeDuplicate() {
		global $current_user;
		
		$current_user->add_cap('wpml_manage_languages');
	}
	
	function wpmlCreateDuplicate($args=array()) {
		$obj = new individole_wpml_create_duplicate();
		return $obj->create($args);
	}
	
	function wpmlCreateLanguages($args) {
		$obj = new individole_wpml_createLanguages();
		return $obj->create($args);
	}
	
	function wpmlField($type, $label="", $instructions="") {
		$default_fields['text'] 					= array();
		$default_fields['textarea'] 				= array();
		$default_fields['image'] 					= array();
		$default_fields['wysiwyg'] 					= array();
		$default_fields['individole_post_object'] 	= array();
		
		foreach($this->wpmlGetAllLanguages() AS $lang) {
			$default_fields['text'][$lang['code']] = array(
				'label'				=> "",
				'prepend'			=> $this->createAdminFlag($lang['code']),
				'type'				=> 'text',
			);
			
			$default_fields['text2'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'text',
			);
			
			$default_fields['textarea'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'textarea',
				'rows'				=> 3,
			);
			
			$default_fields['image'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'image',
			);
			
			$default_fields['wysiwyg'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'wysiwyg',
				'media_upload'		=> 0,
			);
			
			$default_fields['individole_post_object'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'individole_post_object',
				'language'			=> $lang['code'],
			);
			
			$default_fields['individole_post_object_festival'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'individole_post_object',
				'language'			=> $lang['code'],
				'post_type'			=> 'festival',
			);
			
			$default_fields['individole_post_object_conference'][$lang['code']] = array(
				'label'				=> $this->createAdminFlag($lang['code']),
				'type'				=> 'individole_post_object',
				'language'			=> $lang['code'],
				'post_type'			=> 'conference',
			);
		}
		
		$return = array(
			'label'				=> $label,
			'instructions'		=> $instructions,
			'type'				=> 'group',
			'layout'			=> 'table',
			'sub_fields'		=> $default_fields[$type],
		);
		
		return $return;
	}
	
	function wpmlGetCurrentLanguage($args, $test=1) {
		//$obj = new individole_wpml_getCurrentLanguage();
		//return $obj->create($args, $test);
			
		if($this->isWPML()) {
			global $sitepress;
			
			if(is_admin() && !wp_doing_ajax()) {
				if(isset($_GET['lang']) && $_GET['lang'] != "") {
					$current_language = $_GET['lang'];
				
				} else if(isset($_GET['language']) && $_GET['language'] != "") {
					$current_language = $_GET['language'];
				
				} else if(isset($_POST['lang']) && $_POST['lang'] != "") {
					$current_language = $_POST['lang'];
				
				} else if(isset($_POST['language']) && $_POST['language'] != "") {
					$current_language = $_POST['language'];
				
				} else {
					global $sitepress;
					
					$current_language = $sitepress->get_current_language();
				}
				
			} else {
				if(isset($_POST['l']) && $_POST['l'] != "") {
					$current_language = $_POST['l'];
					
				} else {
					$current_language = $sitepress->get_current_language();
				}
			}
			
			$this->l = $current_language;
			
			return $current_language;
			
		} else {
			if(defined("RSS_LANG")) {
				$locale = explode( '_', RSS_LANG );
			
			} else {
				$locale = explode( '_', get_locale() );
			}
			
			//$this->debug($locale);
			
			if(isset($locale[0]) && $locale[0] != '') {
				$final_locale = $locale[0];
			
			} else {
				$final_locale = 'en';
			}
			
			//$this->debug('--> '.$final_locale);
			
			return $final_locale;
		}
	}
	
	function wpmlGetCurrentLanguageName() {
		$languages = $this->wpmlGetAllLanguages(array());
		return $languages[$this->l]['native_name'];
	}
	
	function wpmlGetDefaultLanguage() {
		return apply_filters('wpml_default_language', NULL );
	}
	
	function wpmlGetLanguageCodeByID($post_id) {
		$obj = new individole_wpml_getLanguageCodeByID();
		return $obj->create($post_id);
	}
	
	function wpmlGetTranslationIDs($post_id, $post_type="") {
		$obj = new individole_wpml_getTranslationIDs();
		return $obj->create($post_id, $post_type);
	}
	
	function wpmlGetTranslationIDsFromArray($post_ids=array(), $l="") {
		$obj = new individole_wpml_getTranslationIDsFromArray();
		return $obj->create($post_ids, $l);
	}
	
	function wpmlGetTranslationIDsFromArrayKeys($post_ids=array()) {
		$obj = new individole_wpml_getTranslationIDsFromArrayKeys();
		return $obj->create($post_ids);
	}
	
	function wpmlGetTranslation($post_id=0, $language="") {
		$obj = new individole_wpml_getTranslation();
		return $obj->create($post_id, $language);
	}
	
	function wpmlGetTranslations($post_id=0, $type="post") {
		$obj = new individole_wpml_getTranslations();
		return $obj->create($post_id, $type);
	}
	
	function wpmlGetWCShopID(){
		$shop_id = wc_get_page_id("shop");
		$shop_id = icl_object_id( $shop_id , 'page', false, $this->wpmlGetCurrentLanguage(array()));
		
		return $shop_id;
	}
	
	function wpmlGetAllLanguages() {
		return $this->languages;
	}
	
	function wpmlGetAllLanguageCodes() {
		$obj = new individole_wpml_getAllLanguageCodes();
		return $obj->create();
	}
	
	function wpmlGetLanguageList() {
		$obj = new individole_wpml_getLanguageList();
		return $obj->create();
	}
	
	function wpmlGetHomeURL() {
		return $this->sitepress->language_url(ICL_LANGUAGE_CODE);
	}
	
	function wpmlHasPosttypeTranslation($post_type) {
		if($this->sitepress->is_translated_post_type($post_type)) {
			return true;
		}
	}
	
	function wpmlOverrideIsTranslator($is_translator, $user_id, $args) {
		return true;
	}
	
	function wpmlCreatePostmetaSync($post_id, $p) {
		$obj = new individole_wpml_createPostmetaSync();
		return $obj->create($post_id, $p);
	}
	
	function updatePostAfterDuplicate($id) {
		$my_post = array(
			'ID'				=> $id,
			'post_status'		=> 'draft',
			'post_name'			=> '',
			'post_date'			=> 0,
			'post_date_gmt'		=> 0,
		);
		
		wp_update_post( $my_post );
		
		update_post_meta($id, "post_count", 0);
		
		global $wpdb;
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_icl_lang_duplicate_of'");
	}
	
	function wpmlUpdatePostAfterDuplicate( $master_post_id, $lang, $postarr, $id ) {
		$this->updatePostAfterDuplicate($id);
	}
	
	function wprocketPurgeCloudflare() {
		return;
		
		if($wp_rocket_settings = $this->isWPRocket()) {
			if($wp_rocket_settings['do_cloudflare'] == 1 && $wp_rocket_settings['cloudflare_zone_id'] != "") {
				if($wp_rocket_settings['version'] > 3) {
					
					
					$purge_cloudflare = Cloudflare_Subscriber::purge_cloudflare();
					$this->debug($purge_cloudflare);
					
				} else {
					rocket_purge_cloudflare();
				}
			}
		}
	}
}

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

if (!function_exists('array_column')) {
	 function array_column(array $input, $columnKey, $indexKey = null) {
		  $array = array();
		  foreach ($input as $value) {
				if ( ! isset($value[$columnKey])) {
					 trigger_error("Key \"$columnKey\" does not exist in array");
					 return false;
				}
				if (is_null($indexKey)) {
					 $array[] = $value[$columnKey];
				}
				else {
					 if ( ! isset($value[$indexKey])) {
						  trigger_error("Key \"$indexKey\" does not exist in array");
						  return false;
					 }
					 if ( ! is_scalar($value[$indexKey])) {
						  trigger_error("Key \"$indexKey\" does not contain scalar value");
						  return false;
					 }
					 $array[$value[$indexKey]] = $value[$columnKey];
				}
		}
		return $array;
	 }
}

if (!function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }
        
        return array_keys($array)[count($array)-1];
    }
}

add_filter('date_i18n', function ($date, $format, $timestamp, $gmt) {
	return wp_date($format, $timestamp);
}, 99, 4);