<?php

namespace Strapieno\UserCheckIdentity\Api\V1\Listener;

use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Strapieno\ModelUtils\Entity\IdentityExistAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class UserListener
 */
class UserListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     *  {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('create.post', [$this, 'generateIdentityToken']);
    }

    /**
     * @param Event $e
     */
    public function generateIdentityToken(Event $e)
    {
        $hal = $e->getParam('entity');
        if ($hal instanceof Entity
            && ($user = $hal->entity)
            && $user instanceof IdentityExistAwareInterface
            && $user instanceof ActiveRecordInterface)
        {
            $user->generateIdentityExistToken();
            $user->save();
        }
    }
}