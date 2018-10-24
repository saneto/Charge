<?php
namespace Core\Provider;

use Pimple\Container;
use Psr\Container\ContainerInterface;

abstract class ProviderFactory implements ProviderInterface
{
    /**
     * @var null|string
     */
    private $key;

    /**
     * @var callable
     */
    protected $callable;

    /**
     * Provider constructor.
     *
     * @param null|string $key Clé pour identifier l'ojet dans le container
     */
    public function __construct($key = null)
    {
        $this->key = $key ?? static::getKey();
    }

    /**
     * @return array
     */
    public static function getSettings(): array
    {
        return [];
    }

    /**
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        if ($this->key === 'app') {
            foreach (static::getSettings() as $k => $setting) {
                $pimple['settings'][$k] = $setting;
            }
        } else {
            $settings = static::getSettings();

            if (!empty($settings)) {
                $pimple['settings'][$this->key] = $settings;
            }
        }

        $pimple[$this->key] = $this->getCallable();
    }
}
