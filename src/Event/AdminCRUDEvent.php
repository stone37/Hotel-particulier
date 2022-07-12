<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class AdminCRUDEvent extends Event
{
    public const PRE_CREATE  = 'app.entity.pre_create';

    public const POST_CREATE = 'app.entity.post_create';

    public const PRE_EDIT    = 'app.entity.pre_edit';

    public const POST_EDIT   = 'app.entity.post_edit';

    public const PRE_DELETE  = 'app.entity.pre_delete';

    public const POST_DELETE = 'app.entity.post_delete';

    public const SHOW        = 'app.entity.show';

    private $entity;

    private Response $response;

    public function  __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}


