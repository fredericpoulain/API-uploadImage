<?php

namespace Integration;


use App\Service\ImageService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageServiceTest extends TestCase
{
    private ImageService $imageService;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->imageService = new ImageService();
    }

    /**
     * @return void
     */
    public function testCheckTypeMime(): void
    {
        // Create a mock for UploadedFile with a valid MIME type
        $mockFileValid = $this->createMock(UploadedFile::class);
        $mockFileValid->method('getMimeType')->willReturn('image/jpeg');

        // Check that the MIME type is correct
        $this->assertTrue($this->imageService->checkTypeMime($mockFileValid));


        // Create a mock for UploadedFile with an invalid MIME type
        $mockFileInvalid = $this->createMock(UploadedFile::class);
        $mockFileInvalid->method('getMimeType')->willReturn('text/plain');

        // Test an invalid MIME type
        $this->assertFalse($this->imageService->checkTypeMime($mockFileInvalid));
    }

    /**
     * @return void
     */
    public function testCheckExtension(): void
    {
        // With a valid valid extension
        $mockFileValid = $this->createMock(UploadedFile::class);
        $mockFileValid->method('getMimeType')->willReturn('image/png');

        // Check that the extension is correct
        $this->assertTrue($this->imageService->checkExtension($mockFileValid));

        // Create a mock for UploadedFile with an invalid extension
        $mockFileInvalid = $this->createMock(UploadedFile::class);
        $mockFileInvalid->method('getMimeType')->willReturn('application/octet-stream');

        // Test an invalid extension
        $this->assertFalse($this->imageService->checkExtension($mockFileInvalid));
    }

    /**
     * @return void
     */
    public function testCheckSize(): void
    {
        // Create a mock for UploadedFile with a valid size
        $mockFileValid = $this->createMock(UploadedFile::class);
        $mockFileValid->method('getSize')->willReturn(5 * 1024 * 1024); // 5 MB

        // Check that the size is within the limit
        $this->assertTrue($this->imageService->checkSize($mockFileValid));

        // Create a mock for UploadedFile with a size invalid
        $mockFileInvalid = $this->createMock(UploadedFile::class);
        $mockFileInvalid->method('getSize')->willReturn(15 * 1024 * 1024); // 15 MB

        // Test for invalid size
        $this->assertFalse($this->imageService->checkSize($mockFileInvalid));
    }

    /**
     * @return void
     */
    public function testGenerateImageName(): void
    {
        // Create a mock for UploadedFile
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('getMimeType')->willReturn('image/jpeg');

        // Check that the generated image name has the correct structure
        $imageName = $this->imageService->generateImageName($mockFile);

        // Adjust the regex to accept different extensions
        $this->assertMatchesRegularExpression('/^[0-9a-f]+\.[0-9a-f]+\.(jpg|jpeg|png|webp)$/', $imageName);
    }
}
