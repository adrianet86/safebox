<?php

namespace AdsMurai\Domain\SafeBox;

interface SafeBoxRepositoryInterface
{
    /**
     * @param string $id
     * @return SafeBox
     */
    public function byId(string $id);

    /**
     * @param string $id
     * @return SafeBox
     * @throws \Exception
     */
    public function byIdOrFail(string $id): SafeBox;

    /**
     * @param string $name
     * @return SafeBox
     */
    public function byName(string $name);

    /**
     * @param SafeBox $safeBox
     */
    public function add(SafeBox $safeBox);

    /**
     * @param SafeBox $safeBox
     */
    public function store(SafeBox $safeBox);

}