<?php


namespace App\DefaultPanel\Lib\Filters;


use App\DefaultPanel\Interfaces\IFilter;
use Illuminate\Contracts\Support\Htmlable;

class FormBuilder  implements Htmlable {

    private array $filters;

    public function __construct(array $filters) {

        $this->filters = $filters;
    }

    static function build(array $filters) {
        $html = '';
        /** @var IFilter $class */
        foreach ($filters as $id => $class) {
            $html .= (new $class)->toHtml();
        }
        return $html;
    }

    public function toHtml() {
        return self::build($this->filters);
    }
}
