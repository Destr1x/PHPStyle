<?php

class System {

    win = 'Windows';

    public function Init () {
        system('');
    }

    public function Clear () {
        if (PHP_OS === win) {
            system('cls');
        } else {
            system('clear');
        }
    }

    public function Title (string $title) {
        if (PHP_OS === win) {
            return PHP_OS("title " . str($title));
        }
    }

    public function Size (int $x, int $y) {
        if (PHP_OS === win) {
            return system("mode con cols=" . $x . " lines=".$y);
        }
    }

    public function Command (string $command) {
        return system($command)
    }
}

class Cursor {

    public function HideCursor () {
        if (PHP_OS === 'windows') {
            system('echo "\033[?25l";')
        } else {
            echo '\033[?25l';
            flush();
        }
    }

    public function ShowCursor () {
        if (PHP_OS === 'windows') {
            system('echo "\033[?25h";')
        } else {
            echo '\033[?25h';
            flush();
        }
    }
}

class MakeColors {
    public function MakeAnsi ($color, $text) : string{
        return "\033[38;2;" . $color . "m" . $text "\033[38;2;255;255;255m";
    }

    public function RemoveAnsi ($color) : string{
        $color = str_replace("\033[38;2;", '', $color);
        $color = str_replace('m', '', $color);
        $color = str_replace('50m', '', $color);
        $color = str_replace("\x1b[38", '', $color);
        return $color;
    }

    public function MakeRGBColor (array $var1, array $var2) : array{
        $color = array_slice($var1, 0, 12);
        $color = array_merge($col, array_slice($var2, 0, 12));
        $color = array_merge($col, array_reverse($col));
        return $color;
    }

    public function start (string $color) : string{
        return '\033[38;2;' . $color . 'm';
    }

    public function end () : string{
        return '\033[38;2;255;255;255m';
    }

    public function makeText (string $color, string $text, bool $end = false) : string{
        $end = $end ? MakeColors::end() : "";

        return $color . $text . $end;
    }

    public function getSpaces (string $text) : int{
        return strlen($text) - strlen(ltrim($text));
    }

    public function makeRainbow (...$colors) : array{
        $rainbow = [];

        foreach($colors as $color) {
            $color = substr($color, 0, 24);

            for($i = 0; $i < strlen($color); $i++) {
                $rainbow[] = $color[$i];
            }
        }

        return $rainbow;
    }

    public function reverse (array $colors) : array{
        $color = array($colors);
        foreach (array_reverse($color) as $col) {
            $colors[] = $col;
        }
        
        return $colors;
    }

    public function mixColors (string $color1, string $color2, bool $reverse = true) : array{
        $color1 = MakeColors::RemoveAnsi($color1);
        $color2 = MakeColors::RemoveAnsi($color2);

        $fade1 = Colors::StaticMIX([$col1, $col2], $start = false);
        $fade2 = Colors::StaticMIX([$fade1, $col2], $start = false);
        $fade3 = Colors::StaticMIX([$fade1, $col1], $start = false);
        $fade4 = Colors::StaticMIX([$fade2, $col2], $start = false);
        $fade5 = Colors::StaticMIX([$fade1, $fade3], $start = false);
        $fade6 = Colors::StaticMIX([$fade3, $col1], $start = false);
        $fade7 = Colors::StaticMIX([$fade1, $fade2], $start = false);
    
        $mixed = [$col1, $fade6, $fade3, $fade5, $fade1, $fade7, $fade2, $fade4, $col2];
    
        return $reverse ? MakeColors::reverse($mixed) : $mixed;
    }
}

class Colors {
    public function StaticRGB (int $r, int $g, int $b) : string{
        return MakeColors::start($r . ";" . $g . ";" . $b);
    }

    public function DynamicRGB(int $r1, int $g1, int $b1, int $r2, int $g2, int $b2, int $steps = 10): array {
        $gradient = [];
        
        for ($i = 0; $i <= $steps; $i++) {
            $r = (int)($r1 + ($r2 - $r1) * ($i / $steps));
            $g = (int)($g1 + ($g2 - $g1) * ($i / $steps));
            $b = (int)($b1 + ($b2 - $b1) * ($i / $steps));
            
            $gradient[] = [$r, $g, $b];
        }
        
        return $gradient;
    }

    public function StaticMIX (array $colors, bool $start = true) : string{
        $rgb = [];

        foreach($color as $colors) {
            $colors = MakeColors::RemoveAnsi($colors);
            $colors = explode(";", $colors);
            $r = (int)$col[0];
            $g = (int)$col[1];
            $b = (int)$col[2];
            $rgb[] = [$r, $g, $b];
        }

        $r = round(array_sum(array_column($rgb, 0)) / count($rgb));
        $g = round(array_sum(array_column($rgb, 1)) / count($rgb));
        $b = round(array_sum(array_column($rgb, 2)) / count($rgb));

        $rgb = $r . ";" . $g . ";" . $b;

        return $start ? MakeColors::start($rgb) : $rgb;
    }

    public function DynamicMIX (array $colors) : array {
        $color = [];
        
        for ($i = 0; $i < count($colors) - 1; $i++) {
            $color[] = [$colors[$i], $colors[$i + 1]];
        }
    
        $mix = [];
        foreach ($color as $pair) {
            $mixedColors[] = MakeColors::mixcolors($pair[0], $pair[1], false);
        }

        $final = [];
        foreach ($mixedColors as $colorSet) {
            foreach ($colorSet as $color) {
                $final[] = $color;
            }
        }
    
        return MakeColors::reverse($final);
    }

    public function Symbol (string $symbol, string $col, string $col_left_right, string $left = '[', string $right = ']') : string{
        return "{$col_left_right}{$left}{$col}{$symbol}{$col_left_right}{$right}" . Colors::$reset;
    }

    $black_to_white = ["m;m;m"];
    $black_to_red = ["m;0;0"];
    $black_to_green = ["0;m;0"];
    $black_to_blue = ["0;0;m"];
    
    $white_to_black = ["n;n;n"];
    $white_to_red = ["255;n;n"];
    $white_to_green = ["n;255;n"];
    $white_to_blue = ["n;n;255"];
    
    $red_to_black = ["n;0;0"];
    $red_to_white = ["255;m;m"];
    $red_to_yellow = ["255;m;0"];
    $red_to_purple = ["255;0;m"];
    
    $green_to_black = ["0;n;0"];
    $green_to_white = ["m;255;m"];
    $green_to_yellow = ["m;255;0"];
    $green_to_cyan = ["0;255;m"];
    
    $blue_to_black = ["0;0;n"];
    $blue_to_white = ["m;m;255"];
    $blue_to_cyan = ["0;m;255"];
    $blue_to_purple = ["m;0;255"];
    
    $yellow_to_red = ["255;n;0"];
    $yellow_to_green = ["n;255;0"];
    
    $purple_to_red = ["255;0;n"];
    $purple_to_blue = ["n;0;255"];
    
    $cyan_to_green = ["0;255;n"];
    $cyan_to_blue = ["0;n;255"];
    
    $dynamic_colors = [
        $black_to_white, $black_to_red, $black_to_green, $black_to_blue,
        $white_to_black, $white_to_red, $white_to_green, $white_to_blue,
        $red_to_black, $red_to_white, $red_to_yellow, $red_to_purple,
        $green_to_black, $green_to_white, $green_to_yellow, $green_to_cyan,
        $blue_to_black, $blue_to_white, $blue_to_cyan, $blue_to_purple,
        $yellow_to_red, $yellow_to_green,
        $purple_to_red, $purple_to_blue,
        $cyan_to_green, $cyan_to_blue
    ];
    
    foreach ($dynamic_colors as &$color) {
        $col = 20;
        $reversed_col = 220;
        $dbl_col = 20;
        $dbl_reversed_col = 220;
        $content = array_shift($color);
    
        for ($i = 0; $i < 12; $i++) {
            if (strpos($content, 'm') !== false) {
                $result = str_replace('m', $col, $content);
                $color[] = $result;
            } elseif (strpos($content, 'n') !== false) {
                $result = str_replace('n', $reversed_col, $content);
                $color[] = $result;
            }
            $col += 20;
            $reversed_col -= 20;
        }
    
        for ($i = 0; $i < 12; $i++) {
            if (strpos($content, 'm') !== false) {
                $result = str_replace('m', $dbl_reversed_col, $content);
                $color[] = $result;
            } elseif (strpos($content, 'n') !== false) {
                $result = str_replace('n', $dbl_col, $content);
                $color[] = $result;
            }
            $dbl_col += 20;
            $dbl_reversed_col -= 20;
        }
    }
    
    $red_to_blue = MakeColors::makergbcol($red_to_purple, $purple_to_blue);
    $red_to_green = MakeColors::makergbcol($red_to_yellow, $yellow_to_green);
    
    $green_to_blue = MakeColors::makergbcol($green_to_cyan, $cyan_to_blue);
    $green_to_red = MakeColors::makergbcol($green_to_yellow, $yellow_to_red);
    
    $blue_to_red = MakeColors::makergbcol($blue_to_purple, $purple_to_red);
    $blue_to_green = MakeColors::makergbcol($blue_to_cyan, $cyan_to_green);
    
    $rainbow = MakeColors::makerainbow($red_to_green, $green_to_blue, $blue_to_red);

    $dynamic_colors[] = $red_to_blue;
    $dynamic_colors[] = $red_to_green;
    $dynamic_colors[] = $green_to_blue;
    $dynamic_colors[] = $green_to_red;
    $dynamic_colors[] = $blue_to_red;
    $dynamic_colors[] = $blue_to_green;
    $dynamic_colors[] = $rainbow;
    
    $red = MakeColors::start('255;0;0');
    $green = MakeColors::start('0;255;0');
    $blue = MakeColors::start('0;0;255');
    $white = MakeColors::start('255;255;255');
    $black = MakeColors::start('0;0;0');
    $gray = MakeColors::start('150;150;150');
    $yellow = MakeColors::start('255;255;0');
    $purple = MakeColors::start('255;0;255');
    $cyan = MakeColors::start('0;255;255');
    $orange = MakeColors::start('255;150;0');
    $pink = MakeColors::start('255;0;150');
    $turquoise = MakeColors::start('0;150;255');
    $light_gray = MakeColors::start('200;200;200');
    $dark_gray = MakeColors::start('100;100;100');
    $light_red = MakeColors::start('255;100;100');
    $light_green = MakeColors::start('100;255;100');
    $light_blue = MakeColors::start('100;100;255');
    $dark_red = MakeColors::start('100;0;0');
    $dark_green = MakeColors::start('0;100;0');
    $dark_blue = MakeColors::start('0;0;100');
    $reset = $white;
    
    $static_colors = [
        $red, $green, $blue, $white, $black, $gray,
        $yellow, $purple, $cyan, $orange, $pink, $turquoise,
        $light_gray, $dark_gray, $light_red, $light_green, $light_blue,
        $dark_red, $dark_green, $dark_blue, $reset
    ];
    
    $all_colors = array_merge($dynamic_colors, $static_colors);

}

class Colorate {
    public function Color(string $color, string $text, bool $end = true) : string {
        return MakeColors::makeText($color, $text, $end);
    }

    public function Error(string $text, string $color = "\033[31m", bool $end = false, int $spaces = 1, bool $enter = true, $wait = false) : string {
        $content = MakeColors::makeText(
            color: $color,
            text: str_repeat("\n", $spaces) . $text,
            end: $end
        );

        if ($enter) {
            echo $content;
            $var = readline();
        } else {
            echo $content;
            $var = null;
        }
    
        if ($wait === true) {
            exit();
        } elseif ($wait !== false) {
            sleep($wait);
        }
    
        return $var;
    }

    public function Vertical(array $color, string $text, int $speed = 1, int $start = 0, int $stop = 0, int $cut = 0, bool $fill = false): string {
        $color = array_slice($color, $cut);
        $lines = explode("\n", $text);
        $result = "";
    
        $nstart = 0;
        $color_n = 0;
        foreach ($lines as $lin) {
            $colorR = $color[$color_n];
            if ($fill) {
                $result .= str_repeat(" ", MakeColors::getSpaces($lin)) .
                    join("", array_map(fn($x) => MakeColors::MakeAnsi($colorR, $x), str_split(trim($lin)))) . "\n";
            } else {
                $result .= str_repeat(" ", MakeColors::getSpaces($lin)) .
                    MakeColors::MakeAnsi($colorR, trim($lin)) . "\n";
            }
    
            if ($nstart != $start) {
                $nstart++;
                continue;
            }
    
            if (trim($lin)) {
                if (($stop == 0 && $color_n + $speed < count($color)) || ($stop != 0 && $color_n + $speed < $stop)) {
                    $color_n += $speed;
                } elseif ($stop == 0) {
                    $color_n = 0;
                } else {
                    $color_n = $stop;
                }
            }
        }
    
        return rtrim($result);
    }
    
    public function Horizontal(array $color, string $text, int $speed = 1, int $cut = 0): string {
        $color = array_slice($color, $cut);
        $lines = explode("\n", $text);
        $result = "";
    
        foreach ($lines as $lin) {
            $carac = str_split($lin);
            $color_n = 0;
            foreach ($carac as $car) {
                $colorR = $color[$color_n];
                $result .= str_repeat(" ", MakeColors::getSpaces($car)) . MakeColors::MakeAnsi($colorR, trim($car));
                if ($color_n + $speed < count($color)) {
                    $color_n += $speed;
                } else {
                    $color_n = 0;
                }
            }
            $result .= "\n";
        }
    
        return rtrim($result);
    }

    public function Diagonal(array $color, string $text, int $speed = 1, int $cut = 0): string {
        $color = array_slice($color, $cut);
        $lines = explode("\n", $text);
        $result = "";
        $color_n = 0;
    
        foreach ($lines as $lin) {
            $carac = str_split($lin);
            foreach ($carac as $car) {
                $colorR = $color[$color_n];
                $result .= str_repeat(" ", MakeColors::getSpaces($car)) . MakeColors::MakeAnsi($colorR, trim($car));
                if ($color_n + $speed < count($color)) {
                    $color_n += $speed;
                } else {
                    $color_n = 1;
                }
            }
            $result .= "\n";
        }
    
        return rtrim($result);
    }

    public function DiagonalBackwards(array $color, string $text, int $speed = 1, int $cut = 0): string {
        $color = array_slice($color, $cut);
        $lines = explode("\n", $text);
        $result = "";
        $color_n = 0;
    
        foreach ($lines as $lin) {
            $carac = array_reverse(str_split($lin));
            $resultL = '';
            foreach ($carac as $car) {
                $colorR = $color[$color_n];
                $resultL = str_repeat(" ", MakeColors::getSpaces($car)) . MakeColors::MakeAnsi($colorR, trim($car)) . $resultL;
                if ($color_n + $speed < count($color)) {
                    $color_n += $speed;
                } else {
                    $color_n = 0;
                }
            }
            $result .= "\n" . $resultL;
        }
    
        return trim($result);
    }

    public function Format(string $text, array $second_chars, callable $mode, array $principal_col, string $second_col): string {
        $ctext = $mode($principal_col, $text);
        $ntext = "";
    
        foreach (str_split($ctext) as $x) {
            if (in_array($x, $second_chars)) {
                $x = MakeColors::MakeAnsi($second_col, $x);
            }
            $ntext .= $x;
        }
    
        return $ntext;
    }
}

class Anime () {
    public function Fade(string $text, array $color, callable $mode, $time = true, float $interval = 0.05, bool $hide_cursor = true, bool $enter = false) {
        if ($hide_cursor) {
            Cursor::HideCursor();
        }
    
        if (is_int($time)) {
            $time *= 15;
        }
    
        global $passed;
        $passed = false;
    
        if ($enter) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                Anime::Input();
                exit(0);
            }
        }
    
        if ($time === true) {
            while (true) {
                if ($passed !== false) {
                    break;
                }
                Anime::_Anime($text, $color, $mode, $interval);
                $ncolor = array_slice($color, 1);
                $ncolor[] = $color[0];
                $color = $ncolor;
            }
        } else {
            for ($i = 0; $i < $time; $i++) {
                if ($passed !== false) {
                    break;
                }
                Anime::_Anime($text, $color, $mode, $interval);
                $ncolor = array_slice($color, 1);
                $ncolor[] = $color[0];
                $color = $ncolor;
            }
        }
    
        if ($hide_cursor) {
            Cursor::ShowCursor();
        }
    }

    public function Move($text, $color = [], $time = true, $interval = 0.01, $hide_cursor = true, $enter = false) {
        if ($hide_cursor) {
            Cursor::HideCursor();
        }
    
        if (is_int($time)) {
            $time *= 15;
        }
    
        global $passed;
        $passed = false;
    
        $terminal = terminal_size();
        $columns = $terminal->columns;
    
        if ($enter) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                Anime::input();
                exit(0);
            }
        }
    
        $count = 0;
        $mode = 1;
    
        if ($time === true) {
            while (!$passed) {
                if ($mode == 1) {
                    if ($count >= ($columns - (max(array_map('strlen', explode("\n", $text))) + 1))) {
                        $mode = 2;
                    }
                    $count++;
                } elseif ($mode == 2) {
                    if ($count <= 0) {
                        $mode = 1;
                    }
                    $count--;
                }
    
                Anime::_anime(
                    implode("\n", array_map(fn($line) => str_repeat(' ', $count) . $line, explode("\n", $text))),
                    $color,
                    fn($a, $b) => $b,
                    $interval
                );
            }
        } else {
            for ($i = 0; $i < $time; $i++) {
                if ($passed) {
                    break;
                }
                if ($mode == 1) {
                    if ($count >= ($columns - (max(array_map('strlen', explode("\n", $text))) + 1))) {
                        $mode = 2;
                    }
                } elseif ($mode == 2) {
                    if ($count <= 0) {
                        $mode = 1;
                    }
                }
    
                    Anime::_anime(
                    implode("\n", array_map(fn($line) => str_repeat(' ', $count) . $line, explode("\n", $text))),
                    $color,
                    fn($a, $b) => $b,
                    $interval
                );
    
                $count++;
            }
        }
    
        if ($hide_cursor) {
            Cursor::ShowCursor();
        }
    }

    public function Bar($length, $carac_0 = '[ ]', $carac_1 = '[0]', $color = "\033[37m", $mode = 'mode_function', $interval = 0.5, $hide_cursor = true, $enter = false, $center = false) {
        if ($hide_cursor) {
            Cursor::HideCursor();
        }
    
        if (is_array($color)) {
            while (count($color) < $length) {
                $color = array_merge($color, $color);
            }
        }
    
        global $passed;
        $passed = false;
    
        if ($enter) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                Anime::_input();
                exit(0);
            }
        }
    
        for ($i = 0; $i <= $length; $i++) {
            $bar = str_repeat($carac_1, $i) . str_repeat($carac_0, $length - $i);
            if ($passed) {
                break;
            }
            if (is_array($color)) {
                $colored_bar = $mode($color, $bar);
            } else {
                $colored_bar = $color . $bar . "\033[0m";
            }
    
            if ($center) {
                echo Center::XCenter($colored_bar) . "\n";
            } else {
                echo $colored_bar . "\n";
            }
    
            usleep($interval * 1000000);
            system('cls');
        }
    
        if ($hide_cursor) {
            Cursor::ShowCursor();
        }
    }
}

class Write {
    public function Print($text, $color, $interval = 0.05, $hide_cursor = true, $end = "\033[0m") {
        if ($hide_cursor) {
            echo "\033[?25l";
        }

        self::_write($text, $color, $interval);

        echo $end;
        fflush(STDOUT);

        if ($hide_cursor) {
            echo "\033[?25h";
        }
    }
    public function Input($text, $color, $interval = 0.05, $hide_cursor = true, $input_color = "\033[0m", $end = "\033[0m", $func = 'readline') {
        if ($hide_cursor) {
            echo "\033[?25l";
        }

        self::_write($text, $color, $interval);

        $valor = $func($input_color);

        echo $end;
        fflush(STDOUT);

        if ($hide_cursor) {
            echo "\033[?25h";
        }

        return $valor;
    }

    private function _write($text, $color, $interval) {
        $lines = str_split($text);
        $colorLength = count($color);

        while (count($lines) > $colorLength) {
            $color = array_merge($color, $color);
        }

        $n = 0;
        foreach ($lines as $line) {
            $colorCode = $color[$n % $colorLength];
            echo "\033[38;5;{$colorCode}m" . $line;
            fflush(STDOUT);
            usleep($interval * 1000000);
            if (trim($line) !== '') {
                $n++;
            }
        }
    }
}

class Center {
    const CENTER = 'CENTER';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    public static function XCenter($text, $spaces = null, $icon = " ") {
        if ($spaces === null) {
            $spaces = self::_xspaces($text);
        }
        $lines = explode("\n", $text);
        $centered = array_map(function($line) use ($spaces, $icon) {
            return str_repeat($icon, $spaces) . $line;
        }, $lines);
        return implode("\n", $centered);
    }

    public static function YCenter($text, $spaces = null, $icon = "\n") {
        if ($spaces === null) {
            $spaces = self::_yspaces($text);
        }
        return str_repeat($icon, $spaces) . $text;
    }

    public function Center($text, $xspaces = null, $yspaces = null, $xicon = " ", $yicon = "\n") {
        if ($xspaces === null) {
            $xspaces = self::_xspaces($text);
        }

        if ($yspaces === null) {
            $yspaces = self::_yspaces($text);
        }

        $text = str_repeat($yicon, $yspaces) . $text;
        return self::XCenter($text, $xspaces, $xicon);
    }

    public function GroupAlign($text, $align = self::CENTER) {
        $align = strtoupper($align);
        switch ($align) {
            case self::CENTER:
                return self::XCenter($text);
            case self::LEFT:
                return $text;
            case self::RIGHT:
                $length = self::_terminal_size()['columns'];
                $maxLineSize = max(array_map('strlen', explode("\n", $text)));
                $lines = explode("\n", $text);
                $rightAligned = array_map(function($line) use ($length, $maxLineSize) {
                    return str_repeat(' ', $length - $maxLineSize) . $line;
                }, $lines);
                return implode("\n", $rightAligned);
            default:
                throw new self::BadAlignment();
        }
    }

    public function TextAlign($text, $align = self::CENTER) {
        $align = strtoupper($align);
        $lines = explode("\n", $text);
        $mlen = max(array_map('strlen', $lines));
        switch ($align) {
            case self::CENTER:
                $centeredLines = array_map(function($line) use ($mlen) {
                    return str_repeat(' ', (int)(($mlen - strlen($line)) / 2)) . $line;
                }, $lines);
                return implode("\n", $centeredLines);
            case self::LEFT:
                return $text;
            case self::RIGHT:
                $rightAlignedLines = array_map(function($line) use ($mlen) {
                    return str_repeat(' ', $mlen - strlen($line)) . $line;
                }, $lines);
                return implode("\n", $rightAlignedLines);
            default:
                throw new self::BadAlignment();
        }
    }
    private function _xspaces($text) {
        $cols = self::_terminal_size()['columns'];
        $lines = explode("\n", $text);
        $maxLineLength = max(array_map('strlen', $lines));
        return (int)(($cols - $maxLineLength) / 2);
    }

    private function _yspaces($text) {
        $lines = self::_terminal_size()['lines'];
        $textLines = explode("\n", $text);
        $numTextLines = count($textLines);
        return (int)(($lines - $numTextLines) / 2);
    }
    private function _terminal_size() {
        $cols = (int)shell_exec('tput cols');
        $lines = (int)shell_exec('tput lines');
        return ['columns' => $cols, 'lines' => $lines];
    }

    public function BadAlignment() {
        throw new Exception("Choose a correct alignment: Center::CENTER / Center::LEFT / Center::RIGHT");
    }
}

class Add {

    public static function Add($banner1, $banner2, $spaces = 0, $center = false) {
        if ($center) {
            $split1 = count(explode("\n", $banner1));
            $split2 = count(explode("\n", $banner2));
            if ($split1 > $split2) {
                $spaces = intdiv($split1 - $split2, 2);
            } elseif ($split2 > $split1) {
                $spaces = intdiv($split2 - $split1, 2);
            } else {
                $spaces = 0;
            }
        }

        $maxLines = max(count(explode("\n", $banner1)), count(explode("\n", $banner2)));
        if ($spaces > $maxLines) {
            throw new self::MaximumSpaces($spaces);
        }

        $ban1 = explode("\n", $banner1);
        $ban2 = explode("\n", $banner2);

        $size = self::_length($ban1);

        $ban1 = self::_edit($ban1, $size);

        $ban1Count = count($ban1);
        $ban2Count = count($ban2);

        $text = '';

        for ($i = 0; $i < $spaces; $i++) {
            if ($ban1Count >= $ban2Count) {
                $ban1Data = $ban1[$i] ?? str_repeat(" ", $size);
                $ban2Data = '';
            } else {
                $ban1Data = str_repeat(" ", $size);
                $ban2Data = $ban2[$i] ?? '';
            }
            $text .= $ban1Data . $ban2Data . "\n";
        }

        while ($i < $ban1Count || $i < $ban2Count) {
            $ban1Data = $ban1[$i] ?? str_repeat(" ", $size);
            $ban2Data = $ban2[$i] ?? '';
            $text .= $ban1Data . $ban2Data . "\n";
            $i++;
        }

        return $text;
    }

    private static function _length($banner) {
        $maxLength = 0;
        foreach ($banner as $line) {
            $maxLength = max($maxLength, strlen($line));
        }
        return $maxLength;
    }

    private static function _edit($banner, $size) {
        return array_map(function($line) use ($size) {
            return str_pad($line, $size);
        }, $banner);
    }

    public static function MaximumSpaces($spaces) {
        throw new Exception("Too much spaces [{$spaces}].");
    }
}

class Banner {

    public function Box($content, $up_left, $up_right, $down_left, $down_right, $left_line, $up_line, $right_line, $down_line) {
        $l = 0;
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strlen($line) > $l) {
                $l = strlen($line);
            }
        }
        if ($l % 2 == 1) {
            $l++;
        }
        $box = $up_left . str_repeat($up_line, $l) . $up_right . "\n";
        foreach ($lines as $line) {
            $box .= $left_line . " " . $line . str_repeat(" ", $l - strlen($line)) . " " . $right_line . "\n";
        }
        $box .= $down_left . str_repeat($down_line, $l) . $down_right . "\n";
        return $box;
    }

    public function SimpleCube($content) {
        $l = 0;
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strlen($line) > $l) {
                $l = strlen($line);
            }
        }
        if ($l % 2 == 1) {
            $l++;
        }
        $cube = "__" . str_repeat("_", $l) . "__\n";
        $cube .= "| " . str_repeat(" ", intdiv($l, 2)) . str_repeat(" ", intdiv($l, 2)) . " |\n";
        foreach ($lines as $line) {
            $cube .= "| " . $line . str_repeat(" ", $l - strlen($line)) . " |\n";
        }
        $cube .= "|_" . str_repeat("_", $l) . "_|\n";
        return $cube;
    }

    public function DoubleCube($content) {
        return self::Box($content, "╔═", "═╗", "╚═", "═╝", "║", "═", "║", "═");
    }

    public function Lines($content, $color = null, $mode = null, $line = '═', $pepite = 'ቐ') {
        $l = 1;
        foreach (explode("\n", $content) as $line) {
            if (strlen($line) > $l) {
                $l = strlen($line);
            }
        }
        $mode = $color ? $mode : function($text) { return $text; };
        $box = $mode("─" . str_repeat($line, $l) . $pepite . str_repeat($line, $l) . "─", $color);
        $assembly = $box . "\n" . $content . "\n" . $box;
        $final = '';
        foreach (explode("\n", $assembly) as $line) {
            $final .= Center::XCenter($line) . "\n";
        }
        return $final;
    }

    public function Arrow($icon = 'a', $size = 2, $number = 2, $direction = 'right') {
        $spaces = str_repeat(' ', $size + 1);
        $arrow = '';
        $structure = [$size + 2, [$size * 2, $size * 2]];
        $count = 0;
        if ($direction == 'right') {
            for ($i = 0; $i < $structure[1][0]; $i++) {
                $line = str_repeat($icon, $structure[0]);
                $arrow .= str_repeat(' ', $count) . implode($spaces, array_fill(0, $number, $line)) . "\n";
                $count += 2;
            }
            for ($i = 0; $i < $structure[1][0] + 1; $i++) {
                $line = str_repeat($icon, $structure[0]);
                $arrow .= str_repeat(' ', $count) . implode($spaces, array_fill(0, $number, $line)) . "\n";
                $count -= 2;
            }
        } elseif ($direction == 'left') {
            for ($i = 0; $i < $structure[1][0]; $i++) {
                $count += 2;
            }
            for ($i = 0; $i < $structure[1][0]; $i++) {
                $line = str_repeat($icon, $structure[0]);
                $arrow .= str_repeat(' ', $count) . implode($spaces, array_fill(0, $number, $line)) . "\n";
                $count -= 2;
            }
            for ($i = 0; $i < $structure[1][0] + 1; $i++) {
                $line = str_repeat($icon, $structure[0]);
                $arrow .= str_repeat(' ', $count) . implode($spaces, array_fill(0, $number, $line)) . "\n";
                $count += 2;
            }
        }
        return $arrow;
    }
}