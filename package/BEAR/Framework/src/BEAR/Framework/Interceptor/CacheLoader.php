<?php
/**
 * BEAR.Framework
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Framework\Interceptor;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Guzzle\Common\Cache\CacheAdapterInterface;
use Ray\Di\Di\Inject;
/**
 * Cache interceptor
 *
 * @package    BEAR.Framework
 * @subpackage Intercetor
 */
class CacheLoader implements CacheInterface, MethodInterceptor
{
    /**
     * Cache header key
     *
     * @var string
     */
    const HEADER_CACHE = 'x-cache';

    /**
     * Host
     *
     * @var string
     */
    private $host;

    /**
     * Life time
     *
     * @var int
     */
    private $lifeTime;

    /**
     * Constructor
     *
     * @param Cache $cache
     */
    public function __construct(CacheAdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * (non-PHPdoc)
     * @see Ray\Aop.MethodInterceptor::invoke()
     */
    public function invoke(MethodInvocation $invocation)
    {
        $class = get_class($invocation->getThis());
        $args = $invocation->getArguments();
        $method = $invocation->getMethod();
        $id = $this->getId($class, $args);
        $pager = (isset($_GET['_start'])) ? $_GET['_start'] : '';
        $saved = $this->cache->fetch($id);
        $pager = (! $pager && isset($saved['pager']) ) ? 1 : $pager;
        if ($pager) {
            $pagered = (isset($saved['pager'][$pager])) ? $saved['pager'][$pager] : false;
        } else {
            $pagered = $saved;
        }
        if ($pagered) {
            $resource = $invocation->getThis();
            list($resource->code, $resource->headers, $resource->body) = $pagered;
            $cache = json_decode($resource->headers[self::HEADER_CACHE], true);
            $resource->headers[self::HEADER_CACHE] = json_encode([
                'mode' => 'R',
                'date' => $cache['date'],
                'life' => $cache['life']
            ]);

            return $resource;
        }
        $result = $invocation->proceed();
        $resource = $invocation->getThis();
        $time = $invocation->getAnnotation()->time;
        $resource->headers[self::HEADER_CACHE] = json_encode([
            'mode' => 'W',
            'date' => date('r'),
            'life' => $time
        ]);
        $data = [$resource->code, $resource->headers, $resource->body];
        if ($pager) {
            $saved['pager'][$pager] = $data;
            $data = $saved;
        }
        $this->cache->save($id, $data, $time);

        return $resource;
    }

    /**
     * Return cache id
     *
     * @param string $class
     * @param array  $args
     *
     * @return string
     */
    protected function getId($class, array $args)
    {
        return $class . crc32(serialize($args));
    }

    /**
     * (non-PHPdoc)
     * @see BEAR\Framework\Interceptor\CacheInterface::delete()
     */
    public function delete($class, array $args)
    {
        $id = $this->getId($class, $args);
        $this->cache->delete($id);
    }

    /**
     * (non-PHPdoc)
     * @see BEAR\Framework\Interceptor\CacheInterface::save()
     */
    public function save($class, $args, $data)
    {
        $id = $this->getId($class, $args);
        $this->cache->save($id, serialize($data), 1);
    }
}
