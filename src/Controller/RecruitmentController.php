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

use App\Entity\UserEntity;
use App\Features\Recruitment\PostRecruitmentFeature;
use App\ParamConverter\Recruitment\PostRecruitmentRequest;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The RecruitmentController Class.
 */
class RecruitmentController extends BaseController
{

    /**
     * Post a recruitment.
     *
     * @param UserEntity|null        $user    The user the recruitment belongs to.
     * @param PostRecruitmentRequest $request The validated recruitment request.
     *
     * @Route("/recruitment/{id}", methods={"POST"})
     *
     * @return mixed
     */
    public function postRecruitment(?UserEntity $user, PostRecruitmentRequest $request)
    {
        return $this->serve(new PostRecruitmentFeature($user, $request));
    }

}
