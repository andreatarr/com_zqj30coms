<?php
defined('_JEXEC') or die;

// Component helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Zqj30coms Component Category Tree
 * 
 * @static
 * @package		Joomla.Site
 * @subpackage	com_zqj30coms
 * @since		1.6
 */
class Zqj30comsCategories	extends JCategories
{
	public function __construct($options = array()) 
	{
		$options['table'] = '#_zqj30_zqj30coms';
		$options['extension'] = 'com_zqj30coms';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
}