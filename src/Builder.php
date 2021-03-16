<?php
namespace Exinfinite\Captcha;

class Builder {
    private $session_name = "__captcha_verification__";
    private $base_str = "abcdefhijkmnprstuvxy2345678AEFGHKMNPQRSTUWXY";
    private $width = 150;
    private $height = 45;
    private $txt = 4;
    private $line = 6;
    private $pixel = 180;
    private $font_dir = __DIR__ . "/../fonts/%s.ttf";
    public function __construct($session_name = null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (is_string($session_name)) {
            $this->session_name = trim($session_name);
        }
        $this->container = &$_SESSION;
        $this->verification__session = substr(str_shuffle($this->base_str), 0, $this->txt);
    }
    private function reset() {
        unset($this->container[$this->session_name]);
    }
    /**
     * 圖像大小
     *
     * @param Integer $w
     * @param Integer $h
     * @return this
     */
    public function setSize($w, $h) {
        list($this->width, $this->height) = [(int) $w, (int) $h];
        return $this;
    }
    /**
     * 干擾線條數量
     *
     * @param Integer $line
     * @return this
     */
    public function setLine($line) {
        if ($line > 0) {
            $this->line = $line;
        }
        return $this;
    }
    /**
     * 干擾像素數量
     *
     * @param Integer $pixel
     * @return this
     */
    public function setPixel($pixel) {
        if ($pixel > 0) {
            $this->pixel = $pixel;
        }
        return $this;
    }
    /**
     * 干擾線條
     *
     * @param Integer $line
     * @return this
     */
    private function lineIns() {
        for ($i = 0; $i < $this->line; $i++) {
            $line_color = imagecolorallocatealpha($this->im, rand(0, 200), rand(0, 200), rand(0, 200), rand(0, 50));
            imageline($this->im, rand(0, $this->width), rand(0, $this->height),
                rand($this->height, $this->width), rand(0, $this->height), $line_color);
        }
        return $this;
    }
    /**
     * 干擾像素
     *
     * @param Integer $pixel
     * @return this
     */
    private function pixelIns() {
        for ($i = 0; $i < $this->pixel; $i++) {
            $pixel_color = imagecolorallocatealpha($this->im, rand(0, 200), rand(0, 200), rand(0, 200), rand(0, 50));
            imagesetpixel($this->im, rand() % $this->width,
                rand() % $this->height, $pixel_color);
        }
        return $this;
    }
    /**
     * 設定顯示文字
     *
     * @return this
     */
    private function setTxt() {
        $txt_x_seed = rand(100 / $this->txt, $this->width / $this->txt);
        $fonts = [
            ['font' => 'Jura', 'size' => rand(26, 32)],
            ['font' => 'Borghs', 'size' => rand(26, 28)],
            ['font' => 'bethhand', 'size' => rand(30, 34)],
            ['font' => 'AntykwaBold', 'size' => rand(22, 26)],
            ['font' => 'Duality', 'size' => rand(24, 28)],
            ['font' => 'VeraSansBold', 'size' => rand(22, 24)],
            ['font' => 'Jura', 'size' => rand(26, 32)],
            ['font' => 'StayPuft', 'size' => rand(24, 26)],
            ['font' => 'Ding-DongDaddyO', 'size' => rand(26, 28)],
        ];
        for ($i = 0; $i < $this->txt; $i++) {
            $f = $fonts[array_rand($fonts)];
            $font_color = imagecolorallocatealpha($this->im, rand(0, 200), rand(0, 200), rand(0, 200), rand(0, 50));
            $angel = rand(-29, 27);
            $x = $i * $txt_x_seed + rand(8, 12);
            $y = rand(25, 40);
            imagettftext($this->im, $f['size'], $angel, $x + rand(1, 3), $y + rand(0, 1), $font_color, sprintf($this->font_dir, $f['font']), $this->verification__session[$i]);
            imagettftext($this->im, $f['size'], $angel, $x, $y, $font_color, sprintf($this->font_dir, $f['font']), $this->verification__session[$i]);
        }
        return $this;
    }
    /**
     * 輸出圖像
     *
     * @return void
     */
    public function build() {
        $this->reset();
        $this->im = @imagecreatetruecolor($this->width, $this->height);
        mt_srand((double) microtime() * 1000000);
        $this->container[$this->session_name] = $this->verification__session;
        $bgColor = imagecolorallocate($this->im, rand(230, 255), rand(230, 255), rand(230, 255));
        imagefill($this->im, 0, 0, $bgColor);
        $this->setTxt()->lineIns()->pixelIns();
        ob_start();
        imagepng($this->im);
        $this->imagedata = ob_get_clean();
        imagedestroy($this->im);
        header('Content-Type: image/png');
        exit($this->imagedata);
    }
    /**
     * 驗證碼比對
     *
     * @param String $input
     * @param boolean $case_sensitive
     * @return Bool
     */
    public function verify($input, $case_sensitive = false) {
        $rst = false;
        $input = trim($input);
        $against = $this->getTxt();
        if ($case_sensitive === true) {
            $rst = strcmp($input, $against) == 0;
        } else {
            $rst = strcasecmp($input, $against) == 0;
        }
        $this->reset();
        return $rst;
    }
    /**
     * 取得產生的驗證碼
     *
     * @return void
     */
    public function getTxt() {
        return trim($this->container[$this->session_name]);
    }
}