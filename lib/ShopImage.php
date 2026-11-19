<?php

/**
 * Картинки товаров: upl/shop/products/{product_id}/{name}.jpg (до 1600px) и {name}_thumb.jpg (600px) для каталога.
 * Исходник любого размера уменьшается сразу при загрузке, чтобы в каталоге не грузились многомегабайтные файлы
 */
class ShopImage
{
    const DIR        = 'upl/shop/products/';
    const MAX_SIZE   = 1600;
    const THUMB_SIZE = 600;
    const QUALITY    = 82;
    const MAX_FILE   = 30 * 1024 * 1024;
    const MAX_PIXELS = 40000000; // 40 Мпикс: больше - это уже «бомба» из заголовков, а не фотография товара
    const MAX_MEMORY = 536870912; // Потолок, до которого поднимаем memory_limit на время обработки

    public static function dir(int $product_id): string
    {
        return self::DIR . $product_id . '/';
    }

    public static function url(int $product_id, string $name, bool $thumb = false): string
    {
        return Root(self::dir($product_id) . $name . ($thumb ? '_thumb' : '') . '.jpg');
    }

    /**
     * Сохраняет картинку из файла (загруженного или локального) и возвращает её имя (без .jpg)
     */
    public static function saveFromFile(string $path, int $product_id): string
    {
        $info = @getimagesize($path);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF], true)) {
            throw new InvalidArgumentException('Нужна картинка JPEG, PNG или GIF');
        }
        if (filesize($path) > self::MAX_FILE) {
            throw new InvalidArgumentException('Картинка больше ' . (self::MAX_FILE >> 20) . ' МБ');
        }
        // Размеры из заголовка не должны уводить процесс в гигабайты памяти (PNG на 30 МБ бывает 50 000 × 50 000)
        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            throw new InvalidArgumentException(
                'Картинка слишком большая: ' . $info[0] . '×' . $info[1] . ' пикселей (максимум ' . (self::MAX_PIXELS / 1000000) . ' Мпикс)'
            );
        }

        // GD держит картинку в памяти несжатой (~5 байт на пиксель): фото 4000x3000 - это ~60 МБ, а на сайте лимит 32M (core/config/php_ini.php).
        // Повёрнутое по EXIF фото на время поворота лежит в памяти дважды
        $isRotated = $info[2] === IMAGETYPE_JPEG && self::orientation($path) !== 1;
        $needBytes = min(self::MAX_MEMORY, (int)($info[0] * $info[1] * 5 * ($isRotated ? 2.6 : 1.6)) + 32 * 1024 * 1024);
        if (self::bytes(ini_get('memory_limit')) !== -1 && self::bytes(ini_get('memory_limit')) < $needBytes) {
            ini_set('memory_limit', (string)$needBytes);
        }
        set_time_limit(60);

        $src = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => imagecreatefrompng($path),
            IMAGETYPE_GIF  => imagecreatefromgif($path),
        };
        if (!$src) {
            throw new RuntimeException('Не удалось прочитать картинку');
        }
        if ($info[2] === IMAGETYPE_JPEG) {
            $src = self::fixOrientation($src, $path);
        }

        $name = bin2hex(random_bytes(6));
        $dir  = BASEPATH . self::dir($product_id);
        FileSys::makeDir($dir);
        self::saveResized($src, self::MAX_SIZE, "{$dir}{$name}.jpg");
        self::saveResized($src, self::THUMB_SIZE, "{$dir}{$name}_thumb.jpg");
        imagedestroy($src);
        return $name;
    }

    public static function delete(int $product_id, string $name): void
    {
        if (!preg_match('/^[a-f0-9]{12}$/', $name)) {
            return;
        }
        foreach (["{$name}.jpg", "{$name}_thumb.jpg"] as $file) {
            @unlink(BASEPATH . self::dir($product_id) . $file);
        }
    }

    public static function deleteAll(int $product_id): void
    {
        $dir = BASEPATH . self::dir($product_id);
        foreach (glob("{$dir}*.jpg") ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($dir);
    }

    // Уменьшает по большей стороне (не увеличивает) и сохраняет в JPEG на белом фоне (у PNG может быть прозрачность)
    private static function saveResized($src, int $maxSize, string $file): void
    {
        $w     = imagesx($src);
        $h     = imagesy($src);
        $scale = min(1, $maxSize / max($w, $h));
        $nw    = max(1, (int)round($w * $scale));
        $nh    = max(1, (int)round($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imageinterlace($dst, true); // Прогрессивный JPEG
        $isSaved = imagejpeg($dst, $file, self::QUALITY);
        imagedestroy($dst);
        if (!$isSaved) {
            @unlink($file); // Иначе в товар запишется имя несуществующего файла
            throw new RuntimeException('Не удалось сохранить картинку');
        }
    }

    private static function bytes(string $value): int
    {
        $value = trim($value);
        if ($value === '-1') {
            return -1;
        }
        $n = (int)$value;
        return match (strtolower(substr($value, -1))) {
            'g'     => $n << 30,
            'm'     => $n << 20,
            'k'     => $n << 10,
            default => $n,
        };
    }

    // Фото с телефона хранят поворот в EXIF
    private static function orientation(string $path): int
    {
        return function_exists('exif_read_data') ? (int)(@exif_read_data($path)['Orientation'] ?? 1) : 1;
    }

    private static function fixOrientation($img, string $path)
    {
        $angle = match (self::orientation($path)) {
            3       => 180,
            6       => -90,
            8       => 90,
            default => 0,
        };
        if ($angle === 0) {
            return $img;
        }
        $rotated = imagerotate($img, $angle, 0);
        imagedestroy($img); // Исходник больше не нужен - освобождаем память сразу
        return $rotated ?: throw new RuntimeException('Не удалось повернуть картинку');
    }
}
