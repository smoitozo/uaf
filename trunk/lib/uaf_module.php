 <?php
/**
 * @file
 * Abstract module for the User Attribute Fetcher
 *
 * Modules that extend this module provide a way for uaf to query applications for user attributes
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


/**
 * uaf module class.
 *
 * This class provides the uaf_module interface.
 *
 * @package uaf
 * @version $Id$
 */
abstract class uaf_module {

  /**
   * The module configuration.
   *
   * @var array
   */
  protected $config = array();

  /**
   * The attribute names.
   *
   * @var array
   */
  protected $attributeNames = array();


  /**
   * Initialize the module.
   *
   * @param array $config  The configuration array for the module
   */
  function __construct($config) {

    $this->config = $config;

    if(!isset($config['user_app']['attributeNames']) || !is_array($config['user_app']['attributeNames'])) {
      die('ERROR: no attributeNames defined for this module.');
    }

    $this->attributeNames = $config['user_app']['attributeNames'];

  }

  /**
   * Fetch the user attributes from the application.
   *
   * @param string $userIdentifier The unique user identifier for looking up the user.
   * @return array the array of user attributes
   */
  abstract protected function fetchUserAttributes($userIdentifier);

}
