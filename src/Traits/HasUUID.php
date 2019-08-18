<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUUID
{
    /**
     * Hook into the boot method on the model and register any event listeners.
     *
     * @return void
     */
    public static function bootHasUUID()
    {
        static::creating(function ($model) {
            $uuidFieldName = $model->getUUIDFieldName();
            if (empty($model->$uuidFieldName)) {
                $model->$uuidFieldName = static::generateUUID();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getUUIDFieldName();
    }

    /**
     * Get the name of the uuid field.
     *
     * @return string
     */
    public function getUUIDFieldName()
    {
        if (!empty($this->uuidFieldName)) {
            return $this->uuidFieldName;
        }
        return 'id';
    }

    /**
     * Generate the actual uuid.
     *
     * @return string
     */
    public static function generateUUID()
    {
        return (string) Str::uuid();
    }
}
