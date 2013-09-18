<?php
defined('_JEXEC') or die;

/**
 * Zqj30coms helper.
 */
class Zqj30comsHelper
{
	/**
	 * Configure the Linkbar.
	 * 
	 * @param string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'zqj30coms')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_ZQJ30COMS_SUBMENU_ZQJ30COMS'),
			'index.php?option=com_zqj30coms&view=zqj30coms',
			$vName == 'zqj30coms'
			);
		JSubMenuHelper::addEntry(
			JText::_('COM_ZQJ30COMS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_zqj30coms',
			$vName == 'categories'
		);
		if ($vName=='categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_('COM_ZQJ30COMS')),
				'zqj30coms-categories'
			);
		}			
	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 * 
	 * @param	int		The Category ID
	 * 
	 * @return	JObject	
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		
			if (empty($categoryId)) {
			$assetName = 'com_zqj30coms';
			$level = 'component';
		} else {
			$assetName = 'com_zqj30coms.category.'.(int) $categoryId;
			$level = 'category';
		}

		$actions = JAccess::getActions('com_zqj30coms', $level);

		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
			
		return $result;
	}
}