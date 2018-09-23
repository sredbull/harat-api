<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Provider\ExpressionLanguage;

use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ExpressionLanguageProvider.
 */
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{

    /**
     * The includes.
     *
     * @var array $includes
     */
    private $includes;

    /**
     * ExpressionLanguageProvider constructor.
     *
     * @param array $includes The includes.
     */
    public function __construct(array $includes)
    {
        $this->includes = $includes;
    }

    /**
     * Get the expression language functions
     *
     * @return array|ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'isIncluded',
                static function (): void {},
                function ($arguments, $include) {
                    if ($this->getIncludes() === null) {
                        return false;
                    }

                    return \in_array($include, $this->getIncludes(), true);
                }),
        ];
    }

    /**
     * Get the ExpressionEvaluator
     *
     * @return ExpressionEvaluator
     */
    public function getExpressionEvaluator(): ExpressionEvaluator
    {
        $language = new ExpressionLanguage();
        $language->registerProvider(new self($this->getIncludes()));

        return new ExpressionEvaluator($language);
    }

    /**
     * Get the includes.
     *
     * @return array
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

}