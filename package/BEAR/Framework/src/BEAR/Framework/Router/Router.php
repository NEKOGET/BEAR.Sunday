<?php
/**
 *  BEAR.Framework
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Framework\Router;

use Aura\Router\Map;

/**
 * Standard Router
 *
 * @package BEAR.Framework
 * @author  Akihito Koriyama <akihito.koriyama@gmail.com>
 */
final class Router
{
    /**
     * $GLOBALS
     *
     * @var array
     */
    private $globals;

    /**
     * map
     *
     * @var Aura\Router\Map;
     */
    private $map;

    const METHOD_OVERRIDE = 'X-HTTP-Method-Override';
    const METHOD_OVERRIDE_GET = '_method';

    /**
     * Constructor
     *
     * @param Map $map
     */
    public function __construct(Map $map = null)
    {
        $this->map = $map;
    }

    public function match($globals)
    {
        $this->globals = $globals;
        $uri = $globals['_SERVER']['REQUEST_URI'];
        $query = $globals['_GET'];
        $route = $this->map ? $this->map->match(parse_url($uri, PHP_URL_PATH), $globals['_SERVER']) : false;
        if ($route === false) {
            list($method, $query) = $this->getMethodQuery($globals);
            $pageUri = $this->getPageKey();
        } else {
            $method = $route->values['action'];
            $pageUri = $route->values['page'];
            $query = [];
            foreach ($route->params as $key => $params) {
                $query[$key] = $route->values[$key];
            }
        }
        unset($query[self::METHOD_OVERRIDE]);

        return [$method, $pageUri, $query];
    }

    /**
     * Return request method
     *
     * @return string
     */
    public function getMethodQuery($globals)
    {
        if ($globals['_SERVER']['REQUEST_METHOD'] === 'GET' && isset($globals['_GET'][self::METHOD_OVERRIDE_GET])) {
            $method = $globals['_GET'][self::METHOD_OVERRIDE_GET];
            $query = $globals['_GET'];
            goto complete;
        }
        if ($globals['_SERVER']['REQUEST_METHOD'] === 'POST' && isset($globals['_POST'][self::METHOD_OVERRIDE])) {
            $method = $globals['_POST'][self::METHOD_OVERRIDE];
            $query = $globals['_POST'];
            goto complete;
        }
        $method = $globals['_SERVER']['REQUEST_METHOD'] ;
        $query = $globals['_GET'];
        complete:
        $method = strtolower($method);

        return [$method, $query];
    }

    /**
     * Return page key
     *
     * @return array                     [$method, $pagekey]
     * @throws \InvalidArgumentException
     */
    private function getPageKey()
    {
        if (!isset($this->globals['_SERVER']['REQUEST_URI'])) {
            return '404';
        }
        $pageKey = substr($this->globals['_SERVER']['REQUEST_URI'], 1);

        return $pageKey;
    }
}
