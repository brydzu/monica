<?php

namespace App\Models\CardDAV\Backends;

use Sabre\DAV;
use Sabre\CalDAV;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MonicaPrincipleBackend implements \Sabre\DAVACL\PrincipalBackend\BackendInterface
{
    /**
     * Returns a list of principals based on a prefix.
     *
     * This prefix will often contain something like 'principals'. You are only
     * expected to return principals that are in this base path.
     *
     * You are expected to return at least a 'uri' for every user, you can
     * return any additional properties if you wish so. Common properties are:
     *   {DAV:}displayname
     *   {http://sabredav.org/ns}email-address - This is a custom SabreDAV
     *     field that's actually injected in a number of other properties. If
     *     you have an email address, use this property.
     *
     * @param string $prefixPath
     * @return array
     */
    public function getPrincipalsByPrefix($prefixPath)
    {
        Log::debug(__CLASS__.' getPrincipalsByPrefix', func_get_args());
        $principals = [
            [
                'uri'                                   => 'principals/'.Auth::user()->email,
                '{http://sabredav.org/ns}email-address' => Auth::user()->email,
                '{DAV:}displayname'                     => Auth::user()->name,
            ],
        ];

        $prefixPath = trim($prefixPath, '/');
        if ($prefixPath) {
            $prefixPath .= '/';
        }

        $return = [];
        foreach ($principals as $principal) {
            if ($prefixPath && strpos($principal['uri'], $prefixPath) !== 0) {
                continue;
            }
            $return[] = $principal;
        }

        return $return;
    }

    /**
     * Returns a specific principal, specified by its path.
     * The returned structure should be the exact same as from
     * getPrincipalsByPrefix.
     *
     * @param string $path
     * @return array
     */
    public function getPrincipalByPath($path)
    {
        Log::debug(__CLASS__.' getPrincipalByPath', func_get_args());

        foreach ($this->getPrincipalsByPrefix('principals') as $principal) {
            if ($principal['uri'] === $path) {
                return $principal;
            }
        }
    }

    /**
     * Updates one ore more webdav properties on a principal.
     *
     * The list of mutations is stored in a Sabre\DAV\PropPatch object.
     * To do the actual updates, you must tell this object which properties
     * you're going to process with the handle() method.
     *
     * Calling the handle method is like telling the PropPatch object "I
     * promise I can handle updating this property".
     *
     * Read the PropPatch documentation for more info and examples.
     *
     * @param string $path
     * @param \Sabre\DAV\PropPatch $propPatch
     * @return void
     */
    public function updatePrincipal($path, DAV\PropPatch $propPatch)
    {
        Log::debug(__CLASS__.' updatePrincipal', func_get_args());
    }

    /**
     * This method is used to search for principals matching a set of
     * properties.
     *
     * This search is specifically used by RFC3744's principal-property-search
     * REPORT.
     *
     * The actual search should be a unicode-non-case-sensitive search. The
     * keys in searchProperties are the WebDAV property names, while the values
     * are the property values to search on.
     *
     * By default, if multiple properties are submitted to this method, the
     * various properties should be combined with 'AND'. If $test is set to
     * 'anyof', it should be combined using 'OR'.
     *
     * This method should simply return an array with full principal uri's.
     *
     * If somebody attempted to search on a property the backend does not
     * support, you should simply return 0 results.
     *
     * You can also just return 0 results if you choose to not support
     * searching at all, but keep in mind that this may stop certain features
     * from working.
     *
     * @param string $prefixPath
     * @param array $searchProperties
     * @param string $test
     * @return array
     */
    public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
    {
        Log::debug(__CLASS__.' searchPrincipals', func_get_args());
    }

    /**
     * Finds a principal by its URI.
     *
     * This method may receive any type of uri, but mailto: addresses will be
     * the most common.
     *
     * Implementation of this API is optional. It is currently used by the
     * CalDAV system to find principals based on their email addresses. If this
     * API is not implemented, some features may not work correctly.
     *
     * This method must return a relative principal path, or null, if the
     * principal was not found or you refuse to find it.
     *
     * @param string $uri
     * @param string $principalPrefix
     * @return string
     */
    public function findByUri($uri, $principalPrefix)
    {
        Log::debug(__CLASS__.' searchPrincipals', func_get_args());
    }

    /**
     * Returns the list of members for a group-principal.
     *
     * @param string $principal
     * @return array
     */
    public function getGroupMemberSet($principal)
    {
        Log::debug(__CLASS__.' getGroupMemberSet', func_get_args());

        return [
            'principals/'.Auth::user()->email,
        ];
    }

    /**
     * Returns the list of groups a principal is a member of.
     *
     * @param string $principal
     * @return array
     */
    public function getGroupMembership($principal)
    {
        Log::debug(__CLASS__.' getGroupMembership', func_get_args());

        return [
            'principals/'.Auth::user()->email,
        ];
    }

    /**
     * Updates the list of group members for a group principal.
     *
     * The principals should be passed as a list of uri's.
     *
     * @param string $principal
     * @param array $members
     * @return void
     */
    public function setGroupMemberSet($principal, array $members)
    {
        Log::debug(__CLASS__.' setGroupMemberSet', func_get_args());
    }
}
