<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Item model
 * 
 * @package		Joomla.Administrator
 * @subpackage	com_zqj30coms
 * @since		2.5
 */
class Zqj30comsModelZqj30com extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages
	 * @since	2.5
	 */
	protected $text_prefix = 'COM_ZQJ30COMS';
	
	/**
	 * Method to test whether a record can be deleted.
	 * 
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to delete the record. 
	 * Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return;
			}
			$user = JFactory::getUser();
			
			if ($record->catid) {
				return $user->authorise('core.delete', 'com_zqj30coms.category.'.(int) $record->catid);
			} else {
				return parent::canDelete($record);
			}
		}
	}
	
	/**
	 * Method to test whether a record can have its state changed
	 * 
	 * @param	object		A record object
	 * @return	boolean		True if allowed to change the state of the record.
	 * defaults to the permission set in the component
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		
		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_zqj30coms.category.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}
	
	/**
	 * Returns a reference to the Table object, always creating it.
	 * 
	 * @param 	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Zqj30com', $prefix = 'Zqj30comsTable',  $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 * 
	 * @param	array	$data		An optional array of data for the form to interogate
	 * @param	boolean	$loaddata	True if the form is to load its own data (default case), false if not
	 * 
	 * @return	JForm				A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initiaize variables
		$app	= JFactory::getApplication();
		
		// Get the form.
		$form	= $this->loadForm('com_zqj30coms.zqj30com', 'zqj30com',
			array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		// Determine correct permissions to check.
		if ($this->getState('zqj30com.id')) {
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
		} else {
			// New record. can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}
		
		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			
			// Disable fields while saving.
			// The controller has already verified this is a record you can edit
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
		}
		
		return $form;
		
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 * 
	 * @return	mixed	The data for the form
	 * @since 	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_zqj30coms.edit.zqj30com.data', array());
		
		if (empty($data)) {
			$data = $this->getItem();
			
			// Prime some default values.
			if ($this->getState('zqj30com.id') == 0) {
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_zqj30coms.zqj30coms.filter.category_id')));
			}
		}
		
		return $data;
	}
	
	/**
	 * Prepare and sanitize the table prior to saving.
	 * 
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		$table->alias = JApplication::stringURLSafe($table->alias);
		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->title);
		}
	
		if (empty($table->id)) {
			// Set the values
		
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__zqj30_zqj30coms');
				$max = $db->loadResult();
		
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
		}
	}
	
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}	
	
}
