<?php
abstract class ReadMore {

	public function getRemoveOptions() {

		return array();
	}

	public function includeOptionsBlock($dataObj) {

	}

	public static function RemoveOption($option) {

		global $YrmRemoveOptions;
		return isset($YrmRemoveOptions[$option]);
	}

	public static function isActiveReadMore($id) {
		$isActiveSaved = get_option('yrm-read-more-'.$id);

		if ($isActiveSaved == -1) {
			return false;
		}

		return true;
	}

	public static function allowRender($shortcodeData) {
		if (is_admin()) {
			return true;
		}
		$id = $shortcodeData->getId();
		$status = self::isActiveReadMore($id);
		if(!$status) {
			return false;
		}
		$savedData = $shortcodeData->getDataObj();
		$options = $savedData->getSavedOptions();
		$allowForCurrentDevice = self::allowForCurrentDevice($options, $shortcodeData);
		
		if(!$allowForCurrentDevice) {
			return false;
		}

		$allowForBlogPost = self::allowForBlogPostPage($options);
        if(!$allowForBlogPost) {
            return false;
        }

		return true;
	}

	public static function allowForBlogPostPage($options) {
        $isBlogPostPage = (is_front_page() && is_home());
	    if(!empty($options['hide-button-blog-post']) && $isBlogPostPage) {
	        return false;
        }

        return true;
    }

	public static function allowForCurrentDevice($options, $shortcodeData) {
		$status = true;
		if (!empty($options['show-only-devices'])) {
			$devices = $options['yrm-selected-devices'];
			$hideContent = $options['hide-content'];
			$currentDevice = ReadMoreAdminHelperPro::getUserDevice();
			if(!in_array($currentDevice, $devices)) {
				if($hideContent) {
					$shortcodeData->setToggleContent('');
				}
				$status = false;
			}
		}

		return $status;
	}

	public function includeCustomScript() {

    }
}