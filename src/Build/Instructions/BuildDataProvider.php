<?php

namespace Nayjest\Grids\Build\Instructions;

use Exception;
use Nayjest\Builder\Instructions\Base\Instruction;
use Nayjest\Builder\Scaffold;

/**
 * Class BuildDataProvider
 *
 * This class is a build instruction for nayjest/build package
 * that defines how to setup grids data provider
 *
 * @internal
 * @package Nayjest\Grids\Build\Instructions
 */
class BuildDataProvider extends Instruction
{
    protected $phase = self::PHASE_PRE_INST;


    /**
     * @param Scaffold $s
     * @throws Exception
     */
    public function apply(Scaffold $s)
    {
        $src = $s->getInput('src');
        $s->excludeInput('src');
        $class = null;
        $arg = null;

        if (is_object($src)) {
            if (is_a($src, '\Illuminate\Database\Eloquent\Builder')) {
                $class = '\Nayjest\Grids\EloquentDataProvider';
                $arg = $src;
            } elseif (is_a($src, '\Doctrine\DBAL\Query\QueryBuilder')) {
                $class = '\Nayjest\Grids\DbalDataProvider';
                $arg = $src;
            }

        } elseif (is_string($src)) {
            // model name
            if (
                class_exists($src, true) and
                is_subclass_of($src, '\Illuminate\Database\Eloquent\Model')
            ) {
                $class = '\Nayjest\Grids\EloquentDataProvider';
                $model = new $src;
                $arg = $model->newQuery();
            }
        }
        if ($class !== null and $arg !== null) {
            $provider = new $class($arg);
            $s->input['data_provider'] = $provider;
        } else {
            throw new Exception("Invalid Data Provider Configuration");
        }

    }
}