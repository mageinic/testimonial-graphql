<?php
/**
 * MageINIC
 * Copyright (C) 2023. MageINIC <support@mageinic.com>
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
 * @copyright Copyright (c) 2023. MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

declare(strict_types=1);

namespace MageINIC\TestimonialGraphQl\Model\Testimonial;

use MageINIC\Testimonial\Api\Data\TestimonialInterface;
use MageINIC\Testimonial\Api\TestimonialRepositoryInterface;
use MageINIC\Testimonial\Model\Testimonial\Image;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceOutputProcessor;

/**
 * Class for Testimonial Output Processor
 */
class TestimonialOutputProcessor
{
    /**
     * @var ServiceOutputProcessor
     */
    protected ServiceOutputProcessor $serviceOutputProcessor;

    /**
     * @var Image
     */
    private Image $testimonialImage;

    /**
     * TestimonialOutputProcessor Constructor.
     *
     * @param ServiceOutputProcessor $serviceOutputProcessor
     * @param Image $testimonialImage
     */
    public function __construct(
        ServiceOutputProcessor $serviceOutputProcessor,
        Image                  $testimonialImage
    ) {
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->testimonialImage = $testimonialImage;
    }

    /**
     * Testimonial Output Processor
     *
     * @param TestimonialInterface $testimonial
     * @return array
     * @throws LocalizedException
     */
    public function execute(TestimonialInterface $testimonial): array
    {
        $data = $this->serviceOutputProcessor->process(
            $testimonial,
            TestimonialRepositoryInterface::class,
            'getById'
        );

        return array_merge($data, [
            'image_name' => $testimonial->getUploadedFile(),
            'image_url' => $this->testimonialImage->getUrl($testimonial)
        ]);
    }
}
