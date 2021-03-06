<?php
namespace sandbox\Resource\Page\First;

use BEAR\Framework\Resource\AbstractPage as Page;
use BEAR\Framework\Inject\ResourceInject;
use BEAR\Resource\AbstractObject;

/**
 * Greeting page
 */
class Greeting extends Page
{
    use ResourceInject;
    
    public $body = [
        'greeting' => 'Hello.'
    ];
    
    /**
     * Get
     *
     * @param  string $name
     *
     */
    public function onGet($name = 'anonymous')
    {
        $this['greeting'] = $this
        ->resource
        ->get
        ->uri('app://self/first/greeting')
        ->withQuery(['name' => $name])
        ->request();
        echo $this['greeting']->toUri();
        return $this;
    }    
}
