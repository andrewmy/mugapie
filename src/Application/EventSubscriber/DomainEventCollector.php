<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber;

use App\Domain\Model\Common\Interfaces\Event;
use App\Domain\Model\Common\Interfaces\RecordsEvents;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function class_implements;
use function in_array;

final class DomainEventCollector implements EventSubscriber
{
    private EventDispatcherInterface $dispatcher;

    /** @var Collection<int, RecordsEvents> */
    private Collection $entities;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->entities = new ArrayCollection();

        $this->dispatcher = $dispatcher;
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents() : array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $event) : void
    {
        $this->collect($event);
    }

    public function preUpdate(LifecycleEventArgs $event) : void
    {
        $this->collect($event);
    }

    public function preRemove(LifecycleEventArgs $event) : void
    {
        $this->collect($event);
    }

    private function collect(LifecycleEventArgs $event) : void
    {
        $entity = $event->getObject();

        if (! ($entity instanceof RecordsEvents)) {
            return;
        }

        $this->entities->add($entity);
    }

    public function preFlush(PreFlushEventArgs $args) : void
    {
        $unitOfWork = $args->getEntityManager()->getUnitOfWork();
        foreach ($unitOfWork->getIdentityMap() as $class => $entities) {
            if (! in_array(RecordsEvents::class, class_implements($class), true)) {
                continue;
            }

            foreach ($entities as $entity) {
                $this->entities->add($entity);
            }
        }
    }

    public function postFlush() : void
    {
        /** @var Collection<int, Event> $events */
        $events = new ArrayCollection();

        foreach ($this->entities as $entity) {
            foreach ($entity->popEvents() as $event) {
                $events->add($event);
            }
        }

        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
