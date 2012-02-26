<?php
namespace helloworld\Module;

use Ray\Di\InjectorInterface as Di,
    Ray\Di\ProviderInterface as Provide;

use BEAR\Resource\Adapter\App,
    BEAR\Resource\Adapter\Page,
    BEAR\Resource\SchemeCollection,
    BEAR\Framework\AbstractAppContext as AppContext;

/**
 * Application resource module
 *
 * @package    helloworld
 * @subpackage Module
 */
class SchemeCollectionProvider implements Provide
{
    /**
     * Constructor
     *
     * @param Inject     $injector
     * @param AppContext $app
     *
     * @Inject
     */
    public function __construct(Di $injector, AppContext $app)
    {
        $this->injector = $injector;
        $this->namespace = $app->name;
    }

    /**
     * Return resource adapter set.
     *
     * @return array
     */
    public function get()
    {
        $schemeCollection = new SchemeCollection;
        $schemeCollection->scheme('page')->host('self')->toAdapter(new App($this->injector, $this->namespace, 'Resource\Page'));
        return $schemeCollection;
    }
}