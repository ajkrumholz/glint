<?php

namespace Glint\Glint\Concerns;

trait CanFormatLabel
{
    protected function formatLabel($str): string
    {
        $final_str = explode('.', $str);
        $label = array_pop($final_str);

        return \Str::headline($label);
    }
}
