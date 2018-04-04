<?php

namespace SafeBox\Infrastructure\Repository\SafeBox;


use SafeBox\Domain\SafeBox\SafeBox;
use SafeBox\Domain\SafeBox\SafeBoxNotExistsException;
use SafeBox\Domain\SafeBox\SafeBoxRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SqliteSafeBoxRepository extends Model implements SafeBoxRepositoryInterface
{
    protected $table = 'safeboxes';
    protected $connection = 'sqlite';
    protected $guarded = [];
    public $timestamps = false;

    function byId(string $id): SafeBox
    {
        $model = self::find($id);

        return unserialize($model->serialized_model);
    }

    /**
     * @param SafeBox $safeBox
     */
    function add(SafeBox $safeBox)
    {
        self::create([
            'id' => $safeBox->id(),
            'name' => $safeBox->name(),
            'serialized_object' => serialize($safeBox)
        ]);
    }

    /**
     * @param string $id
     * @return SafeBox
     * @throws \Exception
     */
    public function byIdOrFail(string $id): SafeBox
    {
        $model = self::findOrFail($id);

        return unserialize($model->serialized_object);
    }

    public function findOrFail($id)
    {
        try {
            return parent::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new SafeBoxNotExistsException('SafeBox not found');
        }
    }

    /**
     * @param string $name
     * @return SafeBox
     */
    public function byName(string $name)
    {
        return self::where('name', $name)->get()->all();
    }

    /**
     * @param SafeBox $safeBox
     * @throws \Exception
     */
    public function store(SafeBox $safeBox)
    {
        $model = self::findOrFail($safeBox->id());
        $model->update([
            'name' => $safeBox->name(),
            'serialized_object' => serialize($safeBox)
        ]);
    }
}