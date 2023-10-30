<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_TestimonialGraphQl
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\TestimonialGraphQl\Model\Resolver;

use Exception;
use MageINIC\Testimonial\Api\TestimonialRepositoryInterface as TestimonialRepository;
use MageINIC\TestimonialGraphQl\Model\Testimonial\ExtractTestimonialData as ExtractData;
use MageINIC\TestimonialGraphQl\Model\Testimonial\SaveTestimonial;
use MageINIC\TestimonialGraphQl\Model\Testimonial\TestimonialOutputProcessor as OutputProcessor;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * @inheritdoc
 */
class UpdateTestimonial implements ResolverInterface
{
    /**
     * @var TestimonialRepository
     */
    protected TestimonialRepository $testimonialRepository;

    /**
     * @var ExtractData
     */
    protected ExtractData $extractData;

    /**
     * @var SaveTestimonial
     */
    protected SaveTestimonial $saveTestimonial;

    /**
     * @var OutputProcessor
     */
    protected OutputProcessor $outputProcessor;

    /**
     * UpdateTestimonial Constructor.
     *
     * @param TestimonialRepository $testimonialRepository
     * @param ExtractData $extractData
     * @param SaveTestimonial $saveTestimonial
     * @param OutputProcessor $outputProcessor
     */
    public function __construct(
        TestimonialRepository $testimonialRepository,
        ExtractData           $extractData,
        SaveTestimonial       $saveTestimonial,
        OutputProcessor       $outputProcessor,
    ) {
        $this->testimonialRepository = $testimonialRepository;
        $this->extractData = $extractData;
        $this->saveTestimonial = $saveTestimonial;
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field       $field,
        $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        if (!isset($args)) {
            throw new GraphQlInputException(__('Please fill all filed'));
        }

        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('Please provide an ID for identification.'));
        }

        try {
            $model = $this->testimonialRepository->getById($args['id']);
        } catch (Exception $e) {
            throw new GraphQlInputException(
                __('Testimonial with ID "%1" does not exist.', $args['id']),
                $e
            );
        }

        $data = $this->extractData->execute(
            $model,
            $args['input'],
            $context->getExtensionAttributes()->getStore()
        );
        $model = $this->saveTestimonial->execute($data);

        return $this->outputProcessor->execute($model);
    }
}
