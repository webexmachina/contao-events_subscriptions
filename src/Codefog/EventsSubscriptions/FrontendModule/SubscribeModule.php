<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package events_subscriptions
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

namespace Codefog\EventsSubscriptions\FrontendModule;

use Contao\Environment;
use Codefog\EventsSubscriptions\EventsSubscriptions;

class SubscribeModule extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_eventsubscribe';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### EVENT SUBSCRIBE FORM ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('events', \Input::get('auto_item'));
		}

		// Return if there is no logged user or no event provided
		if (!\Input::get('events'))
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->Template->showForm = true;
		$time = time();
		$objEvent = $this->Database->prepare("SELECT id FROM tl_calendar_events WHERE (id=? OR alias=?)" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1" : ""))
								   ->limit(1)
								   ->execute((is_numeric(\Input::get('events')) ? \Input::get('events') : 0), \Input::get('events'), $time, $time);

		// Return if the event was not found
		if (!$objEvent->numRows)
		{
			$this->Template->showForm = false;
			return;
		}

		$this->import('FrontendUser', 'User');

		$blnSubscribe = EventsSubscriptions::checkSubscription($objEvent->id, $this->User->id);
		$strFormId = 'event_subscribe_' . $this->id;
		$this->Template->message = '';

		// Display the message
		if ($_SESSION['EVENT_SUBSCRIBE_MESSAGE'] != '')
		{
			$this->Template->message = $_SESSION['EVENT_SUBSCRIBE_MESSAGE'];
			unset($_SESSION['EVENT_SUBSCRIBE_MESSAGE']);
		}

		$this->Template->subscribed = !$blnSubscribe;
		$this->Template->formId = $strFormId;
		$this->Template->action = Environment::get('request');
		$this->Template->submit = $blnSubscribe ? $GLOBALS['TL_LANG']['MSC']['eventSubscribe'] : $GLOBALS['TL_LANG']['MSC']['eventUnsubscribe'];

		// Process the form
		if (\Input::post('FORM_SUBMIT') == $strFormId)
		{
			if (!FE_USER_LOGGED_IN)
			{
				$this->jumpToOrReload($this->jumpTo_login);
			}

			// Subscribe user
			if ($blnSubscribe)
			{
				if (EventsSubscriptions::subscribeMember($objEvent->id, $this->User->id))
				{
					if (!$this->jumpTo_subscribe)
					{
						$_SESSION['EVENT_SUBSCRIBE_MESSAGE'] = $GLOBALS['TL_LANG']['MSC']['eventSubscribed'];
					}

					$this->jumpToOrReload($this->jumpTo_subscribe);
				}
			}

			// Unsubscribe user
			else
			{
				if (EventsSubscriptions::unsubscribeMember($objEvent->id, $this->User->id))
				{
					if (!$this->jumpTo_unsubscribe)
					{
						$_SESSION['EVENT_SUBSCRIBE_MESSAGE'] = $GLOBALS['TL_LANG']['MSC']['eventUnsubscribed'];
					}

					$this->jumpToOrReload($this->jumpTo_unsubscribe);
				}
			}
		}
	}
}