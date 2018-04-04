<?php

namespace SafeBox\Infrastructure\Repository\SafeBox;

use SafeBox\Domain\SafeBox\SafeBox;
use SafeBox\Domain\SafeBox\SafeBoxNotExistsException;
use SafeBox\Domain\SafeBox\SafeBoxRepositoryInterface;

class MemorySafeBoxRepository implements SafeBoxRepositoryInterface
{
    private $safeBoxes = [];

    /**
     * @param SafeBox $safeBox
     */
    public function add(SafeBox $safeBox)
    {
        $this->safeBoxes[$safeBox->id()] = $safeBox;
    }

    /**
     * @param string $id
     * @return SafeBox
     */
    public function byId(string $id)
    {
        if (isset($this->safeBoxes[$id])) {
            return $this->safeBoxes[$id];
        }

        return null;
    }

    /**
     * @param string $id
     * @return SafeBox
     * @throws SafeBoxNotExistsException
     */
    public function byIdOrFail(string $id): SafeBox
    {
        $safeBox = $this->byId($id);
        if ($safeBox === null) {
            throw new SafeBoxNotExistsException('SafeBox not found');
        }

        return $safeBox;
    }

    /**
     * @param string $name
     * @return SafeBox
     */
    public function byName(string $name)
    {
        foreach ($this->safeBoxes as $safeBox) {
            if ($safeBox->name() === $name) {
                return $safeBox;
            }
        }

        return null;
    }

    /**
     * @param SafeBox $safeBox
     * @throws SafeBoxNotExistsException
     */
    public function store(SafeBox $safeBox)
    {
        $this->byIdOrFail($safeBox->id());
        $this->safeBoxes[$safeBox->id()] = $safeBox;
    }
}