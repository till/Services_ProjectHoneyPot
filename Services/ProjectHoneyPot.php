<?php
/**
 * Copyright (c) 2007-2008, Till Klampaeckel
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
 * Include PEAR, Net_DNS and Net_CheckIP2
 * @ignore
 */
require_once 'PEAR.php';
require_once 'Net/DNS.php';
require_once 'Net/CheckIP2.php';

/**
 * Services_ProjectHoneyPot_Exception
 */
require_once 'Services/ProjectHoneyPot/Exception.php';

/**
 * Services_ProjectHoneyPot_Response
 */
require_once 'Services/ProjectHoneyPot/Response.php';

/**
 * A class to interface services provided by ProjectHoneyPot.org
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
class Services_ProjectHoneyPot
{
    /**
     * Error constant/codes thrown by the Exception class.
     * @see Services_ProjectHoneyPot_Exception
     */
    const ERR_NO_KEY       = 667;
    const ERR_NO_IP        = 668;
    const ERR_UNKNOWN_RESP = 669;
    const ERR_UNKNOWN_API  = 670;
    const ERR_INTERNAL     = 671;
    const ERR_USER         = 672;

    /**
     * @var $responseFormats array
     */
    static public $responseFormats = array('array', 'object');

    /**
     * @var  string $accesskey Your API access key.
     * @see  Services_ProjectHoneyPot::factory()
     * @see  Services_ProjectHoneyPot::setAccesskey()
     * @link http://www.projecthoneypot.org/httpbl_configure.php
     */
    protected $accesskey;

    /**
     * @var string The name of the dnsbl (provided by Project HoneyPot).
     * @see Services_ProjectHoneyPot::setDnsBlacklist
     */
    protected $dns_blacklist = 'dnsbl.httpbl.org';

    /**
     * @var object $resolver A Net_DNS object.
     * @see Services_ProjectHoneyPot::factory
     * @see Services_ProjectHoneyPot::query
     */
    protected $resolver;

    /**
     * @var bool $debug Yes (true) or no (false)?
     * @see Services_ProjectHoneyPot::factory
     * @see Services_ProjectHoneyPot::parseResponse
     */
    protected $debug = false;

    /**
     * @var string Not yet used.
     * @see Services_ProjectHoneyPot::setHoneypot
     * @see Services_ProjectHoneyPot::getHoneypot
     */
    protected $honeypot;
    
    /**
     * @var string Return an array or an object in the end?
     */
    protected $responseFormat = 'array'; // object

    /**
     * Enforce using Services_ProjectHoneyPot::factory().
     * 
     * @see self::factory()
     */
    private function __construct()
    {
    }

    /**
     * Initialize the class.
     *
     * @param string $accesskey The accesskey provided by Project HoneyPot.
     * @param mixed  $resolver  'null' or Net_DNS_Resolver.
     * @param bool   $debug     Enable debug, or maybe not? :-)
     * 
     * @return Services_ProjectHoneyPot
     * @throws Services_ProjectHoneyPot_Exception
     * @uses   Services_ProjectHoneyPot::$accesskey
     * @uses   Services_ProjectHoneyPot::$debug
     * @uses   Net_DNS_Resolver
     */
    static function factory($accesskey = null, $resolver = null, $debug = null)
    {
        $cls = new Services_ProjectHoneyPot;
        if (is_null($accesskey) === false) {
            $cls->accesskey = $accesskey;
        }
        $cls->setResolver($resolver);
        if ($debug !== null && is_bool($debug) === true) {
            $cls->debug = $debug;
        }
        return $cls;
    }

    /**
     * Set, or create a NET_DNS_Resolver for internal use.
     *
     * @param mixed $resolver 'null' or Net_DNS_Resolver
     *
     * @uses   self::$resolver
     * @return void
     * @throws Services_ProjectHoneyPot_Exception In case of a wrong object or an
     *                                            error on init.
     */
    public function setResolver($resolver = null)
    {
        if ($resolver === null) {
            $this->resolver = new Net_DNS_Resolver;
            if (PEAR::isError($this->resolver)) {
                throw new Services_ProjectHoneyPot_Exception(
                    $this->resolver->getMessage(),
                    $this->resolver->getCode()
                );
            }
            $this->resolver->nameservers = array('66.114.197.251');
        } elseif ($resolver instanceof Net_DNS_Resolver) {
            $this->resolver = $resolver;
        } else {
            throw new Services_ProjectHoneyPot_Exception(
                'Unknown class of type: ' . get_class($resolver),
                self::ERR_USER
            );
        }
    }
    
    /**
     * Set the format to retrieve the info in.
     * 
     * @param string $format Either 'array' or 'object'.
     * 
     * @return string
     * @throws Services_ProjectHoneyPot_Exception On unknown/unsupported format.
     */
    public function setResponseFormat($format)
    {
        if (!in_array($format, self::$responseFormats)) {
            throw new Services_ProjectHoneyPot_Exception(
                'Unknown or unsupported format: ' . $format,
                self::ERR_USER
            );
        }
        return $this->responseFormat = $format;
    }

    /**
     * Set the access key necessary to use this service.
     *
     * @param string $accesskey Another accesskey.
     * 
     * @return string $accesskey
     * @see    Services_ProjectHoneyPot::factory
     */
    public function setAccesskey($accesskey)
    {
        $this->accesskey = $accesskey;
        return $accesskey;
    }

    /**
     * Sets another (than the default) server to query.
     *
     * @param string $server The DNS server to query.
     * 
     * @return string $server
     */
    public function setDnsBlacklist($server)
    {
        $this->dns_blacklist = $server;
        return $server;
    }

    /**
     * Checks if the supplied IP is listed.
     * 
     * @param string $ip IP or hostname. Using an IP is more "expensive" because
     *                   we will need to resolve it.
     * 
     * @return array
     * @uses   Services_ProjectHoneyPot::getHostForLookup
     * @uses   Services_ProjectHoneyPot::parseResponse
     * @uses   Services_ProjectHoneyPot::$resolver
     * @see    Services_ProjectHoneyPot::factory
     * @throws Services_ProjectHoneyPot_Exception
     * @todo   Check multiple if host has 1+ IPs.
     */
    public function query($ip = '')
    {
        if ($ip == '') {
            throw new Services_ProjectHoneyPot_Exception(
                'Please supply an IP-address.',
                self::ERR_NO_IP
            );
        }
        if (Net_CheckIP2::check_ip($ip) !== true) {
            $resp = $this->resolver->query($ip);
            if (isset($resp->answer[0]->address) === false) {
                throw new Services_ProjectHoneyPot_Exception(
                    'Unable to resolve host.',
                    self::ERR_NO_IP
                );
            }
            $ip = $resp->answer[0]->address;
        }

        if ($this->accesskey === null) {
            throw new Services_ProjectHoneyPot_Exception(
                'No accesskey set.',
                self::ERR_NO_KEY
            );
        }
        $ip = $this->getHostForLookup($ip);

        $response = $this->resolver->query($ip);
        if ($response === false) {
            return $response;
        }
        if (is_object($response) === false) {
            throw new Services_ProjectHoneyPot_Exception(
                'Unknown response.',
                self::ERR_UNKNOWN_RESP
            );
        }
        if (isset($response->answer[0]->address) === false) {
            throw new Services_ProjectHoneyPot_Exception(
                'Unknown response object. API changes?',
                self::ERR_UNKNOWN_API
            );
        }
        return $this->parseResponse($response);
    }

    /**
     * Builds the host to query.
     *
     * @param string $ip The IP which needs to be resolved.
     * 
     * @return string $ip_query
     * @uses   Services_ProjectHoneyPot::$accesskey
     * @uses   Services_ProjectHoneyPot::$dns_blacklist
     */
    protected function getHostForLookup($ip)
    {
        $ip_query  = $ip . '.' . $this->accesskey;
        $ip_query  = implode('.', array_reverse(explode('.', $ip_query)));
        $ip_query .= '.' . $this->dns_blacklist;

        return $ip_query;
    }

    /**
     * Parses the response object into a 'readable' format
     *
     * For a more detailed response description, please see
     * {@see Services_ProjectHoneyPot_Response::parse()}
     *
     * @param object $respObj Whatever we received from the API.
     * 
     * @return array|Services_ProjectHoneyPot_Response_Result
     * @link   http://projecthoneypot.org/httpbl_api.php
     * @see    self::query()
     * @throws Services_ProjectHoneyPot_Exception
     */
    protected function parseResponse($respObj)
    {
        return Services_ProjectHoneyPot_Response::parse($respObj,
            $this->responseFormat, $this->debug);
    }

    /**
     * Sets an URL or an array of URLs to use for
     * redirecting later on, if the IP is found
     * 'guilty'. ;-)
     *
     * @param string|array $honeypot Set a honeypot (string), or multiple.
     * 
     * @return null|string|array $honeypot
     * @uses   Services_ProjectHoneyPot::$honeypot
     * @todo   Implement.
     */    
    public function setHoneypot($honeypot = null)
    {
        if (null !== $honeypot && $honeypot != '') {
            $this->honeypot = $honeypot;
        }
        return $honeypot;
    }

    /**
     * Mostly a placeholder for what's to come. Currently we return a honeypot
     * which has been supplied with self::setHoneypot before.
     * 
     * @todo   Implement retrieval of honeypots from projecthoneypot.org.
     * @return string|array|null
     * @uses   Services_ProjectHoneyPot::$honeypot
     * @see    self::setHoneypot()
     */
    public function getHoneyPot()
    {
        if ($this->honeypot == '') {
            return null;
        }
        return $this->honeypot;
    }

    /**
     * Enable debug on runtime.
     *
     * @param boolean $debug To debug (true), or not to debug (false)?
     * 
     * @return boolean
     * @uses   Services_ProjectHoneyPot::$debug
     */
    public function setDebug($debug)
    {
        if (is_bool($debug) === false) {
            return $this->debug;
        }
        $this->debug = $debug;
        return $debug;
    }
}
?>
