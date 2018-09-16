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

use App\Interfaces\FeatureInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserController.
 */
class BaseController extends Controller
{

    /**
     * Serve the feature.
     *
     * @param FeatureInterface $feature The feature to serve.
     *
     * @return mixed
     */
    public function serve(FeatureInterface $feature)
    {
        return $feature($this->container);
    }

}
