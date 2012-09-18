<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

$controller = JController::getInstance('Zqj30coms');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
