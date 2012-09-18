<?php
defined('_JEXEC') or die;

/**
 * Zqj30coms Table class
 *
 */
class Zqj30comsTableZqj30com extends JTable
{
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) 
	{
		parent::__construct('#__zqj25_zqj30coms', 'id', $db);
	}
	
	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see		JTable:bind
	 * @since	1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
	
		return parent::bind($array, $ignore);
	}
		
	/**
	 * Overload the store method for the items table.
	 * 
	 * @param	boolean		Toggle whether null values should be updated.
	 * @return	boolean		True on success, false on failure.
	 * @since 	1.6
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id) {
			// Existing item
			$this->modified		= $date->toMySQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New item. Created and created_by field can be set by the user
			// so we don't touch either of these if they are set.
			if (!intval($this->created)) {
				$this->created = $date->toMySQL();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}
		
		// Verify that the alias is unique
		$table = JTable::getInstance('Zqj30com', 'Zqj30comsTable');
		if ($table->load(array('alias'=>$this->alias,'catid'=>$this->catid))
			&& ($table->id != $this->id || $this->id==0)) {
				$this->setError(JText::_('COM_ZQJ30COMS_ERROR_UNIQUE_ALIAS'));
				return false;
			}
			// Attempt to store the user data.
			return parent::store($updateNulls);
	}
	
	/**
	 * Overloaded check method to ensure data integrity.
	 * 
	 * @return	boolean		True on success.
	 */
	public function check()
	{
		
		// check for valid name
		if (trim($this->title) == '') {
			$this->setError(JText::_('COM_ZQJ30COMS_ERR_TABLES_TITLE'));
			return false;
		}
		// check for existing name
		$query = 'SELECT id FROM #__zqj25_zqj30coms WHERE title = '.
			$this->_db->Quote($this->title).' AND catid = '.(int) $this->catid;
		$this->_db->setQuery($query);
		
		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::_('COM_ZQJ30COMS_ERR_TABLES_NAME'));
			return false;
		}
		
		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		
		// Check the publish down date is not earlier than publish up
		if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}
		
		return true;
	}
}