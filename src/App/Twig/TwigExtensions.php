<?php
namespace App\Twig;

use App\Core;
use App\Entity\Doctrine;
use Core\Entity\EntityInterface;

class TwigExtensions extends \Twig_Extension
{
    /**
     * Ajoute un filtre à l'environnement de Twig en fonction de $filterName.
     * $filterName doit être en Snake case.
     *
     * @param string $filterName
     * @param array  $options
     *
     * @return \Twig_SimpleFilter
     */
    private function addFilter($filterName, array $options = []): \Twig_SimpleFilter
    {
        $filter = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $filterName))));
        $filter .= 'Filter';

        return new \Twig_SimpleFilter($filterName, [$this, $filter], $options);
    }

    /**
     * Ajoute une fonction à l'environnement de Twig en fonction de $functionName.
     * $functionName doit être en Snake case.
     *
     * @param string $functionName
     * @param bool   $need_env
     *
     * @return \Twig_SimpleFunction
     */
    private function addFunction($functionName, bool $need_env = false): \Twig_SimpleFunction
    {
        $function = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $functionName))));
        $function .= 'Function';

        $options = ($need_env) ? [
            'needs_environment' => true,
            'is_safe' => ['html']
        ] : [];

        return new \Twig_SimpleFunction($functionName, [$this, $function], $options);
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            $this->addFilter('zerolead'),
            $this->addFilter('ilot_range'),
            $this->addFilter('serie_label', ['is_safe' => ['html']]),
            $this->addFilter('colorize_quantity'),
            $this->addFilter('html', ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('classname', 'get_class'),
            new \Twig_SimpleFilter('reverse_color', 'reverse_color')
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            $this->addFunction('active_menu', true),
            $this->addFunction('block_title', true),
            $this->addFunction('render_tips', true),
        ];
    }

    /* ================================================== *
     * Filters                                            *
     * ================================================== */

    /**
     * Renvoie $int avec le nombre de zéros précédents ce dernier.
     *
     * @param int $int
     * @param int $length
     *
     * @return string
     */
    public static function zeroleadFilter($int, $length = 3)
    {
        return str_pad($int, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Renvoie une indication textuelle quant aux dimensions d'un ilôt.
     *
     * @param string $dimensions_range
     *
     * @return null|string
     */
    public static function ilotRangeFilter($dimensions_range)
    {
        switch($dimensions_range) {
        case -1:
            return "inf. à";
        case +1:
            return "sup. à";
        default:
            return null;
        }
    }

    /**
     * Renvoie le label d'une série s'il est non-null.
     * $html permet de formatter le label en HTML.
     *
     * @param Doctrine\SerieEntity $serie
     * @param bool        $html
     *
     * @return int|string
     */
    public static function serieLabelFilter(Doctrine\SerieEntity $serie, $html = true)
    {
        if($serie->getLabel() !== null) {
            return ($html) ? ("{$serie->getId()} " . '<small class="app_label">' . $serie->getLabel() . '</small>') : "{$serie->getId()} {$serie->getLabel()}";
        }

        return (string) $serie->getId();
    }

    /**
     * @param string $quantity
     * @param bool $hexa
     *
     * @return string
     */
    public static function colorizeQuantityFilter(?string $quantity, $hexa = false)
    {
        if($quantity >= 50) {
            return ($hexa) ? TK_RGB_GREEN : 'high';
        } elseif($quantity < 50 && $quantity >= 20) {
            return ($hexa) ? TK_RGB_ORANGE : 'medium';
        } else {
            return ($hexa) ? TK_RGB_RED : 'low';
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public static function htmlFilter($value)
    {
        return $value;
    }

    /* ================================================== *
     * Functions                                          *
     * ================================================== */

    /**
     * Ajoute la classes CSS 'active' si la variable 'menu_item' dans l'environnement de Twig correspond à $itemName.
     *
     * @param \Twig_Environment $env
     * @param string            $itemName
     *
     * @return null|string
     */
    public static function activeMenuFunction(\Twig_Environment $env, string $itemName): ?string
    {
        $globals = $env->getGlobals();
        $menu_item = $globals['menu_item'] ?: 'accueil';

        return ($menu_item === $itemName) ? 'class="active"' : null;
    }

    /**
     * Renvoi le titre de la vue Twig $template, avec un lien sur le titre si $href est précisé.
     *
     * @param \Twig_Environment $env
     * @param string            $template
     * @param null|string       $href
     *
     * @return null|string
     */
    public static function blockTitleFunction(\Twig_Environment $env, string $template, ?string $href = null): ?string
    {
        /**
         * @var \Twig_Template $twig_template
        */
        $twig_template = $env->loadTemplate($template);
        $title = $twig_template->renderBlock('title', []);

        return ($href !== null) ? '<a href="' . $href . '" title="' . $title . '">' . $title . '</a>' : $title;
    }

    /**
     * @param \Twig_Environment $env
     * @param EntityInterface $entity
     *
     * @param string $template
     * @return string
     */
    public static function renderTipsFunction(\Twig_Environment $env, EntityInterface $entity, string $template = 'list'): string
    {
        if ($entity->hasTips()) {
            switch ($template) {
                case 'list':
                    $templateFile = 'list.twig';
                    break;
                default:
                    $templateFile = false;
                    break;
            }

            if ($templateFile !== false) {
                return $env->render('tips/' . $templateFile, ['tips' => $entity->getTips()]);
            }
        }

        return "";
    }
}
