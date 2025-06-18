<?php
namespace Avetify\Files;

use Avetify\Interface\Pout;
use Avetify\Modules\Printer;

class FfmpegUtils {
    public static function prepare(){
        putenv("LD_LIBRARY_PATH=");
    }

    public static function getInfo(string $videoPath) : ?array {
        $ffprobe = '/usr/bin/ffprobe';
        if (!file_exists($videoPath)) {
            Printer::errorPrint("Video Not Found!" . Pout::br());
            return null;
        }

        $cmd = "$ffprobe -v quiet -print_format json -show_format -show_streams " . escapeshellarg($videoPath);
        $output = shell_exec($cmd);

        if (empty($output)) {
            Printer::errorPrint("ffprobe returned no output. Check permissions or command." . Pout::br());
        }

        $info = json_decode($output, true);

        if (!$info) {
            Printer::errorPrint("JSON decode failed: " . json_last_error_msg() . Pout::br());
            return null;
        }

        return $info;
    }

    public static function extractData(string $videoPath) : ?FFMedia {
        $info = self::getInfo($videoPath);
        if($info == null) return null;

        $streams = $info['streams'];
        $format = $info['format'];

        $videoStream = null;
        foreach ($streams as $stream) {
            if ($stream['codec_type'] === 'video') {
                $videoStream = $stream;
                break;
            }
        }

        $ffMedia = new FFMedia();
        if($videoStream){
            $ffMedia->codecName = $videoStream['codec_name'] ?? "";
            $ffMedia->profile = $videoStream['profile'] ?? "";
            $ffMedia->width = $videoStream['width'] ?? 0;
            $ffMedia->height = $videoStream['height'] ?? 0;
            $ffMedia->sampleAspectRatio = $videoStream['sample_aspect_ratio'] ?? "";
            $ffMedia->displayAspectRatio = $videoStream['display_aspect_ratio'] ?? "";
            $ffMedia->avgFrameRate = $videoStream['avg_frame_rate'] ?? 0;
        }

        if($format){
            $ffMedia->duration = $format['duration'] ?? 0;
            $ffMedia->bitRate = $format['bit_rate'] ?? 0;
            $ffMedia->size = $format['size'] ?? 0;
        }

        return $ffMedia;
    }

    public static function takeScreen(
        string $videoPath,
        string $targetScreenPath,
        int $second,
        ?int $targetWidth = null
    ) {
        $escapedVideo = escapeshellarg($videoPath);
        $escapedOutput = escapeshellarg($targetScreenPath);

        $scaleOption = "";
        $qualityOption = "";

        $ext = strtolower(pathinfo($targetScreenPath, PATHINFO_EXTENSION));
        $isWebp = $ext === 'webp';

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            Printer::errorPrint("Unsupported screenshot format: .{$ext}" . Pout::br());
            return;
        }

        if (!is_null($targetWidth)) {
            $info = self::extractData($videoPath);
            if ($info && $info->width > 0 && $info->height > 0) {
                $aspectRatio = $info->height / $info->width;
                $targetHeight = intval($targetWidth * $aspectRatio);

                if ($targetHeight % 2 !== 0) {
                    $targetHeight++;
                }

                $scaleOption = "-vf scale={$targetWidth}:{$targetHeight}";

                if ($ext === 'jpg' || $ext === 'jpeg') {
                    if ($targetWidth >= 1280) {
                        $qualityOption = "-q:v 2";
                    } elseif ($targetWidth >= 640) {
                        $qualityOption = "-q:v 5";
                    } else {
                        $qualityOption = "-q:v 8";
                    }
                } elseif ($ext === 'webp') {
                    $qualityOption = "-lossless 0 -compression_level 6 -qscale 80";
                }
            }
        }

        $outputOptions = $qualityOption;
        if ($isWebp) {
            $outputOptions = "-frames:v 1 -c:v libwebp {$qualityOption}";
        }

        $cmd = "ffmpeg -ss {$second} -i {$escapedVideo} -frames:v 1 {$scaleOption} {$outputOptions} {$escapedOutput} -y";
        $cmd = preg_replace('/\s+/', ' ', $cmd); // حذف فاصله‌های اضافه
        shell_exec($cmd);
    }

    public static function takeGif(
        string $videoPath,
        string $targetGifPath,
        int $second,
        int $duration,
        ?int $frameRate = null,
        ?int $targetWidth = null
    ) {
        $escapedVideo = escapeshellarg($videoPath);
        $escapedOutput = escapeshellarg($targetGifPath);

        $info = self::extractData($videoPath);

        if (!$info || $info->width <= 0 || $info->height <= 0) {
            Printer::errorPrint("Failed to extract video info." . Pout::br());
            return;
        }

        if (is_null($frameRate)) {
            $parts = explode('/', $info->avgFrameRate);
            $frameRate = (count($parts) == 2 && (int)$parts[1] != 0)
                ? intval($parts[0] / $parts[1])
                : 10;
        }

        $scaleFilter = "";
        if (!is_null($targetWidth)) {
            $aspectRatio = $info->height / $info->width;
            $targetHeight = intval($targetWidth * $aspectRatio);
            $scaleFilter = "scale={$targetWidth}:{$targetHeight}:flags=lanczos";
        } else {
            $scaleFilter = "scale={$info->width}:{$info->height}:flags=lanczos";
        }

        $palettePath = tempnam(sys_get_temp_dir(), 'palette') . '.png';

        $paletteCmd = "ffmpeg -ss {$second} -t {$duration} -i {$escapedVideo} -vf \"$scaleFilter,fps={$frameRate},palettegen=max_colors=128\" -y " . escapeshellarg($palettePath);
        $paletteCmd = preg_replace('/\s+/', ' ', $paletteCmd);
        shell_exec($paletteCmd);

        $gifCmd = "ffmpeg -ss {$second} -t {$duration} -i {$escapedVideo} -i " . escapeshellarg($palettePath) .
            " -filter_complex \"[0:v] $scaleFilter, fps=$frameRate [x]; [x][1:v] paletteuse=dither=bayer:bayer_scale=5\" -y {$escapedOutput}";

        $gifCmd = preg_replace('/\s+/', ' ', $gifCmd);
        shell_exec($gifCmd);

        @unlink($palettePath);
    }

    public static function takeWebp(
        string $videoPath,
        string $targetWebpPath,
        int $second,
        int $duration,
        ?int $frameRate = null,
        ?int $targetWidth = null
    ) {
        $escapedVideo = escapeshellarg($videoPath);
        $escapedOutput = escapeshellarg($targetWebpPath);

        $info = self::extractData($videoPath);

        if (!$info || $info->width <= 0 || $info->height <= 0) {
            Printer::errorPrint("Failed to extract video info." . Pout::br());
            return;
        }

        if (is_null($frameRate)) {
            $parts = explode('/', $info->avgFrameRate);
            $frameRate = (count($parts) == 2 && (int)$parts[1] != 0)
                ? intval($parts[0] / $parts[1])
                : 10;
        }

        $scaleFilter = "";
        if (!is_null($targetWidth)) {
            $aspectRatio = $info->height / $info->width;
            $targetHeight = intval($targetWidth * $aspectRatio);
            $scaleFilter = "scale={$targetWidth}:{$targetHeight}";
        } else {
            $scaleFilter = "scale={$info->width}:{$info->height}";
        }

        $cmd = "ffmpeg -ss {$second} -t {$duration} -i {$escapedVideo} " .
            "-vf \"$scaleFilter,fps=$frameRate\" " .
            "-loop 0 -preset default -an -vsync 0 " .
            "-c:v libwebp -lossless 0 -compression_level 6 -q:v 60 " .
            "{$escapedOutput} -y";

        $cmd = preg_replace('/\s+/', ' ', $cmd);
        shell_exec($cmd);
    }

    public static function takeMp4(
        string $videoPath,
        string $targetMp4Path,
        int $startSecond,
        int $duration,
        bool $removeAudio = true,
        ?int $frameRate = null,
        ?int $targetWidth = null
    ) {
        $escapedVideo = escapeshellarg($videoPath);
        $escapedOutput = escapeshellarg($targetMp4Path);

        $info = self::extractData($videoPath);
        if (!$info || $info->width <= 0 || $info->height <= 0) {
            Printer::errorPrint("Failed to extract video info." . Pout::br());
            return;
        }

        if (is_null($frameRate)) {
            $parts = explode('/', $info->avgFrameRate);
            if (count($parts) === 2 && (int)$parts[1] !== 0) {
                $frameRate = intval($parts[0] / $parts[1]);
            } else {
                $frameRate = 30;
            }
        }

        $scaleFilter = "";
        if (!is_null($targetWidth) && $targetWidth > 0 && $targetWidth < $info->width) {
            $aspectRatio = $info->height / $info->width;
            $targetHeight = intval(round($targetWidth * $aspectRatio));
            if ($targetHeight % 2 !== 0) {
                $targetHeight++;
            }
            $scaleFilter = "scale={$targetWidth}:{$targetHeight}";
        }

        $filterParts = [];
        if ($scaleFilter !== "") {
            $filterParts[] = $scaleFilter;
        }
        if ($frameRate > 0) {
            $filterParts[] = "fps={$frameRate}";
        }
        $filter = "";
        if (count($filterParts) > 0) {
            $filter = "-filter:v \"" . implode(',', $filterParts) . "\"";
        }

        $audioOption = $removeAudio ? "-an" : "-c:a copy";

        $cmd = "ffmpeg -ss {$startSecond} -t {$duration} -i {$escapedVideo} {$filter} -c:v libx264 -preset veryfast -crf 23 {$audioOption} -movflags +faststart {$escapedOutput} -y";

        exec($cmd . " 2>&1", $outputLines, $returnVar);

        if ($returnVar !== 0) {
            Printer::errorPrint("FFmpeg command failed with code {$returnVar}:" . Pout::br());
            Printer::errorPrint(implode("\n", $outputLines) . Pout::br());
        }
    }

    public static function takeCompilationMp4(
        string $videoPath,
        string $targetMp4Path,
        array $pieces, // e.g. [[1, 4], [6, 2]]
        bool $removeAudio = true,
        ?int $frameRate = null,
        ?int $targetWidth = null,
    ) {
        $escapedVideo = escapeshellarg($videoPath);
        $escapedOutput = escapeshellarg($targetMp4Path);

        $info = self::extractData($videoPath);
        if (!$info || $info->width <= 0 || $info->height <= 0) {
            Printer::errorPrint("Failed to extract video info." . Pout::br());
            return;
        }

        if (is_null($frameRate)) {
            $parts = explode('/', $info->avgFrameRate);
            if (count($parts) === 2 && (int)$parts[1] !== 0) {
                $frameRate = intval($parts[0] / $parts[1]);
            } else {
                $frameRate = 30;
            }
        }

        $aspectRatio = $info->height / $info->width;
        $targetHeight = intval(round($targetWidth * $aspectRatio));
        if ($targetHeight % 2 !== 0) {
            $targetHeight++;
        }

        $scaleFilter = "";
        if (!is_null($targetWidth) && $targetWidth > 0 && $targetWidth < $info->width) {
            $scaleFilter = "scale={$targetWidth}:{$targetHeight}";
        }

        $filterParts = [];
        if ($scaleFilter !== "") {
            $filterParts[] = $scaleFilter;
        }
        if ($frameRate > 0) {
            $filterParts[] = "fps={$frameRate}";
        }
        $filter = "";
        if (count($filterParts) > 0) {
            $filter = "-vf \"" . implode(',', $filterParts) . "\"";
        }

        $audioOption = $removeAudio ? "-an" : "-c:a aac -b:a 128k";

        $tempDir = sys_get_temp_dir() . '/ffmpeg_compilation_' . uniqid();
        if (!mkdir($tempDir) && !is_dir($tempDir)) {
            Printer::errorPrint("Failed to create temp dir for compilation." . Pout::br());
            return;
        }

        $tempFiles = [];
        foreach ($pieces as $index => $piece) {
            if (!is_array($piece) || count($piece) < 2) continue;
            $start = floatval($piece[0]);
            $dur = floatval($piece[1]);
            $tempFile = "{$tempDir}/part_{$index}.mp4";
            $tempFileEscaped = escapeshellarg($tempFile);

            $cmd = "ffmpeg -ss {$start} -t {$dur} -i {$escapedVideo} {$filter} -c:v libx264 -preset veryfast -crf 23 {$audioOption} -movflags +faststart {$tempFileEscaped} -y";
            exec($cmd . " 2>&1", $outputLines, $returnVar);
            if ($returnVar !== 0) {
                Printer::errorPrint("Failed to extract piece {$index}: " . implode("\n", $outputLines) . Pout::br());
                foreach ($tempFiles as $f) @unlink($f);
                @rmdir($tempDir);
                return;
            }
            $tempFiles[] = $tempFile;
        }

        $concatListFile = "{$tempDir}/concat_list.txt";
        $handle = fopen($concatListFile, 'w');
        if (!$handle) {
            Printer::errorPrint("Failed to create concat list file." . Pout::br());
            foreach ($tempFiles as $f) @unlink($f);
            @rmdir($tempDir);
            return;
        }
        foreach ($tempFiles as $file) {
            fwrite($handle, "file '{$file}'\n");
        }
        fclose($handle);

        $concatListFileEscaped = escapeshellarg($concatListFile);

        // ✅ Final concat with re-encoding to fix timing/audio issues
        $finalAudioOption = $removeAudio ? "-an" : "-c:a aac -b:a 128k";
        $concatCmd = "ffmpeg -f concat -safe 0 -i {$concatListFileEscaped} -c:v libx264 -preset veryfast -crf 23 {$finalAudioOption} -movflags +faststart {$escapedOutput} -y";
        exec($concatCmd . " 2>&1", $outputLines, $returnVar);
        if ($returnVar !== 0) {
            Printer::errorPrint("Failed to concat pieces: " . implode("\n", $outputLines) . Pout::br());
            foreach ($tempFiles as $f) @unlink($f);
            @unlink($concatListFile);
            @rmdir($tempDir);
            return;
        }

        foreach ($tempFiles as $f) {
            @unlink($f);
        }
        @unlink($concatListFile);
        @rmdir($tempDir);
    }

    public static function takeRandomCompilation(
        string $videoPath,
        string $targetMp4Path,
        float $duration,        // final total duration
        int $piecesCount,       // how many segments to extract
        bool $removeAudio = true,
        ?int $frameRate = null,
        ?int $targetWidth = null,
    ) {
        $info = self::extractData($videoPath);
        if (!$info || $info->duration <= 0) {
            Printer::errorPrint("Failed to get video info." . Pout::br());
            return;
        }

        $videoDuration = floatval($info->duration);
        $maxAllowed = $videoDuration / 5;
        if ($duration > $maxAllowed) {
            $duration = $maxAllowed;
        }

        if ($piecesCount <= 0 || $duration <= 0 || $piecesCount > 100) {
            Printer::errorPrint("Invalid duration or piece count." . Pout::br());
            return;
        }

        $pieceDuration = $duration / $piecesCount;
        $segmentLength = $videoDuration / $piecesCount;

        if ($segmentLength < $pieceDuration) {
            Printer::errorPrint("Cannot fit $piecesCount segments of $pieceDuration sec each into video duration." . Pout::br());
            return;
        }

        $pieces = [];

        for ($i = 0; $i < $piecesCount; $i++) {
            $segmentStart = $i * $segmentLength;
            $maxStartInSegment = $segmentLength - $pieceDuration;

            if ($maxStartInSegment <= 0) {
                $randomOffset = 0;
            } else {
                $randomOffset = mt_rand(0, intval($maxStartInSegment * 1000)) / 1000.0;
            }

            $clipStart = $segmentStart + $randomOffset;
            $clipStart = min($clipStart, $videoDuration - $pieceDuration);

            $pieces[] = [$clipStart, $pieceDuration];
        }

        // Call the compilation builder
        self::takeCompilationMp4(
            $videoPath,
            $targetMp4Path,
            $pieces,
            $removeAudio,
            $frameRate,
            $targetWidth,
        );
    }
}

class FFMedia {
    //video stream
    public string $codecName = "";
    public string $profile = "";
    public int $width = 0;
    public int $height = 0;
    public string $sampleAspectRatio = "";
    public string $displayAspectRatio = "";
    public string $avgFrameRate = "";

    //format
    public float $duration = 0;
    public int $bitRate = 0;
    public int $size = 0;
}
