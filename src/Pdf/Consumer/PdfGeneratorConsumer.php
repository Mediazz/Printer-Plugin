<?php

namespace Mediazz\Printer\Pdf\Consumer;

use GuzzleHttp\Exception\GuzzleException;
use Mediazz\Printer\Exceptions\Pdf\ConnectionErrorException;
use Mediazz\Printer\Exceptions\Pdf\NoPdfReturnedException;

/**
 * Class PdfGeneratorConsumer
 * @package Mediazz\Printer\Pdf\Consumer
 */
class PdfGeneratorConsumer
{
    /**
     * @var mixed
     */
    private $apiUrl;

    /**
     * @var mixed
     */
    private $apiValidator;

    /**
     * @var \GuzzleHttp\Client
     */
    private $apiConsumer;

    /**
     * CoinMarketCapConsumer constructor.
     */
    public function __construct()
    {
        $this->apiUrl = config('mediazz.printer.pdf.url');
        $this->apiValidator = config('mediazz.printer.pdf.validator');

        $this->apiConsumer = new \GuzzleHttp\Client(['base_uri' => $this->apiUrl]);
    }

    /**
     * @param string $html
     * @param array $options
     * @return string
     * @throws ConnectionErrorException
     * @throws NoPdfReturnedException
     */
    public function pdfByHtml(string $html, array $options = [])
    {
        $query = http_build_query([
            'validator' => $this->apiValidator,
            'orientation' => $options['orientation'] ?? 'portrait',
        ]);
        try {
            $response = $this->apiConsumer
                ->post('?' . $query, [
                    'form_params' => [
                        'html' => $html,
                        'border' => '5mm',
                        'footerHeight' => $options['footerHeight'] ?? '0mm',
                        'headerHeight' => $options['headerHeight'] ?? '0mm',
                    ],
                ]);
        } catch (GuzzleException $exception) {
            throw new NoPdfReturnedException($exception->getMessage());
        }

        $body = ((string)$response->getBody());

        if ($body == 'validator is invalid') {
            throw new ConnectionErrorException($body);
        }

        if (!strstr($body, '%PDF')) {
            throw new NoPdfReturnedException('Not a valid pdf');
        }

        return $body;
    }
}
