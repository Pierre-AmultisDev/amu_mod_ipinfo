<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_amu_ipinfo
 *
 * @copyright   Copyright (C) 2024 Amultis. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$ipVersion  = ModAmuIPInfoHelper::getIPVersion($ip);
$isPrivate  = ModAmuIPInfoHelper::isPrivateIP($ip);
$showVersion = $params->get('show_ip_version', 1);
$showPrivate = $params->get('show_private_note', 1);
$showCopy    = $params->get('show_copy_button', 1);
$showGeoIP   = $params->get('show_geoip', 0);
$showFlag    = $params->get('show_flag', 1);
$showMapLink = $params->get('show_map_link', 1);
$showIsp     = $params->get('show_isp', 1);
$provider    = $params->get('geoip_provider', 'ip-api');
$moduleId    = 'mod-ipinfo-' . $module->id;
?>

<div class="mod-ipinfo<?php echo $params->get('moduleclass_sfx') ? ' ' . htmlspecialchars($params->get('moduleclass_sfx')) : ''; ?>" id="<?php echo $moduleId; ?>">

    <!-- IP Address display -->
    <div class="mod-ipinfo__ip-block">
        <div class="mod-ipinfo__label"><?php echo Text::_('MOD_AMU_IPINFO_YOUR_IP'); ?></div>

        <div class="mod-ipinfo__ip-row">
            <span class="mod-ipinfo__ip-address" id="<?php echo $moduleId; ?>-ip">
                <?php echo htmlspecialchars($ip); ?>
            </span>

            <?php if ($showVersion && $ipVersion !== 'Unknown') : ?>
                <span class="mod-ipinfo__version-badge mod-ipinfo__version-badge--<?php echo strtolower($ipVersion); ?>">
                    <?php echo htmlspecialchars($ipVersion); ?>
                </span>
            <?php endif; ?>

            <?php if ($showCopy) : ?>
                <button class="mod-ipinfo__copy-btn"
                        onclick="modIPInfoCopy('<?php echo $moduleId; ?>-ip', this)"
                        title="<?php echo Text::_('MOD_AMU_IPINFO_COPY_IP'); ?>"
                        type="button"
                        aria-label="<?php echo Text::_('MOD_AMU_IPINFO_COPY_IP'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                </button>
            <?php endif; ?>
        </div>

        <?php if ($showPrivate && $isPrivate) : ?>
            <div class="mod-ipinfo__notice mod-ipinfo__notice--private">
                <?php echo Text::_('MOD_AMU_IPINFO_PRIVATE_IP_NOTE'); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($showGeoIP) : ?>
        <div class="mod-ipinfo__geo-block">
            <?php if ($geoError !== null) : ?>
                <div class="mod-ipinfo__notice mod-ipinfo__notice--error">
                    <?php
                    switch ($geoError) {
                        case 'private_ip':
                            echo Text::_('MOD_AMU_IPINFO_GEO_PRIVATE_IP');
                            break;
                        case 'missing_key':
                            echo Text::_('MOD_AMU_IPINFO_GEO_MISSING_KEY');
                            break;
                        default:
                            echo Text::sprintf('MOD_AMU_IPINFO_GEO_ERROR', htmlspecialchars($geoError));
                    }
                    ?>
                </div>

            <?php elseif (!empty($geoData)) : ?>

                <div class="mod-ipinfo__geo-header">
                    <?php if ($showFlag && !empty($geoData['country_code'])) : ?>
                        <img class="mod-ipinfo__flag"
                             src="https://flagcdn.com/24x18/<?php echo htmlspecialchars($geoData['country_code']); ?>.png"
                             srcset="https://flagcdn.com/48x36/<?php echo htmlspecialchars($geoData['country_code']); ?>.png 2x"
                             width="24"
                             height="18"
                             alt="<?php echo htmlspecialchars($geoData['country']); ?>"
                             loading="lazy"
                        />
                    <?php endif; ?>
                    <span class="mod-ipinfo__geo-location">
                        <?php
                        $locationParts = array_filter([
                            $geoData['city'] ?? '',
                            $geoData['region'] ?? '',
                            $geoData['country'] ?? ''
                        ]);
                        echo htmlspecialchars(implode(', ', $locationParts));
                        ?>
                    </span>
                </div>

                <dl class="mod-ipinfo__geo-details">

                    <?php if (!empty($geoData['postal'])) : ?>
                        <div class="mod-ipinfo__geo-row">
                            <dt><?php echo Text::_('MOD_AMU_IPINFO_GEO_POSTAL'); ?></dt>
                            <dd><?php echo htmlspecialchars($geoData['postal']); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($geoData['timezone'])) : ?>
                        <div class="mod-ipinfo__geo-row">
                            <dt><?php echo Text::_('MOD_AMU_IPINFO_GEO_TIMEZONE'); ?></dt>
                            <dd><?php echo htmlspecialchars($geoData['timezone']); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ($showIsp && !empty($geoData['isp'])) : ?>
                        <div class="mod-ipinfo__geo-row">
                            <dt><?php echo Text::_('MOD_AMU_IPINFO_GEO_ISP'); ?></dt>
                            <dd><?php echo htmlspecialchars($geoData['isp']); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ($showIsp && !empty($geoData['asn'])) : ?>
                        <div class="mod-ipinfo__geo-row">
                            <dt><?php echo Text::_('MOD_AMU_IPINFO_GEO_ASN'); ?></dt>
                            <dd><?php echo htmlspecialchars($geoData['asn']); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ($showMapLink && !empty($geoData['latitude']) && !empty($geoData['longitude'])) : ?>
                        <div class="mod-ipinfo__geo-row">
                            <dt><?php echo Text::_('MOD_AMU_IPINFO_GEO_COORDINATES'); ?></dt>
                            <dd>
                                <a class="mod-ipinfo__map-link"
                                   href="https://www.openstreetmap.org/?mlat=<?php echo htmlspecialchars($geoData['latitude']); ?>&mlon=<?php echo htmlspecialchars($geoData['longitude']); ?>#map=12/<?php echo htmlspecialchars($geoData['latitude']); ?>/<?php echo htmlspecialchars($geoData['longitude']); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($geoData['latitude']); ?>, <?php echo htmlspecialchars($geoData['longitude']); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </a>
                            </dd>
                        </div>
                    <?php endif; ?>

                </dl>

                <div class="mod-ipinfo__provider-note">
                    <?php echo Text::sprintf('MOD_AMU_IPINFO_POWERED_BY', htmlspecialchars($provider)); ?>
                </div>

            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php if ($showCopy) : ?>
<script>
function modIPInfoCopy(elementId, btn) {
    var text = document.getElementById(elementId).textContent.trim();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            modIPInfoCopyFeedback(btn);
        });
    } else {
        var el = document.createElement('textarea');
        el.value = text;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        modIPInfoCopyFeedback(btn);
    }
}
function modIPInfoCopyFeedback(btn) {
    btn.classList.add('mod-ipinfo__copy-btn--copied');
    setTimeout(function() {
        btn.classList.remove('mod-ipinfo__copy-btn--copied');
    }, 1500);
}
</script>
<?php endif; ?>
