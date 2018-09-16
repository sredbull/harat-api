<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Features\User;

use App\Features\BaseFeature;
use App\Jobs\User\GetAllUsersJob;
use App\Jobs\View\JsonResponseJob;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUsersFeature.
 */
class GetUsersFeature extends BaseFeature
{

    /**
     * Handle the feature.
     *
     * @return mixed
     */
    public function handle (): JsonResponse
    {
        $users = $this->run(new GetAllUsersJob());

        return $this->run(new JsonResponseJob($users, Response::HTTP_OK));
    }

}
