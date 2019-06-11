<?php


namespace App\Traits;
use DateTime;


/**
 * Trait SoftDeletable
 *
 * Provides soft delete functionality for our entities.
 *
 * @package App\Traits
 * @author @dev1 -> Ore Richard
 */
trait SoftDeletable
{
    /**
     * An entity with this status is active
     * @var
     */
    public static $activeStatus = 1;

    /**
     * An entity with this status is inactive
     * @var
     */
    public static $inactiveStatus = 0;

    /**
     * An entity with this status is deleted
     * @var
     */
    public static $deletedStatus = -1;

    /**
     * Sends this entity to the recycle bin.
     */
    public function trash(): self
    {
        $this->setStatus(self::$deletedStatus);
        $this->setDeletedAt(new DateTime());

        return $this;
    }

    /**
     * Restores this entity from the recycle bin.
     */
    public function restore(): self
    {
        if ($this->isDeleted()) {
            $this->setStatus(self::$activeStatus);
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * Suspends this entity (makes it inactive).
     */
    public function suspend(): self
    {
        if (!$this->isDeleted()) {
            $this->setStatus(self::$inactiveStatus);
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * Restores this entity to its active state if previously suspended.
     */
    public function unsuspend(): self
    {
        if ($this->isSuspended()) {
            $this->setStatus(self::$activeStatus);
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }


    public function isActive(): bool
    {
        return $this->getStatus() == self::$activeStatus;
    }

    public function isSuspended(): bool
    {
        return $this->getStatus() == self::$inactiveStatus;
    }

    public function isDeleted(): bool
    {
        return $this->getStatus() == self::$deletedStatus;
    }
}

