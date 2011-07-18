<?php
/**
 * @file
 * User Attribute Fetcher
 *
 * Quick and dirty middleware app that the returns the attributes of a user from the target system (e.g., Drupal).
 *
 * @author Steve Moitozo <steve_moitozo@sil.org>
 *
 * Copyright SIL International
 * Licensed under the GPL v2.0
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Written for the Polder Consortium
 * http://www.polderconsortium.org
 *
 * Project Web site
 * http://code.google.com/p/uaf/
 *
 * See the README for more information.
 *
 * Basic rule of execution: die silently, unless in debug mode.
 */

require_once('lib/uaf_module.php');

// SANITY CHECKS =======================================================

// Require SSL
if (!isset($_SERVER['HTTPS'])) {
  bailout('All request must come over HTTPS.');
}

// Two parameters are required for servicing requests.

// 1) Validate the shared secret (sharedsec).

if (!(isset($_REQUEST['sharedsec']) && $_REQUEST['sharedsec'])) {
   bailout('The request is missing the required shared secret.');
}

if(!is_array($CONFIG['authorized_agents'])) {
  bailout('No authorized agents have been configured.');
}

// Look up the agent
if (isset($CONFIG['authorized_agents'][$_REQUEST['sharedsec']]) && $CONFIG['authorized_agents'][$_REQUEST['sharedsec']]) {
  $AGENT = $CONFIG['authorized_agents'][$_REQUEST['sharedsec']];
}
else {
  bailout('Failed to locate any viable shared secrets in the configuration.');
}

// 2) Check the user identifier (userid).

if (!(isset($_REQUEST['userid']) && $_REQUEST['userid'])) {
  bailout('The request is missing a user identifier.');
}

// Test the array of modules and make sure the configured modules exist.
if (!isset($CONFIG['user_app']) || !is_array($CONFIG['user_app'])) {
  bailout('No user app configured.');
}

if(!(isset($CONFIG['user_app']['module']) || !file_exists('modules/' . $CONFIG['user_app']['module'] .'/'. $CONFIG['user_app']['module'] . '.module'))) {
  bailout($CONFIG['user_app']['module'] . '.module does not exist in the modules directory.');
}

// Do a sanity check on mode, if sent
if (isset($_REQUEST['mode']) && $_REQUEST['mode']) {

  switch($_REQUEST['mode']) {
    case 'PHP':
      $strMode = 'PHP';
      break;

    case 'JSON':
      $strMode = 'JSON';
      break;

    default:
      $strMode = 'JSON';
  }

}

// EXECUTION =======================================================


// Include the module and instantiate the object.
$strModule = $CONFIG['user_app']['module'];
require_once('modules/' . $strModule .'/'. $strModule . '.module');
$strModuleClassName = $strModule . '_module';
$objModule = new $strModuleClassName($CONFIG, $AGENT);

$arrUserAttributes = filterAttributes($objModule->fetchUserAttributes($_REQUEST['userid']), $AGENT);

$arrUserAttributes = appendRealm($arrUserAttributes, $CONFIG['realm']);

$strAttributes = serializeAttributes($arrUserAttributes, $strMode);

// Output the attributes and exit; our work here is done.
die($strAttributes);



// HELPER FUNCTIONS =================================================



/**
 * Cease execution.
 *
 * If debug mode is turned on the script will display the error and
 * exit. Otherwise it will exit silently.
 *
 * @param
 *  the error
 */
function bailout($strError=null) {
  global $CONFIG;

  if (isset($CONFIG['debug']) && $CONFIG['debug']) {
    die($strError);
  }
  else {
    die;
  }
}



/**
 * Serializes the array of attributes.
 *
 * @param
 *  The array of attributes.
 *
 * @param
 *  The type of serialization (PHP, JSON).
 *
 * @return
 *  The serialized string of attributes.
 */
function serializeAttributes($arrAttrs, $strMode=JSON) {
  $strAttrs = null;

  switch($strMode) {

    case 'PHP':
        $strAttrs = serialize($arrAttrs);
        break;

    case 'JSON':
        $strAttrs = json_encode($arrAttrs);
         break;

   default:
        $strAttrs = json_encode($arrAttrs);

  }

  return $strAttrs;
}



/**
 * Append the realm to all the attributes.
 *
 * @param
 *  The array of attributes.
 *
 * @param
 *  The agent configuration array.
 *
 * @return
 *  The array of attributes.
 */
function filterAttributes($arrAttrs, $arrAgent) {

  $arrReturn = NULL;

  // The agent is not entitled to any attributes, do not pass any through.
  if (!isset($arrAgent['entitledAttributes'])) {
    bailout('The requesting agent is not entitled to any attributes. Check your configuration for this agent.');

    // return NULL
    return $arrReturn;
  }

  if (is_array($arrAttrs)){

    foreach($arrAgent['entitledAttributes'] as $strAttributeName) {
      if (isset($arrAttrs[$strAttributeName]) && !is_array($arrAttrs[$strAttributeName])) {
        $arrReturn[$strAttributeName] = $arrAttrs[$strAttributeName];
      }
      elseif(isset($arrAttrs[$strAttributeName])) {
        // Recurse.
        $arrReturn[$strAttributeName] = appendRealm($arrAttrs[$strAttributeName], $arrAgent);
      }
    }

  }

  return $arrReturn;
}



/**
 * Append the realm to all the attributes.
 *
 * @param
 *  The array of attributes.
 *
 * @param
 *  The realm string.
 *
 * @return
 *  The array of attributes.
 */
function appendRealm($arrAttrs, $strRealm) {

  $arrReturn = null;

  if (is_array($arrAttrs)){

    $arrKeys = array_keys($arrAttrs);

    foreach($arrKeys as $mixedKey) {
      if (!is_array($arrAttrs[$mixedKey])) {
        $arrReturn[$mixedKey] = $arrAttrs[$mixedKey] . $strRealm;
      }
      else {
        // Recurse.
        $arrReturn[$mixedKey] = appendRealm($arrAttrs[$mixedKey], $strRealm);
      }
    }

  }

  return $arrReturn;
}