<?php

namespace Riak\Client\Core\Transport\Proto\Kv;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Kv\PutRequest;
use Riak\Client\Core\Message\Kv\PutResponse;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbPutReq;
use Riak\Client\ProtoBuf\RpbContent;
use Riak\Client\ProtoBuf\RpbPair;

/**
 * rpb put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoPut extends BaseProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Kv\PutRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbPutReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new RpbPutReq();

        $rpbPutReq->setVclock($request->vClock);
        $rpbPutReq->setBucket($request->bucket);
        $rpbPutReq->setType($request->type);
        $rpbPutReq->setKey($request->key);

        if ($request->w !== null) {
            $rpbPutReq->setW($request->w);
        }

        if ($request->dw !== null) {
            $rpbPutReq->setDw($request->dw);
        }

        if ($request->pw !== null) {
            $rpbPutReq->setPw($request->pw);
        }

        if ($request->returnBody !== null) {
            $rpbPutReq->setReturnBody($request->returnBody);
        }

        if ($request->vClock !== null) {
            $rpbPutReq->setVclock($request->vClock);
        }

        if ( ! $request->content) {
            return $rpbPutReq;
        }

        $rpbContent = new RpbContent();

        $rpbContent->setVtag($request->content->vtag);
        $rpbContent->setValue($request->content->value);
        $rpbContent->setContentType($request->content->contentType);

        foreach ($request->content->indexes as $name => $values) {
            foreach ($values as $v) {
                $value = new RpbPair();

                $value->setKey($name);
                $value->setValue($v);

                $rpbContent->addIndexes($value);
            }
        }

        foreach ($request->content->metas as $name => $meta) {
            $value = new RpbPair();

            $value->setKey($name);
            $value->setValue($meta);

            $rpbContent->addUsermeta($value);
        }

        $rpbPutReq->setContent($rpbContent);

        return $rpbPutReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\PutResponse
     */
    public function send(Request $request)
    {
        $response   = new PutResponse();
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::PUT_REQ, RiakMessageCodes::PUT_RESP);

        if ( ! $rpbGetResp instanceof RpbGetResp) {
            return $response;
        }

        if ( ! $rpbGetResp->hasContent()) {
            return $response;
        }

        $response->vClock      = $rpbGetResp->getVclock()->get();
        $response->contentList = $this->createContentList($rpbGetResp->getContentList());

        return $response;
    }
}