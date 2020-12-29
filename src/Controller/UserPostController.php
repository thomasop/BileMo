<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserPostController extends AbstractFOSRestController
{
    /**
     * function create user
     *
     * @Post(
     *     path = "/BileMo/user",
     *     name="app_user_post"
     * )
     * @View(
     *     serializerGroups={"user_read"},
     *     StatusCode=201
     * )
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param SerializerInterface $serialize
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return $user
     */
    public function create(User $user, SerializerInterface $serialize, EntityManagerInterface $emi, ValidatorInterface $validator)
    {
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $data = $serialize->serialize($errors, 'json');
            return new JsonResponse($data, 400, [], true);
        }
        $user->setCustomer($this->getUser());
        $emi->persist($user);
        $emi->flush();

        return $this->view(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_user_detail', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        );
    }
}
