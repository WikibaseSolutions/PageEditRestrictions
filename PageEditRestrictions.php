<?php
/**
 * This extension allows certain pages to be restricted to be edited only by certain users.
 *
 * @version 1.0.0
 *
 * @author Pim Bax (Joeytje50)
 */

// Ensure that the script cannot be executed outside of MediaWiki
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is an extension to MediaWiki and cannot be run standalone.' );
}

// Register extension with MediaWiki
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'PageEditRestrictions',
	'author' => array(
		'Pim Bax'
		),
	'version' => '1.0.0',
	'url' => 'https://www.mediawiki.org/wiki/Extension:PageEditRestrictions',
	'descriptionmsg' => 'pageeditrestrictions-desc',
	'license-name' => 'GPL-3.0+'
);

// Load extension's class
$wgAutoloadClasses['PageEditRestrictions'] = __DIR__ . '/PageEditRestrictions.class.php';

// Register extension messages
$wgMessagesDirs['PageEditRestrictions'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['PageEditRestrictions'] = __DIR__ . '/PageEditRestrictions.i18n.php';

// Add user permission
$wgAvailableRights[] = 'editrestrictedpages';
$wgGroupPermissions['sysop']['editrestrictedpages'] = true;

// Register hook
$wgHooks['userCan'][] = 'PageEditRestrictions::onUserCan';