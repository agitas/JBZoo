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
 * Class JBFilterElementRatingRanges
 */
class JBFilterElementRatingRanges extends JBFilterElementRating
{

    /**
     * Render HTML
     * @return string|null
     */
    function html()
    {
        $values = $this->_getValues();

        return $this->app->jbhtml->select(
            $this->_createOptionsList($values),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * Get values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        $start = (int)$this->_config->get('stars', 5);

        $values = array();

        for ($i = 0; $i <= $start; $i++) {

            if ($i + 1 <= $start) {

                $values[] = array(
                    'value' => $i . '/' . ($i + 1),
                    'text'  => JText::_('JBZOO_FROM') . ' ' . $i . ' ' . JText::_('JBZOO_TO') . ' ' . ($i + 1),
                    'count' => null,
                );

            }
        }

        return $values;
    }

}
