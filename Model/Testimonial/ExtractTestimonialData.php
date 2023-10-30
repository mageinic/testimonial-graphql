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
use MageINIC\Testimonial\Model\Testimonial\DataProcessor;
use MageINIC\Testimonial\Ui\Component\Listing\Column\Options\Status;
use MageINIC\TestimonialGraphQl\Model\Resolver\DataProvider\UploadFile;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Validator\EmailAddress;
use Magento\Framework\Validator\Url;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class for Extract Testimonial data
 */
class ExtractTestimonialData
{
    /**
     * @var UploadFile
     */
    protected UploadFile $uploadFile;

    /**
     * @var File
     */
    protected File $fileDriver;

    /**
     * @var EmailAddress
     */
    protected EmailAddress $emailValidation;

    /**
     * @var Url
     */
    protected Url $urlValidation;

    /**
     * @var DataProcessor
     */
    protected DataProcessor $dataProcessor;

    /**
     * ExtractTestimonialData Constructor.
     *
     * @param UploadFile $uploadFile
     * @param File $fileDriver
     * @param EmailAddress $emailValidation
     * @param Url $urlValidation
     * @param DataProcessor $dataProcessor
     */
    public function __construct(
        UploadFile    $uploadFile,
        File          $fileDriver,
        EmailAddress  $emailValidation,
        Url           $urlValidation,
        DataProcessor $dataProcessor
    ) {
        $this->uploadFile = $uploadFile;
        $this->fileDriver = $fileDriver;
        $this->emailValidation = $emailValidation;
        $this->urlValidation = $urlValidation;
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * Creates Testimonial.
     *
     * @param TestimonialInterface $testimonial
     * @param array $data
     * @param StoreInterface $store
     * @return TestimonialInterface
     * @throws GraphQlInputException
     */
    public function execute(TestimonialInterface $testimonial, array $data, StoreInterface $store): TestimonialInterface
    {
        try {
            $testimonialData = $this->manageData($data, $store);
            $testimonial = $this->dataProcessor->dataObjectProcessor($testimonialData, $testimonial);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $testimonial;
    }

    /**
     * Manage Testimonial Input data
     *
     * @param array $data
     * @param StoreInterface $store
     * @return array
     * @throws FileSystemException|GraphQlInputException
     */
    private function manageData(array $data, StoreInterface $store): array
    {
        if (isset($data['email']) && !$this->emailValidation->isValid($data['email'])) {
            throw new GraphQlInputException(__('Please Provide Appropriate Email Address.'));
        }

        if (isset($data['website']) && !$this->urlValidation->isValid($data['website'])) {
            throw new GraphQlInputException(__('Please Provide Appropriate Website URL.'));
        }

        if (isset($data['uploaded_file'])) {
            $fileContent = $data['uploaded_file'][0];
            $imageFullPath = $this->uploadFile->getMediaUrl();
            $ImagePath = $imageFullPath . $fileContent['image_name'];
            if ($this->fileDriver->isExists($ImagePath)) {
                $ImgContent = $this->fileDriver->fileGetContents($ImagePath);
                $base64 = base64_encode($ImgContent);
                if ($this->uploadFile->areEqual($base64, $fileContent['image_base64'])) {
                    $data['uploaded_file'] = $fileContent['image_name'];
                } else {
                    $data['uploaded_file'] = $this->uploadFile->uploadFile($fileContent);
                }
            } else {
                $data['uploaded_file'] = $this->uploadFile->uploadFile($fileContent);
            }
        }

        $data['store_id'] = (array)$store->getId();
        $data['enable'] = (string)Status::PENDING;

        return $data;
    }
}
