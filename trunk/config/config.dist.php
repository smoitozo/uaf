<?php
/**
 * @file
 * Configuration file for the User Attribute Fetcher
 */

// REGISTRY OF AUTHORIZED AGENTS --------------------------------

// Note: one way to generate these keys is using the following command on the unix shell:
//  tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
// Once an agent is configured you need to tell the agent what it's shared secret is.
//
// $CONFIG['authorized_agents']['UNIQUE_SHARED_SECRET_FOR_THIS_AGENT'] = array(
//  'name'          => 'Agent name (usually a fully qualified host name)',
//  'desc'          => 'A description of the agent.',
//  'contact'       => 'The e-mail of the contact person for the agent',
//  'entitledAttributes' => array(
//      'Attribute Name 1',
//      'Attribute Name 2',
//    ),
//  );

$CONFIG['authorized_agents']['1234567890abcdefghijk'] = array(
  'name'          => 'idp.example.com',
  'desc'          => 'simpleSAMLphp IdP at idp.example.com',
  'contact'       => 'fedmaster@idp.example.com',
  'entitledAttributes' => array(
      'roles',
      'name',
    ),
  );

// ONE TIME CONFIGURATION SETTINGS --------------------------------

// Run in debug mode?
$CONFIG['debug'] = TRUE;

// The domain that will be appended to each role
$CONFIG['realm'] = '@www.example.com';


// Where's Drupal 7 boostrap file?
$CONFIG['user_app'] = array(
    'module' => 'drupal7',
    'path'  => '/path/to/drupal7',
    'attributeNames' => array(
        'uid',
        'roles',
        'email',
        'name',
      ),
  );

// To configure other applications, assuming there is a module
//
// $CONFIG['user_app'] = array(
//    'module' => 'module_name',
//    'module_path' => 'path/to/app',
//    'module_param1' => 'value',
//    'attributeNames' => array(
//        'uid',
//        'roles',
//        'email',
//        'name',
//      ),
//  );


// For instance, if there were a wordpress module
/*
$CONFIG['user_app'] = array(
    'module' => 'wordpressX',
    'wordpress_path' => '/path/to/wordpress-X.X',
    'attributeNames' => array(
        'uid',
        'roles',
        'email',
        'name',
      ),
  );
*/
