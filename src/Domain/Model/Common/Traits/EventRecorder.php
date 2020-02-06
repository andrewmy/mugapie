<?php

declare(strict_types=1);

namespace App\Domain\Model\Common\Traits;

use App\Domain\Model\Common\Interfaces\Event;

trait EventRecorder
{
    /** @var Event[] */
    private array $events = [];

    /**
     * @return Event[]
     */
    public function popEvents() : array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

    private function recordThat(Event $event) : void
    {
        $this->events[] = $event;
    }
}
