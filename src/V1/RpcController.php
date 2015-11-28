<?php

namespace Strapieno\UserCheckIdentity\Api\V1;

use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Strapieno\Auth\Model\OAuth2\AdapterInterface;
use Strapieno\ModelUtils\Entity\PasswordAwareInterface;
use Strapieno\ModelUtils\Entity\RercoverPasswordAwareInterface;
use Strapieno\User\Model\Criteria\Mongo\UserMongoCollectionCriteria;
use Strapieno\User\Model\Entity\State\UserStateAwareInterface;
use Strapieno\User\Model\Entity\UserInterface;
use Strapieno\User\Model\UserModelInterface;
use Strapieno\User\Model\UserModelService;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Http\Response;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\View\ApiProblemModel;
use ZF\Rpc\RpcController as ApigilityRpcController;

/**
 * Class RpcController
 */
class RpcController extends ApigilityRpcController
{
    /**
     * @param MvcEvent $e
     */
    public function validateIdentity(MvcEvent $e)
    {
        $inputFilter = $e->getParam('ZF\ContentValidation\InputFilter');
        if (!$inputFilter instanceof InputFilter) {
            return new ApiProblemModel(new ApiProblem(500, 'Missing InputFilter; cannot validate request'));
        }

        $data = $inputFilter->getValues();

        $criteria = (new UserMongoCollectionCriteria())->setIdentityExistToken($data['token']);
        /** @var $userService  UserModelInterface */
        $userService = $this->model()->get(UserModelService::class);
        $result = $userService->find($criteria);

        if ($result->count() == 1) {
            $user = $result->current();
            if ($user instanceof UserStateAwareInterface && $user instanceof ActiveRecordInterface) {
                $user->validated();
                $user->save();

                if ($this->getResponse() instanceof Response) {
                    $this->getResponse()->setStatusCode(204);
                }
                return new JsonModel();
            }
            return new ApiProblemModel(new ApiProblem(500, 'Invalid object register in user service'));
        }
        return new ApiProblemModel(new ApiProblem(404, 'Token not found'));
    }
}