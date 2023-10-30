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

use MageINIC\Testimonial\Api\TestimonialRepositoryInterface as TestimonialRepository;
use MageINIC\Testimonial\Model\Testimonial\Image;
use MageINIC\TestimonialGraphQl\Model\Testimonial\SearchCriteria;
use MageINIC\TestimonialGraphQl\Model\Testimonial\TestimonialOutputProcessor as OutputProcessor;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * @inheritdoc
 */
class TestimonialList implements ResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var TestimonialRepository
     */
    protected TestimonialRepository $testimonialRepository;

    /**
     * @var OutputProcessor
     */
    protected OutputProcessor $outputProcessor;

    /**
     * @var Image
     */
    protected Image $testimonialImage;

    /**
     * @var SearchCriteria
     */
    protected SearchCriteria $searchCriteria;

    /**
     * TestimonialList Constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TestimonialRepository $testimonialRepository
     * @param OutputProcessor $outputProcessor
     * @param Image $testimonialImage
     * @param SearchCriteria $searchCriteria
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TestimonialRepository $testimonialRepository,
        OutputProcessor       $outputProcessor,
        Image                 $testimonialImage,
        SearchCriteria        $searchCriteria
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->testimonialRepository = $testimonialRepository;
        $this->outputProcessor = $outputProcessor;
        $this->testimonialImage = $testimonialImage;
        $this->searchCriteria = $searchCriteria;
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
        $store = $context->getExtensionAttributes()->getStore();
        $args = $this->searchCriteria->buildCriteria($args, $store);

        $searchCriteria = $this->searchCriteriaBuilder->build('testimonial', $args);

        $pageSize = $args['pageSize'];
        $currentPage = $args['currentPage'];
        $searchCriteria->setCurrentPage($currentPage);
        $searchCriteria->setPageSize($pageSize);

        $collection = $this->testimonialRepository->getList($searchCriteria);
        $count = $collection->getTotalCount();

        if ($count) {
            $testimonialCollection = array_map([$this->outputProcessor, 'execute'], $collection->getItems());

            return [
                'total_count' => $count,
                'total_pages' => ceil($count / $pageSize),
                'testimonialList' => $testimonialCollection
            ];

        } else {
            throw new GraphQlInputException(__('Testimonial does not exist.'));
        }
    }
}
