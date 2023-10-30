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

namespace MageINIC\TestimonialGraphQl\Model\Resolver\DataProvider;

use MageINIC\Testimonial\Model\Testimonial\FileInfo;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

/**
 * class for Upload Files
 */
class UploadFile
{
    /**
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * @var File
     */
    private File $fileDriver;

    /**
     * UploadFile Constructor.
     *
     * @param Filesystem $fileSystem
     * @param File $fileDriver
     */
    public function __construct(
        Filesystem $fileSystem,
        File       $fileDriver
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Upload Image file
     *
     * @param array $fileData
     * @return string
     * @throws FileSystemException
     */
    public function uploadFile(array $fileData): string
    {
        $uploadedFileName = "";
        $fileName = $fileData['image_name'] ?? rand() . time();

        if (isset($fileData['image_base64'])) {
            $mediaFullPath = $this->getMediaUrl();
            if (!$this->fileDriver->isExists($mediaFullPath)) {
                $this->fileDriver->createDirectory($mediaFullPath);
            }
            $fullFilepath = $mediaFullPath . $fileName;
            if ($this->fileDriver->isExists($fullFilepath)) {
                $fileName = rand() . time() . $fileName;
            }
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $fileContent = base64_decode($fileData['image_base64']);
            // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
            $savedFile = $this->fileDriver->fileOpen($mediaFullPath . $fileName, "wb");
            $this->fileDriver->fileWrite($savedFile, $fileContent);
            $this->fileDriver->fileClose($savedFile);
            $uploadedFileName = $fileName;
        }
        return $uploadedFileName;
    }

    /**
     * Get Media Url
     *
     * @return string
     */
    public function getMediaUrl(): string
    {
        $mediaPath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        return $mediaPath . FileInfo::ENTITY_MEDIA_PATH . '/';
    }

    /**
     * Check Are Equal or not
     *
     * @param string $sourceImage
     * @param string $uploadedImage
     * @return bool
     */
    public function areEqual(string $sourceImage, string $uploadedImage): bool
    {
        if (strcmp($sourceImage, $uploadedImage) !== 0) {
            return false;
        }
        return true;
    }
}
