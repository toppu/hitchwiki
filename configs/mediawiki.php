<?php
#
# Hitchwiki MediaWiki configuration
#
# See /public/wiki/includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
  exit;
}

# If PHP's memory limit is very low, some operations may fail.
ini_set('memory_limit', '64M');

# Load Hitchwiki Config
$hwConfig = parse_ini_file("settings.ini",true);

if ($wgCommandLineMode) {
  if (isset($_SERVER) && array_key_exists( 'REQUEST_METHOD', $_SERVER))
  die("This script must be run from the command line\n");
} elseif (empty($wgNoOutputBuffer)) {
  // Compress output if the browser supports it
  if (!ini_get( 'zlib.output_compression')) @ob_start('ob_gzhandler');
}

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = $hwConfig["general"]["sitename"];
$wgMetaNamespace = $hwConfig["general"]["metanamespace"];

##
## Dev environment settings
##
if(isset($hwConfig['general']['env']) && $hwConfig['general']['env'] == 'dev') {

  // Enable error reporting
  error_reporting( -1 );
  ini_set( 'display_errors', 1 );

  // Show the debug toolbar if 'debug' is set on the request, either as a
  // parameter or a cookie.
  if ( !empty( $_REQUEST['debug'] ) ) {
    $wgDebugToolbar = true;
  }

  // Expose debug info for PHP & SQL errors.
  $wgShowExceptionDetails = true;
  $wgDevelopmentWarnings = true;
  $wgDebugDumpSql = true;
  $wgShowDBErrorBacktrace = true;
  $wgShowSQLErrors = true;

  // Profiling
  $wgDebugProfiling = false;

  // Log into file
  $logDir = '/vagrant/logs';
  $wgDebugLogFile = "{$logDir}/mediawiki-debug.log";
  foreach ( array( 'exception', 'runJobs', 'JobQueueRedis' ) as $logGroup ) {
    $wgDebugLogGroups[$logGroup] = "{$logDir}/mediawiki-{$logGroup}.log";
  }

  # Disable caching
  #$wgEnableParserCache = false;
  #$wgCachePages = false;
}

## Setup $hwLang
## Will also change $wgSitename if it finds local name
require_once("mediawiki-lang.php");

## When you make changes to this configuration file, this will make
## sure that cached pages are cleared.
$configdate      = gmdate( 'YmdHis', @filemtime( __FILE__ ) );
$wgCacheEpoch    = max($wgCacheEpoch, $configdate);

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath       = "/" . $hwLang;
$wgScriptExtension  = ".php";
$wgArticlePath      = "{$wgScriptPath}/$1";
$wgScript           = "{$wgScriptPath}/index.php";
$wgUsePathInfo      = true;

# Site language code, should be one of the list in ./languages/Names.php
$wgLanguageCode = $hwLang;

## The protocol and server name to use in fully-qualified URLs
$wgServer = "http://" . $hwConfig["general"]["domain"];

## The relative URL path to the skins directory
$wgStylePath = $wgScriptPath . "/skins";


## The relative URL path to the logo.
$wgLogo = $wgScriptPath . "/../wiki-logo.png";

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "contact@" . $hwConfig["general"]["domain"];
$wgPasswordSender = "noreply@" . $hwConfig["general"]["domain"];

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Database settings
$wgDBtype     = "mysql";
$wgDBserver   = $hwConfig["db"]["host"];
$wgDBname     = $hwConfig["db"]["database"];
$wgDBuser     = $hwConfig["db"]["username"];
$wgDBpassword = $hwConfig["db"]["password"];

# MySQL specific settings
$wgDBprefix = $hwConfig["db"]["prefix"];

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Experimental charset support for MySQL 5.0.
$wgDBmysql5 = true;

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = array();


## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgGenerateThumbnailOnParse = false;

$wgUploadPath       = $wgScriptPath . "/images/" . $hwLang;
$wgUploadDirectory  = $IP . "/images/" . $hwLang;

# Allowed file extensions for uploading files
$wgFileExtensions = array(
  'png', 'gif', 'jpg', 'jpeg', 'svg', 'pdf',
  'PNG', 'GIF', 'JPG', 'JPEG', 'SVG', 'PDF',
);

$wgUseCommaCount = false;

# InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons = true;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.utf8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
#$wgHashedUploadDirectory = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

$wgSecretKey = "a9fcd44f28a9e7caf01d2a8e7a27031805d08b23fa37f3030dc55fae01709145";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = "e420593e5f32abdf";

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.

$wgEnableCreativeCommonsRdf = true;
$wgRightsPage               = ""; // Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl                = "http://creativecommons.org/licenses/by-sa/4.0/";
$wgRightsText               = "Creative Commons Attribution-Share Alike";
$wgRightsIcon               = "${wgStylePath}/common/images/cc-by-sa.png";


$wgAllowDisplayTitle = true;

# CSS
$wgUseSiteCss        = true;
$wgAllowUserCss      = true;

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

# Recent changes patrolling
$wgShowUpdatedMarker             = true;
$wgAllowCategorizedRecentChanges = true;
$wgAllowCategorizedRecentChanges = true;
$wgPutIPinRC                     = true;
$wgUseRCPatrol                   = true;

# Permissions
$wgGroupPermissions['*']['edit'] = false;



/***** Skins ******************************************************************************************/

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = "vector";

# Enabled skins
require_once "$IP/skins/CologneBlue/CologneBlue.php";
require_once "$IP/skins/Modern/Modern.php";
require_once "$IP/skins/MonoBook/MonoBook.php";
require_once "$IP/skins/Vector/Vector.php";
#require_once "$IP/skins/Hitchwiki/Hitchwiki.php";


/***** Extensions ******************************************************************************************/

# Validator & ParamProcessor are required by SMW
# Note that this doesn't load param-processor's tests
#require_once "$IP/extensions/param-processor/src/Settings.php";
#require_once "$IP/extensions/param-processor/src/IParam.php";
#require_once "$IP/extensions/param-processor/src/IParamDefinition.php";
#require_once "$IP/extensions/param-processor/src/Param.php";
#require_once "$IP/extensions/param-processor/src/ParamDefinition.php";
#require_once "$IP/extensions/param-processor/src/ProcessedParam.php";
#require_once "$IP/extensions/param-processor/src/ParamDefinitionFactory.php";
#require_once "$IP/extensions/param-processor/src/ProcessingResult.php";
#require_once "$IP/extensions/param-processor/src/ProcessingError.php";
#require_once "$IP/extensions/param-processor/src/ProcessingErrorHandler.php";
#require_once "$IP/extensions/param-processor/src/Options.php";
#require_once "$IP/extensions/param-processor/src/TopologicalSort.php";
#require_once "$IP/extensions/param-processor/src/Definition/StringParam.php";
#require_once "$IP/extensions/param-processor/src/Definition/DimensionParam.php";
#require_once "$IP/extensions/param-processor/DefaultConfig.php";
#require_once "$IP/extensions/Validator/Validator.php";


require_once "$IP/extensions/CustomData/CustomData.php"; // CustomData is needed by GeoCrumbs
require_once "$IP/extensions/GeoCrumbs/GeoCrumbs.php";
require_once "$IP/extensions/GeoData/GeoData.php";
require_once "$IP/extensions/ExternalData/ExternalData.php";
require_once "$IP/extensions/ParserFunctions/ParserFunctions.php";
require_once "$IP/extensions/MultimediaViewer/MultimediaViewer.php";
require_once "$IP/extensions/ApiSandbox/ApiSandbox.php";
require_once "$IP/extensions/OAuth/OAuth.php";

# Define "Special:AdminLinks" page, that holds links meant to be helpful for wiki administrators
require_once "$IP/extensions/AdminLinks/AdminLinks.php";
$wgGroupPermissions['my-group']['adminlinks'] = true;

# This can be disabled after http://hitchwiki.org/en/MediaWiki:Sitenotice is no more needed.
# http://www.mediawiki.org/wiki/Extension:DismissableSiteNotice
require_once "$IP/extensions/DismissableSiteNotice/DismissableSiteNotice.php";

# Semantic MediaWiki extensions
# These were installed via composer directly at MediaWiki folder and Composer takes care loading them
# https://semantic-mediawiki.org/wiki/Help:Installation#Installation
//require_once "$IP/extensions/SemanticMediaWiki/SemanticMediaWiki.php";
if(file_exists("$IP/extensions/SemanticMediaWiki/SemanticMediaWiki.php")) {
  enableSemantics();
  //require_once "$IP/extensions/Maps/Maps.php";
  //require_once "$IP/extensions/SemanticMaps/SemanticMaps.php";
  //require_once "$IP/extensions/SemanticForms/SemanticForms.php";
  require_once "$IP/extensions/SemanticFormsInputs/SemanticFormsInputs.php";
  require_once "$IP/extensions/SemanticWatchlist/SemanticWatchlist.php";
}

# Enable old string functions (needed at our semantic templates)
require_once "$IP/extensions/ParserFunctions/ParserFunctions.php";
$wgPFEnableStringFunctions = true;

# Interwiki links (nomadwiki, trashwiki etc)
# - Grant sysops permissions to edit interwiki data
# - To enable pulling global interwikis from a central database
require_once "$IP/extensions/Interwiki/Interwiki.php";
$wgGroupPermissions['sysop']['interwiki'] = true;
$wgInterwikiCentralDB = 'interwiki';

# Recent changes cleanup
# https://www.mediawiki.org/wiki/Extension:Recent_Changes_Cleanup
require_once "$IP/extensions/RecentChangesCleanup/RecentChangesCleanup.php";
$wgAvailableRights[] = 'recentchangescleanup';
$wgGroupPermissions['sysop']['recentchangescleanup'] = true;
$wgGroupPermissions['recentchangescleanup']['recentchangescleanup'] = true;

# CheckUser
# https://www.mediawiki.org/wiki/Extension:CheckUser
# Requires install, see scripts/vagrant_bootstrap.sh
require_once "$IP/extensions/CheckUser/CheckUser.php";
$wgGroupPermissions['sysop']['checkuser'] = true;

# Preventing confusable usernames from being created.
# It blocks the creation of accounts with mixed-script,
# confusing and similar usernames.
# https://www.mediawiki.org/wiki/Extension:AntiSpoof
# Requires install, see scripts/vagrant_bootstrap.sh
require_once "$IP/extensions/AntiSpoof/AntiSpoof.php";
$wgSharedTables[] = 'spoofuser';

# Provides a special page to allow administrators to do a global string
# find-and-replace on both the text and titles of the wiki's content pages.
require_once "$IP/extensions/ReplaceText/ReplaceText.php";
$wgGroupPermissions['bureaucrat']['replacetext'] = true;

# Allow privileged users to set specific controls on actions by users,
# such as edits, and create automated reactions for certain behaviors.
# https://www.mediawiki.org/wiki/Extension:AbuseFilter
require_once "$IP/extensions/AbuseFilter/AbuseFilter.php";
$wgGroupPermissions['sysop']['abusefilter-modify'] = true;
$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
$wgGroupPermissions['*']['abusefilter-view'] = true;
$wgGroupPermissions['*']['abusefilter-log'] = true;
$wgGroupPermissions['sysop']['abusefilter-private'] = true;
$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
$wgGroupPermissions['sysop']['abusefilter-revert'] = true;

#
# Hitchwiki extensions
#
#require_once "$IP/extensions/HitchwikiMap/HitchwikiMap.php";

#
# Settings for preventing spam on MediaWiki
#
require_once "mediawiki-spam.php";