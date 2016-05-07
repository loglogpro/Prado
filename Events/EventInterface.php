<?php
/**
 * The start file of the framework.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Events;


interface EventInterface {

    public static function with($socketHandler);

    public function addReadEvent($callback);

    public function delete();

    public function listen();

}