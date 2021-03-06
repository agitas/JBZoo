<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBMenuHelper
 */
class JBMenuHelper extends AppHelper
{
    /**
     * Create new item
     * @param AppMenuItem $parentItem
     * @param array       $options
     * @return AppTreeItem
     */
    public function addItem(AppMenuItem $parentItem, array $options)
    {
        $options = $this->app->data->create($options);

        $task = $options->get('task', 'index');
        $ctrl = $options->get('controller');

        $item = $this->app->object->create('AppMenuItem', [
            $ctrl . '-' . $task,
            JText::_('JBZOO_ADMIN_MENU_' . $ctrl . '_' . $task),
            $this->app->link(['controller' => $ctrl, 'task' => $task])
        ]);

        return $parentItem->addChild($item);
    }

    /**
     * Add new tab
     * @param string $name
     * @param string $ctrlName
     * @param string $class
     * @param string $text
     * @return mixed
     */
    public function addTab($name, $ctrlName, $class = null, $text = null)
    {
        $text = $text ? '<span class="icon"> </span>' . $text : '<span class="icon"> </span>';

        $tab = $this->app->object->create('AppMenuItem', [
            $name . '-index',
            $text,
            $this->app->link(['controller' => $ctrlName, 'task' => 'index']),
            ['class' => $class]
        ]);

        $this->getAdmin()->addChild($tab);

        return $tab;
    }

    /**
     * Get admin menu
     * @return AppMenu
     */
    public function getAdmin()
    {
        return $this->app->menu->get('nav');
    }

    /**
     * Render menu
     * @return string
     */
    public function renderAdmin()
    {
        $menu = $this->getAdmin();

        $menu
            ->addFilter(['JBMenuHelper', 'filterZooActive'])
            ->addFilter(['JBMenuHelper', 'filterJBZooActive'])
            ->addFilter(['JBMenuHelper', 'filterNames'])
            ->addFilter(['JBMenuHelper', 'filterVersions'])
            ->applyFilter();

        $menuHtml = $menu->render(['AppMenuDecorator', 'index']);

        return '<div id="nav"><div class="bar"></div>' . $menuHtml . '</div>';
    }

    /**
     * Filter: Zoo menu item activator
     * @param AppMenuItem $item
     */
    public static function filterZooActive(AppMenuItem $item)
    {
        // init vars
        $id = '';
        $app = App::getInstance('zoo');
        $application = $app->zoo->getApplication();
        $controller = $app->jbrequest->getCtrl();
        $task = $app->jbrequest->getWord('task');
        $classes = [];

        // application context
        if (!empty($application)) {
            $id = $application->id . '-' . $controller;
        }

        // application configuration
        if ($controller == 'configuration' && $task) {
            if (in_array($task, ['importfrom', 'import', 'importcsv', 'importexport'])) {
                $id .= '-importexport';
            } else {
                if ($task != 'index') {
                    $id .= '-' . $task;
                }
            }
        }

        // new application
        if ($controller == 'new') {
            $id = 'new';
        }

        // application manager
        if ($controller == 'manager') {
            $id = 'manager';
            if (in_array($task,
                ['types', 'addtype', 'edittype', 'editelements', 'assignelements', 'assignsubmission'])) {
                $id .= '-types';
            } elseif ($task) {
                $id .= '-' . $task;
            }
        }

        // save current class attribute
        $class = $item->getAttribute('class');
        if (!empty($class)) {
            $classes[] = $class;
        }

        // set active class
        if ($item->getId() == $id || $item->hasChild($id, true)) {
            $classes[] = 'active';
        }

        // replace the old class attribute
        $item->setAttribute('class', implode(' ', $classes));
    }

    /**
     * Filter: JBZoo menu item activator
     * @param AppMenuItem $item
     */
    public static function filterJBZooActive(AppMenuItem $item)
    {
        if (strpos($item->getId(), 'jb') !== 0) {
            return;
        }

        // init vars
        $app = App::getInstance('zoo');
        $controller = $app->jbrequest->getCtrl();
        $classes = [];

        $id = $controller . '-index';

        // save current class attribute
        $classes[] = $item->getAttribute('class');

        // set active class
        if (strpos($id, $item->getId()) === 0 || $item->hasChild($id, true)) {
            $classes[] = 'active';
        }

        // replace the old class attribute
        $item->setAttribute('class', implode(' ', $classes));
    }

    /**
     * Filter: Menu item names corrector
     * @param AppMenuItem $item
     */
    public static function filterNames(AppMenuItem $item)
    {
        if (!(in_array($item->getId(), ['new', 'manager']) || strpos($item->getId(), 'jb') === 0)) {
            $item->setName(htmlspecialchars($item->getName(), ENT_QUOTES, 'UTF-8'));
        }
    }

    /**
     * Filter: Add versions
     * @param AppMenuItem $item
     */
    public static function filterVersions(AppMenuItem $item)
    {
        $app = App::getInstance('zoo');

        if ($item->getId() == 'manager') {
            if ($version = $app->zoo->version()) {
                $item->setAttribute('data-zooversion', $version);
            }
        }

        if (strpos($item->getId(), 'jb') === 0) {
            if ($version = $app->jbversion->jbzoo()) {
                $version = strip_tags($version);
                $item->setAttribute('data-jbzooversion', $version);
            }
        }
    }

}
