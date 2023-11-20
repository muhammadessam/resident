<?php

namespace App\Traits;
trait InteractsWithSoftCascadedRelation
{
    protected static function bootInteractsWithSoftCascadedRelation(): void
    {
        static::deleting(function ($resource) {
            foreach (static::$relations_to_cascade as $relation) {
                foreach ($resource->{$relation}()->get() as $item) {
                    $item->delete();
                }
            }
        });

        static::restoring(function ($resource) {
            foreach (static::$relations_to_cascade as $relation) {
                foreach ($resource->{$relation}()->withTrashed()->get() as $item) {
                    $item->withTrashed()->restore();
                }
            }
        });
    }
}
