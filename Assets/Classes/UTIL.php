<?php 
class UTIL{
    public static function ext($name){
        return substr($name,strrpos($name, '.', -0)+1);
    }
    public static function captcha(){
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,200,50,$background_color);
        $line_color = imagecolorallocate($image, 64,64,64);
        $number_of_lines=rand(3,7);
        for($i=0;$i<$number_of_lines;$i++){
            imageline($image,0,rand()%50,250,rand()%50,$line_color);
        }
        $pixel = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<500;$i++){
            imagesetpixel($image,rand()%200,rand()%50,$pixel);
        }
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length-1)];
        $word='';
        $text_color = imagecolorallocate($image, 0,0,0);
        $cap_length=6;// No. of character in image
        for ($i = 0; $i< $cap_length;$i++){
            $letter = $allowed_letters[rand(0, $length-1)];
            imagestring($image, 5,  5+($i*30), 20, $letter, $text_color);
            $word.=$letter;
        }
        $_SESSION[SI]['captcha_string'] = $word;
        // imagepng($image, "captcha_image.png");
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }
    private function multiexplode ($delimiters,$string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }
    public static function getSize($size){
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}
?>