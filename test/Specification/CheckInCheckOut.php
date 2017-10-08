<?php

declare(strict_types=1);

namespace Specification;

use Behat\Behat\Context\Context;
use Building\Domain\Aggregate\Building;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Aggregate\AggregateType;
use Rhumsaa\Uuid\Uuid;

final class CheckInCheckOut implements Context
{
    /**
     * @var AggregateChanged[]
     */
    private $pastEvents = [];

    /**
     * @var Building|null
     */
    private $building;

    /**
     * @var AggregateChanged[]|null
     */
    private $recordedEvents;

    /**
     * @Given a building was registered
     */
    public function a_building_was_registered()
    {
        $this->pastEvents[] = NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => 'Something',
            ]
        );
    }

    /**
     * @When the user checks into the building
     */
    public function the_user_checks_into_the_building()
    {
        $this->building()->checkInUser('alice');
    }

    /**
     * @Then the user was checked into the building
     */
    public function the_user_was_checked_into_the_building()
    {
        if (! $this->popNextRecordedEvent() instanceof UserCheckedIn) {
            throw new \Exception();
        }
    }

    private function building() : Building
    {
        if (! $this->building) {
            $this->building = (new AggregateTranslator())
                ->reconstituteAggregateFromHistory(
                    AggregateType::fromAggregateRootClass(Building::class),
                    new \ArrayIterator($this->pastEvents)
                );
        }

        return $this->building;
    }

    private function popNextRecordedEvent() : AggregateChanged
    {
        if (null === $this->recordedEvents) {
            $this->recordedEvents = (new AggregateTranslator())
                ->extractPendingStreamEvents($this->building());
        }

        return \array_shift($this->recordedEvents);
    }
}
