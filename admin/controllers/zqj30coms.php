<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * List controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_zqj30coms
 * @since		1.6
 */
class Zqj30comsControllerZqj30coms extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Zqj30com', $prefix = 'Zqj30comModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

}