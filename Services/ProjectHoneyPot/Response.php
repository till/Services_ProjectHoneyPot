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
 * Services_ProjectHoneyPot_Exception
 */
require_once 'Services/ProjectHoneyPot/Exception.php';

/**
 * A class to parse the response from ProjectHoneyPot.org
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
class Services_ProjectHoneyPot_Response
{
    /**
     * HR representations to match the int type returned from the API
     */
    const RESPONSE_HR_SEARCHENGINE   = 'Search Engine';
    const RESPONSE_HR_SUSPICIOUS     = 'Suspicious';
    const RESPONSE_HR_HARVESTER      = 'Harvester';
    const RESPONSE_HR_COMMENTSPAMMER = 'Comment Spammer';
    
    /**
     * Parse the response into an array or object.
     *
     * It will look like the following, the object response follows the same
     * pattern (and an example is therefor omitted):
     * <ul>
     *   <li>$response['suspicious'] -> Is the host/ip suspicious?</li>
     *   <li>$response['harvester'] -> Is the host/ip a known harvester?</li>
     *   <li>$response['comment_spammer'] -> Is this a known comment spammer?</li>
     *   <li>$response['search_engine'] -> Is this a search engine?</li>
     *   <li>$response['debug'] -> The entire response for debugging purposes.</li>
     *   <li>$response['last_activity'] -> The last known recorded activity.</li>
     *   <li>$response['score'] -> A score allocated by ProjectHoneyPot.</li>
     *   <li>$response['type'] -> The type (from ProjectHoneyPot) of host.</li>
     *   <li>$response['type_hr'] -> Human-readable equivalent of 'type'.</li>
     * </ul>
     *
     * @param SimpleXML $respObj The response.
     * @param String    $format  Do we return an array or object?
     * @param boolean   $debug   Include entire response from API or not?
     *
     * @return mixed
     * @see    Services_ProjectHoneyPot_Result
     */
    static function parse($respObj, $format = 'array', $debug = false)
    {
        $ip = $respObj->answer[0]->address;

        list($foobar, $last_activity, $score, $type) = explode('.', $ip);

        if ($format == 'array') {
            
            $response                    = array();
            $response['suspicious']      = null;
            $response['harvester']       = null;
            $response['comment_spammer'] = null;
            $response['search_engine']   = null;
    
            if ($debug === true) {
                $response['debug'] = $respObj;
            } else {
                $response['debug'] = null;
            }
    
            $type_hr = '';
            switch ($type) {
            case 0:
                $type_hr .= self::RESPONSE_HR_SEARCHENGINE;
                
                $score         = null;
                $last_activity = null;
                
                $response['seach_engine'] = 1;
                break;
    
            case 1:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS;
                
                $response['suspicious'] = 1;
                break;
    
            case 2:
                $type_hr .= self::RESPONSE_HR_HARVESTER;
                break;
    
            case 3:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS . ' & ';
                $type_hr .= self::RESPONSE_HR_HARVESTER;
                
                $response['suspicious'] = 1;
                $response['harvester']  = 1;
                break;
    
            case 4:
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response['comment_spammer'] = 1;
                break;
    
            case 5:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS . ' & ';
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response['suspicious']      = 1;
                $response['comment_spammer'] = 1;
                break;
    
            case 6:
                $type_hr .= self::RESPONSE_HR_HARVESTER . ' & ';
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response['harvester']       = 1;
                $response['comment_spammer'] = 1;
                break;
    
            case 7:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS . ' & ';
                $type_hr .= self::RESPONSE_HR_HARVESTER . ' & ';
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response['suspicious']      = 1;
                $response['harvester']       = 1;
                $response['comment_spammer'] = 1;
                break;
    
            default:
                throw new Services_ProjectHoneyPot_Response_Exception(
                    'Unknown type ' . $type . ' in response. API changes?',
                    self::ERR_UNKNOWN_API
                );
            }
    
            $response['last_activity'] = $last_activity;
            $response['score']         = $score;
            $response['type']          = $type;
            $response['type_hr']       = $type_hr;
        
        } elseif ($format == 'object') {

            /* Services_ProjectHoneyPot_Result */
            include_once 'Services/ProjectHoneyPot/Response/Result.php';
            if (!class_exists('Services_ProjectHoneyPot_Response_Result')) {
                throw new Services_ProjectHoneyPot_Response_Exception(
                    'Unable to load file: Result.php'
                    Services_ProjectHoneyPot::ERR_INTERNAL);
            }
            
            $response = new Services_ProjectHoneyPot_Response_Result;

            $response->suspicious      = null;
            $response->harvester       = null;
            $response->comment_spammer = null;
            $response->search_engine   = null;
    
            if ($debug === true) {
                $response->debug = $respObj;
            } else {
                $response->debug = null;
            }
    
            $type_hr = '';
            switch ($type) {
            case 0:
                $type_hr .= self::RESPONSE_HR_SEARCHENGINE;
                
                $score         = null;
                $last_activity = null;
                
                $response->seach_engine = 1;
                break;
    
            case 1:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS;
                
                $response->suspicious = 1;
                break;
    
            case 2:
                $type_hr .= self::RESPONSE_HR_HARVESTER;
                break;
    
            case 3:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS . ' & ';
                $type_hr .= self::RESPONSE_HR_HARVESTER;
                
                $response->suspicious = 1;
                $response->harvester  = 1;
                break;
    
            case 4:
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response->comment_spammer = 1;
                break;
    
            case 5:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS . ' & ';
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response->suspicious      = 1;
                $response->comment_spammer = 1;
                break;
    
            case 6:
                $type_hr .= self::RESPONSE_HR_HARVESTER . ' & ';
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response->harvester       = 1;
                $response->comment_spammer = 1;
                break;
    
            case 7:
                $type_hr .= self::RESPONSE_HR_SUSPICIOUS . ' & ';
                $type_hr .= self::RESPONSE_HR_HARVESTER . ' & ';
                $type_hr .= self::RESPONSE_HR_COMMENTSPAMMER;
                
                $response->suspicious      = 1;
                $response->harvester       = 1;
                $response->comment_spammer = 1;
                break;
    
            default:
                throw new Services_ProjectHoneyPot_Response_Exception(
                    'Unknown type ' . $type . ' in response. API changes?',
                    self::ERR_UNKNOWN_API
                );
            }
    
            $response->last_activity = $last_activity;
            $response->score         = $score;
            $response->type          = $type;
            $response->type_hr       = $type_hr;
            
        } else {
            throw new Services_ProjectHoneyPot_Response_Exception(
                'Unknown format: ' . $format,
                Services_ProjectHoneyPot::ERR_INTERNAL
            );
        }
        return $response;
    }
}
?>
