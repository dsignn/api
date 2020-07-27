<?php
declare(strict_types=1);

namespace App\Module\Organization\Url;

/**
 * Interface SlugifyInterface
 * @package App\Module\Organization\Url
 */
interface SlugifyInterface {

    /**
     * @param string $path
     * @return mixed
     */
    public function slugify(string $path);
}