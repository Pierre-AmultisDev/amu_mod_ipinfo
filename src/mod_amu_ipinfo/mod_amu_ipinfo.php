<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_amu_ipinfo
 *
 * @copyright   Copyright (C) 2024 Amultis. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('mod_amu_ipinfo', 'mod_amu_ipinfo/style.css');

// Get client IP address (IPv4, IPv6, or dual-stack)
$ip = ModAmuIPInfoHelper::getClientIP();

// Gather Geo IP data if enabled
$geoData = null;
$geoError = null;
if ($params->get('show_geoip', 0) && !empty($ip)) {
    $provider = $params->get('geoip_provider', 'ip-api');
    [$geoData, $geoError] = ModAmuIPInfoHelper::getGeoIP($ip, $provider, $params);
}

require ModuleHelper::getLayoutPath('mod_amu_ipinfo', $params->get('layout', 'default'));
