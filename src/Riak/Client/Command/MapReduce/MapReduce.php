<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\RiakCommand;
use Riak\Client\Core\Query\Func\RiakFunction;
use Riak\Client\Command\MapReduce\Specification;
use Riak\Client\Command\MapReduce\Phase\MapPhase;
use Riak\Client\Command\MapReduce\Phase\LinkPhase;
use Riak\Client\Command\MapReduce\Phase\ReducePhase;

/**
 * Base abstract class for all MapReduce commands.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class MapReduce implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\Func\RiakFunction
     */
    protected $specification;

    /**
     * @param \Riak\Client\Command\MapReduce\Specification $specification
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * Set the operations timeout
     *
     * @param integer $timeout
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withTimeout($timeout)
    {
        $this->specification->setTimeout($timeout);

        return $this;
    }

    /**
     * Add a Map Phase
     *
     * @param \Riak\Client\Core\Query\Func\RiakFunction $function
     * @param mixed                                     $argument
     * @param boolean                                   $keepResult
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withMapPhase(RiakFunction $function, $argument = null, $keepResult = false)
    {
        $this->specification->addPhase(new MapPhase($function, $argument, $keepResult));

        return $this;
    }

    /**
     * Add a Reduce Phase
     *
     * @param \Riak\Client\Core\Query\Func\RiakFunction $function
     * @param mixed                                     $argument
     * @param boolean                                   $keepResult
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withReducePhase(RiakFunction $function, $argument = null, $keepResult = false)
    {
        $this->specification->addPhase(new ReducePhase($function, $argument, $keepResult));

        return $this;
    }

    /**
     * @param string  $bucket
     * @param string  $tag
     * @param boolean $keepResult
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withLinkPhase($bucket, $tag, $keepResult = false)
    {
        $this->specification->addPhase(new LinkPhase($bucket, $tag, $keepResult));

        return $this;
    }
}
