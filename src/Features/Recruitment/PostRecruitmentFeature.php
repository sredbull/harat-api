<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Features\Recruitment;

use App\Entity\UserEntity;
use App\Exception\UserNotFoundException;
use App\Features\BaseFeature;
use App\Jobs\Recruitment\DeleteRecruitmentsFromUserJob;
use App\Jobs\Recruitment\NewRecruitmentForUserJob;
use App\Jobs\View\JsonResponseJob;
use App\ParamConverter\Recruitment\PostRecruitmentRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PostRecruitmentFeature.
 */
class PostRecruitmentFeature extends BaseFeature
{

    /**
     * The user.
     *
     * @var UserEntity|null
     */
    private $user;

    /**
     * The request.
     *
     * @var PostRecruitmentRequest
     */
    private $request;

    /**
     * PostRecruitmentFeature constructor.
     *
     * @param UserEntity|null        $user    The user to post the recruitment to.
     * @param PostRecruitmentRequest $request The validated recruitment request.
     */
    public function __construct(?UserEntity $user, PostRecruitmentRequest $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * Handle the feature.
     *
     * @return mixed
     *
     * @throws UserNotFoundException When the user could not be found.
     */
    public function handle (): JsonResponse
    {
        if ($this->user === null) {
            throw new UserNotFoundException();
        }

        $this->run(new DeleteRecruitmentsFromUserJob($this->user));
        $this->run(new NewRecruitmentForUserJob($this->user, $this->request->getForm()));

        $response = [
            'code' => Response::HTTP_CREATED,
            'status' => 'ok',
            'message' => 'Recruitment posted',
        ];

        return $this->run(new JsonResponseJob($response, Response::HTTP_CREATED));
    }

}
