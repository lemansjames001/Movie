<?php

class ReadMoreAdminHelper {
	public static function getPluginActivationUrl($key) {
		$action = 'install-plugin';
		$contactFormUrl = wp_nonce_url(
			add_query_arg(
				array(
					'action' => $action,
					'plugin' => $key
				),
				admin_url( 'update.php' )
			),
			$action.'_'.$key
		);

		return $contactFormUrl;
	}

	public static function getVersionString() {
	    $version = 'YRM_VERSION='.EXPM_VERSION;
	    if(YRM_PKG > YRM_FREE_PKG) {
		    $version = 'YRM_VERSION_PRO=' . EXPM_VERSION_PRO.";";
	    }

	    return $version;
    }

    public static function separateToActiveAndNotActive($extensions) {
        $result = array(
          'active' => array(),
          'passive' => array()
        );

        foreach($extensions as $extension) {
            if(empty($extension)) {
                continue;
            }
            $key = @$extension['pluginKey'];

            if(is_plugin_active($key)) {
                if($extension['isType']) {
                    $result['active'][] = $extension;
                }
            }
            else {
                $result['passive'][] = $extension;
            }
        }

        return $result;
    }

    public static function getLabelProSpan() {
        $proSpan = '';
        if(YRM_PKG == YRM_FREE_PKG) {
            $proSpan = '<a class="yrm-pro-span" href="'.YRM_PRO_URL.'" target="_blank">'.__('pro', YCD_TEXT_DOMAIN).'</a>';
        }

        return $proSpan;
    }

    public static function getOptionPkgClassName() {
        $optionPkgClassName = 'yrm-option-wrapper';
        if(YRM_PKG == YRM_FREE_PKG) {
            $optionPkgClassName .= '-pro';
        }

        return $optionPkgClassName;
    }
    
    public static function getTitleFromType($type) {
		$title = '';
		
		switch ($type) {
			case 'button':
				$title = 'Button';
				break;
			case 'inline':
				$title = 'Inline';
				break;
			case 'link':
				$title = 'Link button';
				break;
			case 'alink':
				$title = 'Link';
				break;
			case 'popup':
				$title = 'Button & popup';
				break;
			case 'inlinePopup':
				$title = 'Inline & popup';
				break;
			case 'scroll':
				$title = 'Scroll to Top';
				break;
			case 'yrm-forms':
				$title = 'Read More Login & Registration forms';
				break;
			case 'proVersion':
				$title = 'Read more & popup';
				break;
			case 'analytics':
				$title = 'Analytics';
				break;
			default:
				$title = ucfirst($title);
				break;
		}
		
		return $title;
    }
	
	public static function getYoutubeEmbedUrl($url) {
		$shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
		$longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
		
		if (preg_match($longUrlRegex, $url, $matches)) {
			$youtube_id = $matches[count($matches) - 1];
		}
		
		if (preg_match($shortUrlRegex, $url, $matches)) {
			$youtube_id = $matches[count($matches) - 1];
		}
		return 'https://www.youtube.com/embed/' . $youtube_id ;
	}
}