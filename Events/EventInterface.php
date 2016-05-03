<?php
/**
 * The start file of the framework.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Events;


interface EventInterface {
    const EVENT_READ = 1;
    const EVENT_WRITE = 2;

    public static function with($socketHandler);

    public function add($eventFlag, $callback);

    public function delete();

    public function listen();
}