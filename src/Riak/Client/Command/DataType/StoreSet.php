<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\StoreSetBuilder;
use Riak\Client\Core\Operation\DataType\StoreSetOperation;

/**
 * Command used to update or create a set datatype in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSet extends StoreDataType
{
    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location, array $options = [])
    {
        parent::__construct($location, new SetUpdate(), $options);
    }

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function add($value)
    {
        $this->update->add($value);

        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function remove($value)
    {
        $this->update->remove($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $op        = $this->update->getOp();
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreSetOperation($converter, $this->location, $op, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreSetBuilder($location, $options);
    }
}