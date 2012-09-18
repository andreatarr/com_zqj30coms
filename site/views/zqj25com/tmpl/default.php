<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="page-zqj30com<?php echo $this->pageclass_sfx; ?>">
	<?php if ( $this->params->get( 'show_page_title', 1 ) ) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_title')) : ?>
		<h2>
			<?php echo $this->escape($this->item->title); ?>
		</h2>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_create_date')) : ?>
		<p>
			<?php echo JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
		</p>	
	<?php endif; ?>
	
	<div>	
		<?php echo JHtml::_('content.prepare', $this->item->description, '', 'com_zqj30coms.category'); ?>
	</div>

</div>
