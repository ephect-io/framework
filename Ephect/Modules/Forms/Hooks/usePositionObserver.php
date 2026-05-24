<?php

namespace Ephect\Modules\Forms\Hooks;

function usePositionObserver(array $data, string $observe, callable $callback): array
{
    $resources = [];
    $oldObserve = '';
    $nextResource = null;

    foreach($data as $i => $item) {
        $resource = $nextResource ?? $callback($item);
        $resource->first = false;
        $resource->last = false;

        if(!property_exists($resource, $observe)) {
            throw new \Error("The observed property [$observe] is not defined.");
        }

        if($resource->$observe != $oldObserve) {
            $resource->first = true;
            $oldObserve = $resource->$observe;
        } else {
            $resource->first = false;
        }

        if($i < count($data) - 1) {
            $nextResource = $callback($data[$i + 1]);
            if($nextResource->$observe != $resource->$observe) {
                $resource->last = true;
            }
        } else {
            $nextResource = null;
            $resource->last = true;
        }

        $resources[] = $resource;
    }

    return $resources;
}
