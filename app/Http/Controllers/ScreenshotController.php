<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Screenshot Controller - Server-side rendering với Puppeteer
 * 
 * Xử lý screenshot cho genealogy tree lớn vượt giới hạn browser
 */
class ScreenshotController extends Controller
{
    /**
     * POST /api/screenshot
     * 
     * Nhận HTML/SVG từ client, render thành PNG ở server
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function capture(Request $request)
    {
        try {
            $validated = $request->validate([
                'html' => 'required|string',
                'width' => 'nullable|integer|min:100|max:20000',
                'height' => 'nullable|integer|min:100|max:20000',
                'scale' => 'nullable|numeric|min:0.1|max:5',
                'format' => 'nullable|in:png,jpeg,jpg',
                'quality' => 'nullable|integer|min:1|max:100',
                'filename' => 'nullable|string'
            ]);

            $html = $validated['html'];
            $width = $validated['width'] ?? 1200;
            $height = $validated['height'] ?? 800;
            $scale = $validated['scale'] ?? 2;
            $format = $validated['format'] ?? 'png';
            $quality = $validated['quality'] ?? 90;
            $filename = $validated['filename'] ?? 'screenshot';

            Log::info('Screenshot request', [
                'size' => "{$width}x{$height}",
                'scale' => $scale,
                'format' => $format
            ]);

            // Kiểm tra xem screenshot service có đang chạy không
            $serviceUrl = config('services.screenshot.url', 'http://localhost:3000');
            
            if (!$this->isServiceRunning($serviceUrl)) {
                return response()->json([
                    'error' => 'Screenshot service is not running',
                    'message' => 'Please start the service: npm start',
                    'service_url' => $serviceUrl
                ], 503);
            }

            // Gọi screenshot service
            $imageData = $this->callScreenshotService($serviceUrl, [
                'html' => $html,
                'width' => $width,
                'height' => $height,
                'scale' => $scale,
                'format' => $format,
                'quality' => $quality,
                'fullPage' => true
            ]);

            if (!$imageData) {
                throw new \Exception('Failed to generate screenshot');
            }

            // Trả về image binary
            $contentType = $format === 'png' ? 'image/png' : 'image/jpeg';
            $extension = $format === 'png' ? 'png' : 'jpg';

            return response($imageData)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.{$extension}\"")
                ->header('Content-Length', strlen($imageData));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Screenshot error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Screenshot generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/screenshot/url
     * 
     * Puppeteer truy cập URL thật, click nút download, dùng code domtoimage gốc
     * Không thay đổi code frontend!
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function captureUrl(Request $request)
    {
        try {
            $validated = $request->validate([
                'url' => 'required|url',
                'selector' => 'nullable|string',
                'width' => 'nullable|integer|min:800|max:5000',
                'height' => 'nullable|integer|min:600|max:5000',
                'timeout' => 'nullable|integer|min:10|max:120'
            ]);

            $url = $validated['url'];
            $selector = $validated['selector'] ?? '.btn_ctrl_svg1';
            $width = $validated['width'] ?? 1920;
            $height = $validated['height'] ?? 1080;
            $timeout = ($validated['timeout'] ?? 60) * 1000; // Convert to ms

            // Get user's cookies to pass to Puppeteer
            $cookies = $this->getUserCookies($request);

            Log::info('Screenshot URL request', [
                'url' => $url,
                'selector' => $selector,
                'cookies_count' => count($cookies)
            ]);

            // Call screenshot-url service
            $serviceUrl = config('services.screenshot.url_service', 'http://localhost:3001');
            
            if (!$this->isServiceRunning($serviceUrl)) {
                return response()->json([
                    'error' => 'Screenshot URL service is not running',
                    'message' => 'Please start: pm2 start task-cli/screenshot-url-service.js --name screenshot-url-service',
                    'service_url' => $serviceUrl
                ], 503);
            }

            $imageData = $this->callScreenshotService($serviceUrl, [
                'url' => $url,
                'selector' => $selector,
                'cookies' => $cookies,
                'width' => $width,
                'height' => $height,
                'timeout' => $timeout,
                'waitForDownload' => true
            ], '/screenshot-url');

            if (!$imageData) {
                throw new \Exception('Failed to capture URL screenshot');
            }

            // Extract filename from URL
            $filename = $this->extractFilename($url);

            return response($imageData)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.png\"")
                ->header('Content-Length', strlen($imageData));

        } catch (\Exception $e) {
            Log::error('Screenshot URL error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'URL screenshot failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user cookies to pass to Puppeteer
     */
    private function getUserCookies(Request $request): array
    {
        $cookies = [];
        $domain = parse_url(config('app.url'), PHP_URL_HOST);

        // Get Laravel session cookie
        foreach ($request->cookies as $name => $value) {
            $cookies[] = [
                'name' => $name,
                'value' => $value,
                'domain' => $domain,
                'path' => '/',
                'httpOnly' => true,
                'secure' => $request->secure()
            ];
        }

        return $cookies;
    }

    /**
     * Extract filename from URL
     */
    private function extractFilename(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $query = $parsed['query'] ?? '';
        
        // Extract pid from query if exists
        if (preg_match('/pid=([^&]+)/', $query, $matches)) {
            return 'tree-' . $matches[1];
        }
        
        return 'screenshot-' . time();
    }

    /**
     * POST /api/screenshot/svg
     * 
     * Chuyên dụng cho SVG element (genealogy tree)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function captureSvg(Request $request)
    {
        try {
            $validated = $request->validate([
                'svg_html' => 'required|string',
                'bbox' => 'required|array',
                'bbox.x' => 'required|numeric',
                'bbox.y' => 'required|numeric',
                'bbox.width' => 'required|numeric',
                'bbox.height' => 'required|numeric',
                'scale' => 'nullable|numeric|min:0.1|max:5',
                'format' => 'nullable|in:png,jpeg,jpg',
                'filename' => 'nullable|string'
            ]);

            $svgHtml = $validated['svg_html'];
            $bbox = $validated['bbox'];
            $scale = $validated['scale'] ?? 2;
            $format = $validated['format'] ?? 'png';
            $filename = $validated['filename'] ?? 'genealogy-tree';

            // Tạo HTML wrapper cho SVG
            $html = $this->wrapSvgInHtml($svgHtml, $bbox);

            // Tính dimensions
            $width = ceil($bbox['width'] + 400); // Padding 200px mỗi bên
            $height = ceil($bbox['height'] + 400);

            Log::info('SVG screenshot request', [
                'bbox' => $bbox,
                'final_size' => "{$width}x{$height}",
                'scale' => $scale
            ]);

            // Call screenshot service
            $serviceUrl = config('services.screenshot.url', 'http://localhost:3000');
            
            $imageData = $this->callScreenshotService($serviceUrl, [
                'html' => $html,
                'width' => $width,
                'height' => $height,
                'scale' => $scale,
                'format' => $format,
                'fullPage' => true,
                'backgroundColor' => '#ffffff'
            ]);

            if (!$imageData) {
                throw new \Exception('Failed to generate SVG screenshot');
            }

            // Return image
            $contentType = $format === 'png' ? 'image/png' : 'image/jpeg';
            $extension = $format === 'png' ? 'png' : 'jpg';

            return response($imageData)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.{$extension}\"")
                ->header('Content-Length', strlen($imageData));

        } catch (\Exception $e) {
            Log::error('SVG screenshot error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'SVG screenshot failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/screenshot/health
     * 
     * Kiểm tra screenshot service có hoạt động không
     */
    public function health()
    {
        $serviceUrl = config('services.screenshot.url', 'http://localhost:3000');
        
        try {
            $response = \Http::timeout(5)->get("{$serviceUrl}/health");
            
            if ($response->successful()) {
                return response()->json([
                    'status' => 'ok',
                    'service' => $response->json(),
                    'service_url' => $serviceUrl
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Service returned error',
                'service_url' => $serviceUrl
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not reachable: ' . $e->getMessage(),
                'service_url' => $serviceUrl,
                'help' => 'Start service with: npm start'
            ], 503);
        }
    }

    /**
     * Kiểm tra screenshot service có đang chạy không
     */
    private function isServiceRunning(string $serviceUrl): bool
    {
        try {
            $response = \Http::timeout(3)->get("{$serviceUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gọi screenshot service qua HTTP
     */
    private function callScreenshotService(string $serviceUrl, array $data, string $endpoint = '/screenshot')
    {
        try {
            $response = \Http::timeout(60) // 60 seconds cho large images
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$serviceUrl}{$endpoint}", $data);

            if (!$response->successful()) {
                throw new \Exception("Service returned status {$response->status()}");
            }

            return $response->body();

        } catch (\Exception $e) {
            Log::error('Screenshot service call failed', [
                'error' => $e->getMessage(),
                'url' => $serviceUrl,
                'endpoint' => $endpoint
            ]);
            throw $e;
        }
    }

    /**
     * Wrap SVG trong HTML document hoàn chỉnh
     */
    private function wrapSvgInHtml(string $svgHtml, array $bbox): string
    {
        $width = $bbox['width'] + 400;
        $height = $bbox['height'] + 400;
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: {$width}px;
            height: {$height}px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        svg {
            display: block;
        }
    </style>
</head>
<body>
    {$svgHtml}
</body>
</html>
HTML;
    }
}
