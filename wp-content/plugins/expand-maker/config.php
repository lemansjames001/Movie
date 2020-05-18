<?php
if(!class_exists('YrmConfig')) {

	class YrmConfig {

		public function __construct() {

			$this->init();
		}

		public function addDefine($name, $value) {
			if(!defined($name)) {
				define($name, $value);
			}
		}

		public static function typePaths() {
			global $YRM_TYPES;
			global $YRM_EXTENSIONS;
			$paths = array(
				'button' => YRM_CLASSES,
				'inline' => YRM_CLASSES,
				'popup' => YRM_CLASSES,
				'inlinePopup' => YRM_CLASSES
			);
			$paths = apply_filters('yrmClassesPaths', $paths);
			$YRM_EXTENSIONS = apply_filters('yrmExtensions', array());
		
			$YRM_TYPES = $paths;
		}

		public static function extensions() {
		    $extensions = array();
            $extensions['forms'] = array(
                'pluginKey' => 'read-more-login-form/ReadMoreLoginForm.php',
                'isType' => true,
                'shortKey' => 'forms',
                'videoURL' => YRM_FORMS_VIDEO,
                'boxTitle' => __('Read More Login & Registration forms')
            );

            $extensions['scroll'] = array(
                'pluginKey' => 'read-more-scroll/read-more-scroll.php',
                'isType' => true,
                'useMainOptions' => true,
                'shortKey' => 'scroll',
                'videoURL' => YRM_SCROLL_VIDEO,
                'boxTitle' => __('Scroll to Top')
            );

            $extensions['analytics'] = array(
                'pluginKey' => 'read-more-analytics/read-more-analytics.php',
                'isType' => false,
                'shortKey' => 'analytics',
                'videoURL' => YRM_ANALYTICS_VIDEO,
                'boxTitle' => __('Analytics')
            );

		    return apply_filters('yrmExtensionsInfo', $extensions);
        }

		public function getDirectoryName() {

			$baseName = plugin_basename(__FILE__);
			$readMoreDirectoryName = explode('/', $baseName);

			if(isset($readMoreDirectoryName[0])) {
				return $readMoreDirectoryName[0];
			}
			else {
				return '';
			}
		}

		private function init() {

			if(!defined('ABSPATH')) {
				exit();
			}
			
			$readMoreDirectoryName = $this->getDirectoryName();

			if(!defined('YRM_PLUGIN_PREFIX')) {
				define("YRM_PLUGIN_PREFIX", $readMoreDirectoryName);
			}

			if(!defined('YRM_MAIN_FILE')) {
				define("YRM_MAIN_FILE", $readMoreDirectoryName . '.php');
			}

			if(!defined('YRM_PATH')) {
				define("YRM_PATH", dirname(__FILE__) . '/');
			}
			
			if(!defined('YRM_CLASSES')) {
				define("YRM_CLASSES", YRM_PATH . 'classes/');
			}

			if(!defined('YRM_FILES')) {
				define("YRM_FILES", YRM_PATH . 'files/');
			}

			if(!defined('YRM_CSS')) {
				define("YRM_CSS", YRM_PATH . 'css/');
			}

			if(!defined('YRM_VIEWS')) {
				define("YRM_VIEWS", YRM_PATH . 'views/');
			}

			if(!defined('YRM_VIEWS_SECTIONS')) {
				define('YRM_VIEWS_SECTIONS', YRM_VIEWS.'sections/');
			}

			if(!defined('YRM_JAVASCRIPT_PATH')) {
				define("YRM_JAVASCRIPT_PATH", YRM_PATH . 'js/');
			}

			if(!defined('YRM_URL')) {
				define("YRM_URL", plugins_url('', __FILE__) . '/');
			}

			if(!defined('YRM_JAVASCRIPT')) {
				define("YRM_JAVASCRIPT", YRM_URL . 'js/');
			}

			if(!defined('YRM_CSS_URL')) {
				define("YRM_CSS_URL", YRM_URL . 'css/');
			}
			
			if(!defined('YRM_IMG_URL')) {
				define('YRM_IMG_URL', YRM_URL . 'img/');
			}

			if(!defined('YRM_LANG')) {
				define("YRM_LANG", 'yrm_lang');
			}

			if(!defined('YRM_VERSION')) {
				define("EXPM_VERSION", 2.49);
			}

			if(!defined('YRM_VERSION_TEXT')) {
				define("YRM_VERSION_TEXT", '2.4.9');
			}

			if(!defined('EXPM_VERSION_PRO')) {
				define("EXPM_VERSION_PRO", 1.55);
			}

			if(!defined('YRM_ADMIN_POST_NONCE')) {
				define('YRM_ADMIN_POST_NONCE', 'YRM_ADMIN_POST_NONCE');
			}

			if(!defined('YRM_FREE_PKG')) {
				define("YRM_FREE_PKG", 1);
			}

			if(!defined('YRM_SILVER_PKG')) {
				define("YRM_SILVER_PKG", 2);
			}

			if(!defined('YRM_GOLD_PKG')) {
				define("YRM_GOLD_PKG", 3);
			}
			
			if(!defined('YRM_PLATINUM_PKG')) {
				define("YRM_PLATINUM_PKG", 3);
			}

			if(!defined('YRM_PLATINUM_PKG')) {
				define("YRM_PLATINUM_PKG", 3);
			}

			require_once(dirname(__FILE__).'/config-pkg.php');

			if(!defined('YRM_SHOW_REVIEW_PERIOD')) {
				define('YRM_SHOW_REVIEW_PERIOD', 30);
			}

			if(!defined('YRM_PRO_URL')) {
				define('YRM_PRO_URL', 'https://edmonsoft.com/');
			}

			if(!defined('YRM_REVIEW_URL')) {
				define('YRM_REVIEW_URL', 'https://wordpress.org/support/plugin/expand-maker/reviews/?filter=5');
			}

			$this->addDefine('YRM_BUTTON_ICON_URL', YRM_IMG_URL.'arrow.png');

			$this->addDefine('YRM_SUPPORT_URL', 'https://wordpress.org/support/plugin/expand-maker/');
			
			$this->addDefine('YRM_NUMBER_PAGES', 15);
			$this->addDefine('YRM_SUPPORT_MENU_KEY', 'yrmSupportKey');
			$this->addDefine('YRM_VIDEO_TUTORIAL_KEY', 'yrmTutorialKey');
			$this->addDefine('YRM_ANALYTICS_VIDEO', 'https://www.youtube.com/watch?v=h_RTKiBJ1aE');
			$this->addDefine('YRM_FORMS_VIDEO', 'https://www.youtube.com/watch?v=SXYKH5Hlf5k');
			$this->addDefine('YRM_SCROLL_VIDEO', 'https://www.youtube.com/watch?v=Adj-xmePz7k');
			$this->addDefine('YRM_POPUP_VIDEO', 'https://www.youtube.com/watch?v=Iz8U2Ly-VN8&feature=emb_title');
			
			$this->globalVariables();
		}

		public static function readMoreHeaderScript() {

			$headerScript = "EXPM_VERSION=" . EXPM_VERSION.";";
			if(YRM_PKG > YRM_FREE_PKG) {
				$headerScript = "EXPM_VERSION_PRO=" . EXPM_VERSION_PRO.";";
			}
            $headerScript .= "EXPM_AJAX_URL='" . admin_url( 'admin-ajax.php' )."';";
			$headerScript .= "
			function yrmAddEvent(element, eventName, fn) {
				if (element.addEventListener)
					element.addEventListener(eventName, fn, false);
				else if (element.attachEvent)
					element.attachEvent('on' + eventName, fn);
			}";
			return "<script type=\"text/javascript\">
				$headerScript
			</script>";
		}
		
		private function globalVariables() {
			global $YRM_TYPES_INFO;
			$tutorials = array(
				'proVersion' => YRM_POPUP_VIDEO,
				'yrm-forms' => YRM_FORMS_VIDEO,
				'analytics' => YRM_ANALYTICS_VIDEO,
				'scroll' => YRM_SCROLL_VIDEO
			);
			$tutorials = apply_filters('YrmYoutubeUrls', $tutorials);
			$YRM_TYPES_INFO['youtubeUrls'] = $tutorials;
		}

		public static function optionsValues() {
			global $YRM_OPTIONS;
			$options = array();
			$YRM_OPTIONS = apply_filters('yrmOptionsCongifFilter', $options);
		}
	}

	$configInit = new YrmConfig();
}