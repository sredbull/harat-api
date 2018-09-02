<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ParamConverter\Recruitment;

use JMS\Serializer\Annotation as JMSSerializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostRecruitmentParamConverter.
 */
class PostRecruitmentParamConverter
{

    /**
     * The form.
     *
     * @var array $form
     *
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     *
     * @JMSSerializer\Type("array")
     */
    private $form;

    /**
     * Get the form.
     *
     * @return array
     */
    public function getForm(): array
    {
        return $this->form;
    }

}
