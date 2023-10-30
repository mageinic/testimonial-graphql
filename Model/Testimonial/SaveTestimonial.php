<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageINIC\TestimonialGraphQl\Model\Testimonial;

use MageINIC\Testimonial\Api\Data\TestimonialInterface;
use MageINIC\Testimonial\Api\TestimonialRepositoryInterface as TestimonialRepository;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Class for Save testimonial
 */
class SaveTestimonial
{
    /**
     * @var TestimonialRepository
     */
    private TestimonialRepository $testimonialRepository;

    /**
     * SaveTestimonial Constructor.
     *
     * @param TestimonialRepository $testimonialRepository
     */
    public function __construct(
        TestimonialRepository $testimonialRepository
    ) {
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * Save Testimonial.
     *
     * @param TestimonialInterface $testimonial
     * @return TestimonialInterface
     * @throws GraphQlInputException|GraphQlNoSuchEntityException
     */
    public function execute(TestimonialInterface $testimonial): TestimonialInterface
    {
        try {
            $data = $this->testimonialRepository->save($testimonial);
        } catch (CouldNotSaveException $e) {
            throw new GraphQlNoSuchEntityException(__('Error while saving the Testimonial.'), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return $data;
    }
}
