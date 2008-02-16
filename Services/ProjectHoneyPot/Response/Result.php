<?php
/**
 * Copyright (c) 2008, Till Klampaeckel
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *  * Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice, this
 *    list of conditions and the following disclaimer in the documentation and/or
 *    other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5
 *
 * @category Services
 * @package  Services_ProjectHoneyPot
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  CVS: $Id$
 * @link     http://code.google.com/p/services-projecthoneypot/
 */

/**
 * A class to return the result from ProjectHoneyPot.org
 *
 * @category Services
 * @package  Services_ProjectHoneyPot
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://code.google.com/p/services-projecthoneypot/
 * @uses     Net_CheckIP2
 * @uses     Net_DNS
 */
class Services_ProjectHoneyPot_Response_Result
{
    private $_store = array('suspicious' = false,
        'harvester' => false,
        'comment_spammer' => false,
        'search_engine' => false,
        'last_activity' => null,
        'score' => null,
        'type' => null,
        'type_hr' => null);

    /**
     * __construct
     */
    private __construct()
    {
    }

    /**
     * magical __set
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return mixed
     * @uses   self::$_store
     */
    public function __set($name, $value)
    {
        if (isset($this->_store[$name])) {
            return $this->_store[$name] = $value;
        }
        return null;
    }

    /**
     * magical __get
     *
     * @param string $name The name.
     *
     * @return mixed
     * @uses   self::$_store
     */
    public function __get($name)
    {
        if (isset($this->_store[$name])) {
            return $this->_store[$name];
        }
        return null;
    }

    /**
     * Is comment spammer? true/false
     *
     * @return boolean
     */
    public function isCommentSpammer()
    {
        return $this->_store['comment_spammer'];
    }

    /**
     * Is a harvester? true/false
     *
     * @return boolean
     */
    public function isHarvester()
    {
        return $this->_store['harvester'];
    }

    /**
     * Is a search engine? true/false
     *
     * @return boolean
     */
    public function isSearchEngine()
    {
        return $this->_store['search_engine'];
    }

    /**
     * Returns all known about this host/ip.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_store;
    }

    /**
     * Get the last activity. An integer representation (of the number of days - 0
     * to 255) or null (if there is none recorded)..
     *
     * @return mixed
     */
    public function getLastActivity()
    {
        return $this->_store['last_activity'];
    }

    /**
     * Get the score of the host/ip.
     *
     * <quote>
     * Threat Scores are a rough guide to determine the threat a particular IP
     * address may pose to your site. Threat Scores should be treated as a rough
     * measure. Threat Scores range from 0-255, however they follow a logrithmic
     * scale which makes it extremely unlikely that a threat score over 200 will
     * ever be returned.
     * </quote>
     *
     * @return mixed
     * @link   http://www.projecthoneypot.org/httpbl_api.php
     */ 
    public function getScore()
    {
        return $this->_store['score'];
    }

    /**
     * Returns which type the current host/ip is. The result is always an array.
     *
     * @return array
     */
    public function getType()
    {
        return array('type' => $this->_store['type'],
            $this->_store['type_hr']);
    }
}
?>
