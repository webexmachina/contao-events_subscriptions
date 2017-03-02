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

/**
 * Table tl_calendar_events_subscription
 */
$GLOBALS['TL_DCA']['tl_calendar_events_subscription'] = [

    // Config
    'config'   => [
        'dataContainer'   => 'Table',
        'ptable'          => 'tl_calendar_events',
        'onload_callback' => [
            ['Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer', 'displaySummary'],
        ],
        'sql'             => [
            'keys' => [
                'id'     => 'primary',
                'pid'    => 'index',
                'member' => 'index',
            ],
        ],
    ],

    // List
    'list'     => [
        'sorting'           => [
            'mode'                  => 4,
            'disableGrouping'       => true,
            'headerFields'          => ['title', 'startDate', 'startTime', 'endDate', 'endTime', 'published'],
            'child_record_callback' => [
                'Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer',
                'listMembers',
            ],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{member_legend},member,addedBy',
    ],

    // Fields
    'fields'   => [
        'id'        => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'       => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'    => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'member'    => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member'],
            'exclude'          => true,
            'inputType'        => 'select',
            'foreignKey'       => 'tl_member.username',
            'options_callback' => [
                'Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer',
                'getMembers',
            ],
            'eval'             => [
                'mandatory'          => true,
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'save_callback'    => [
                ['Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer', 'checkIfAlreadyExists'],
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'addedBy'   => [
            'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['addedBy'],
            'default'    => \Contao\BackendUser::getInstance()->id,
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_user.name',
            'eval'       => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'lastEmail' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastEmail'],
            'flag'  => 8,
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];