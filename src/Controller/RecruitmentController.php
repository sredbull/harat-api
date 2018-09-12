<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controller;

use App\Entity\RecruitmentEntity;
use App\Entity\UserEntity;
use App\Exception\RecruitmentNotFoundException;
use App\Exception\UserNotFoundException;
use App\Exception\ValidationException;
use App\ParamConverter\Recruitment\PostRecruitmentParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * The RecruitmentController Class.
 */
class RecruitmentController extends BaseController
{

    /**
     * Get all recruitments.
     *
     * @Rest\Get("recruitment")
     *
     * @return View
     */
    public function getRecruitments(): View
    {
        $recruitments = $this->recruitmentRepository->findAll();

        return $this->view(
            $recruitments,
            Response::HTTP_OK
        );
    }

    /**
     * Get a recruitment.
     *
     * @param RecruitmentEntity $recruitment The recruitment to get.
     *
     * @Rest\Get("recruitment/{id}")
     *
     * @return View
     *
     * @throws RecruitmentNotFoundException When the recruitment could not be found.
     */
    public function getRecruitment(?RecruitmentEntity $recruitment): View
    {
        if ($recruitment === null) {
            throw new RecruitmentNotFoundException();
        }

        return $this->view(
            $recruitment,
            Response::HTTP_OK
        );
    }

    /**
     * Post a recruitment.
     *
     * @param UserEntity                       $user             The user to recruitment belongs to.
     * @param PostRecruitmentParamConverter    $params           The validated recruitment fields.
     * @param ConstraintViolationListInterface $validationErrors The validation validation errors.
     *
     * @Rest\Post("recruitment/{id}")
     *
     * @ParamConverter("params", converter="fos_rest.request_body")
     *
     * @return JsonResponse
     *
     * @throws UserNotFoundException Thrown when the user could not be found.
     * @throws ValidationException   Thrown when the registration validation fails.
     */
    public function postRecruitment(?UserEntity $user, PostRecruitmentParamConverter $params, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($user === null) {
            throw new UserNotFoundException();
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        if (count($user->getRecruitments()->toArray())) {
            foreach($user->getRecruitments() as $recruitment) {
                $this->getRepository()->remove($recruitment);
            }
        }

        $recruitment = new RecruitmentEntity();
        $recruitment->setUser($user);
        $recruitment->setForm($params->getForm());

        $this->getRepository()->save($recruitment);

        return $this->getView(
            [
                'code' => Response::HTTP_CREATED,
                'status' => 'ok',
                'message' => 'Recruitment posted',
            ],
            Response::HTTP_CREATED
        );
    }

}
