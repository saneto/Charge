<?php
namespace App\Provider;

use Core\Provider\ProviderFactory;
use Psr\Container\ContainerInterface;

class CasProvider extends ProviderFactory
{
    /**
     * @return array
     */
    public static function getSettings(): array
    {
        return static::getServerSettings();
    }

    /**
     * @return callable
     */
    public function getCallable() : callable
    {
        return function (ContainerInterface $container): \phpCAS {
            $settings = static::getSettings();

            $phpCAS = new \phpCAS();
            $phpCAS::client(CAS_VERSION_3_0, $settings['host'], $settings['port'], $settings['context']);

            // activation du fichier de log
            if(AppProvider::getEnv() === 'dev') {
                $phpCAS::setVerbose(true);
                $phpCAS::setDebug($settings['logs']);
                $phpCAS::setExtraCurlOption(CURLOPT_VERBOSE, 1);
            }

            // certificat pour cURL
            if(AppProvider::getEnv() !== 'dev') {
                if(isset($settings['valid_cacert']) && $settings['valid_cacert'] !== false) {
                    $phpCAS::setCasServerCACert($settings['valid_cacert']);
                    $phpCAS::setExtraCurlOption(CURLOPT_SSL_VERIFYPEER, 1);
                }
            } else {
                $phpCAS::setNoCasServerValidation();
            }

            return $phpCAS;
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'phpcas';
    }

    /**
     * @return array
     */
    public static function getServerSettings(): array
    {
        return [
            'host' => "cas.tkmf.ad",
            'port' => 443,
            'context' => "",
            'valid_cacert' => "C:/Apache24/ssl/ca/tkmf_root_ca.crt",
            'logs' => LOGS_DIR . "/phpCAS.log"
        ];
    }
}
