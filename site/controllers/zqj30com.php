<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.user.helper');

class Zqj30comsControllerZqj30com extends JControllerForm
{
	protected $view_item = 'form';
	
	public function edit($key = null, $urlVar = 'sub_id')
	{
		$result = false;
		$itemid	= JRequest::getInt('Itemid');
		$catid	= JRequest::getInt('id');
		if (($catid) && ($this->allowEdit($catid))) {
			$result = parent::edit($key, $urlVar);
			
			// Check in the item, since it was checked out in the edit method
			$this->getModel()->checkIn(JRequest::getInt($urlVar));
		}	
		return $result;
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$itemId = JRequest::getInt('Itemid');
		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}
		return $append;
	}
	
	protected function allowEdit($catid)
	{
		return JFactory::getUser()->authorise('core.edit', $this->option.'.category.'.$catid);
	}
	
	public function getModel($name = 'form', $prefix = '', $config = array('ignore_request'=> true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function subscribe ($key = null, $urlVar = 'sub_id')
	{
		// Check that user is authorised
		if (!JFactory::getUser()->authorise('core.edit', 'com_zqj30coms.category.' . $this->category->id)) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}	
		
		// Check that form data is valid
		if (!$this->validate()) {
			return false;
		}
		
		// Add user to group if not already a member
		$model = $this->getModel();
		$user	= JFactory::getUser();
		$id		= JRequest::getInt('sub_id');
		$subscription = $model->getItem($id);
		
		// Set redirect without id in case of an error
		$this->setRedirect(JRoute::_('index.php?option=com_zqj30coms&amp;view=form&amp;layout=thankyou'));
		if (!in_array($subscription->group_id, $user->groups)) {
			if (!JUserHelper::addUserToGroup($user->id, $subscription->group_id)) {
				$this->setMessage($model->getError(), 'error');
				return false;
			}
		}

		// Add or update row to mapping table
		if (!$result = $model->updateSubscriptionMapping($subscription, $user)) {
			$this->setMessage($model->getError(), 'error');
			return false;
		}
		
		// At this point, we have succeeded
		// Trigger the onAfterSubscribe event
		JDispatcher::getInstance()->trigger('onAfterSubscribe', array(&$subscription));
		
		// Include id in redirect for success message
		$this->setRedirect(JRoute::_('index.php?option=com_zqj30coms&amp;view=form&amp;layout=thankyou&amp;sub_id='.$id)) ;
		return true;
	}
	
	protected function validate()
	{
		$app	= JFactory::getApplication();
		$model	= $this->getModel();
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$form	= $model->getForm($data, false);
		$validData	= $model->validate($form, $data);
		$recordId	= JRequest::getInt('sub_id');
		
		// Check for validation errors
		if ($validData === false) {
			// Get the validation messages
			$errors = $model->getErrors();
			
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errros[$i], 'warning');
				}
			}
			
			// Save the data in the session
			if (isset($data[0])) {
				$app->setUserState($context.'.data', $data);
			}
			
			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'.&view='.$this->view_item.
			$this->getRedirectToItemAppend($recordID, 'sub_id'), flase));
			return false;
		}
		
		return true;
	}
	
}