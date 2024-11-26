<?php

namespace App\DefaultPanel\Lib\Filters\Html;

use Illuminate\Support\Str;
use Spatie\Html\Elements\Input;

trait Container {

    /**
     * @param Input $input
     * @param $label
     * @param string $containerClass
     * @param string $inputClass
     * @param string $labelClass
     * @return \Spatie\Html\Elements\Div
     */
    function htmlContainer($input, $label, $inputClass = 'col-lg-12', $containerClass = 'col-lg-3', $labelClass = 'col-lg-12') {


        $_label = html()->label($label)->class("control-label $labelClass")->for(Str::slug($label));
        $_input = html()->div(
            $input->id(Str::slug($label))
                ->attributeIf($this->isSelect($input), 'data-placeholder', __("Select"))
                ->prependChildIf($this->isSelect($input), html()->option("", ""))
        )->class($inputClass);
        return html()->div()->class("form-group $containerClass")->addChildren([$_label, $_input]);
    }

    public function isSelect($input) {
        return Str::contains($input->getAttribute('class'), "select-search");
    }
}
