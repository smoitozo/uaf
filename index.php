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
 * See the README and INSTALL for more information.
 */

// Load up the configuration.
require_once('config/config.php');

// Execute the script.
require_once('lib/uaf.php');