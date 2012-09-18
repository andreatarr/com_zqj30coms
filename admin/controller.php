<?php
defined('_JEXEC') or die;

/**
 * Main Controller
 * 
 * @package		Joomla.Administrator
 * @subpackage	com_zqj30coms
 * @since		1.5
 */
class Zqj30comsController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'zqj30coms';
	/**
	 * Method to display a view.
	 * 
	 * @param	boolean $cachable	If true, the view output will be cached
	 * @param	array	$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 * 
	 * @return	JController	This object to support chaining
	 * @since 1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Loads this class, matching class name to the filename
		// could use require_once, but this is quicker
		JLoader::register('Zqj30comsHelper', JPATH_COMPONENT.'/helpers/zqj30coms.php');
		// Load the submenu using a method from this class.
		Zqj30comsHelper::addSubmenu(JRequest::getCmd('view', 'zqj30coms'));
		
		$view	= JRequest::getCmd('view', 'zqj30coms');
		$layout	= JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');
		
		// Check for edit form.
		if ($view == 'zqj30com' && $layout == 'edit' && !$this->checkEditId('com_zqj30coms.edit.zqj30com', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_zqj30coms&view=zqj30coms', false));
			
			return false;
		}
		
		parent::display();
		
		return $this;
	}
}