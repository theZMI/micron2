<?php

class SimpleCaptcha extends \LordDashMe\SimpleCaptcha\Captcha
{
    protected function allowedCodeCharacters()
    {
        return 'ABCDEFHJKLMNPRSTUVWXYZ23456789';
    }

    protected function backgroundsDirectoryPath()
    {
        return BASEPATH . 'i/image/simple_captcha/backgrounds/';
    }

    protected function textPosition($textBoxSize, $backgroundSize)
    {
        $boxWidth = \abs($textBoxSize[6] - $textBoxSize[2]);
        $boxHeight = \abs($textBoxSize[5] - $textBoxSize[1]);

        $textPositionXMin = 0;
        $textPositionXMax = $backgroundSize['bg_width'] - $boxWidth;

        if ($textPositionXMin > $textPositionXMax) {
            $textPositionXMax = $textPositionXMin;
        }

        $textPositionX = \mt_rand($textPositionXMin, $textPositionXMax);

        $textPositionYMin = $boxHeight;
        $textPositionYMax = ($backgroundSize['bg_height'] - ($boxHeight / 2) - 5);

        if ($textPositionYMin > $textPositionYMax) {
            $temp_textPositionY = $textPositionYMin;
            $textPositionYMin = $textPositionYMax;
            $textPositionYMax = $temp_textPositionY;
        }

        $textPositionY = \mt_rand(intval($textPositionYMin), intval($textPositionYMax));

        if ($textPositionX < 30 && $textPositionY < 40) {
            return array(
                'text_position_x' => 30,
                'text_position_y' => 45
            );
        }

        return array(
            'text_position_x' => $textPositionX,
            'text_position_y' => $textPositionY
        );
    }

    public function getSession()
    {
        $this->startSession();
        $data = $this->collectSessionData();
        @session_write_close();

        return $data;
    }

    protected function startSession()
    {
        @session_start();
    }
}