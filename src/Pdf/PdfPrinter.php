<?php

namespace Mediazz\Printer\Pdf;

use Mediazz\Printer\Pdf\Consumer\PdfGeneratorConsumer;

/**
 * This class should be handled like a Eloquent Base Model
 * All subsequent Models should only Adapt several values
 * Class PdfPrinter
 * @package Mediazz\Printer\Pdf
 */
abstract class PdfPrinter
{
    /**
     * Possibilites of the orientation
     */
    protected const ORIENTATION_LANDSCAPE = 'landscape';
    protected const ORIENTATION_PORTRAIT = 'portrait';

    protected const BORDER_BOTTOM = 'bottom';
    protected const BORDER_TOP = 'top';
    protected const BORDER_LEFT = 'left';
    protected const BORDER_RIGHT = 'right';

    /**
     * Unit possibilites for the sheet
     */
    protected const UNIT_MM = 'mm';
    protected const UNIT_PX = 'px';
    protected const UNIT_PERCENT = '%';

    /**
     * The orientation of the sheet
     *
     * @var string
     */
    protected $orientation = self::ORIENTATION_LANDSCAPE;

    /**
     * The view to use for the print
     *
     * @var string
     */
    protected $view;

    /**
     * The view data for the print
     *
     * @var array
     */
    protected $viewData = [];

    /**
     * Defines the height of the header
     *
     * @var string
     */
    protected $headerHeight = '0mm';

    /**
     * Defines the height of the footer
     *
     * @var string
     */
    protected $footerHeight = '0mm';

    /**
     * defines the border size
     *
     * @var array
     */
    protected $border = [];

    /**
     * @var PdfGeneratorConsumer
     */
    private $pdfGenerator;

    /**
     * @return PdfPrinter
     */
    public static function init(): self
    {
        return new static();
    }

    /**
     * @param string $orientation
     * @return PdfPrinter
     */
    protected function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @param int $height
     * @param string $unit
     * @return PdfPrinter
     */
    protected function setHeaderHeight(int $height, string $unit): self
    {
        $this->headerHeight = $height . $unit;

        return $this;
    }

    /**
     * @param int $height
     * @param string $unit
     * @return PdfPrinter
     */
    protected function setFooterHeight(int $height, string $unit): self
    {
        $this->footerHeight = $height . $unit;

        return $this;
    }

    /**
     * @param string $border
     * @param int $size
     * @param string $unit
     * @return PdfPrinter
     */
    protected function setBorder(string $border, int $size, string $unit): self
    {
        $this->border[$border] = $size . $unit;

        return $this;
    }

    /**
     * PdfPrinter constructor.
     */
    public function __construct()
    {
        $this->pdfGenerator = new PdfGeneratorConsumer();
    }

    /**
     * @param array $data
     * @return string
     * @throws \Mediazz\Printer\Exceptions\Pdf\ConnectionErrorException
     * @throws \Mediazz\Printer\Exceptions\Pdf\NoPdfReturnedException
     */
    public function generate(array $data = []): string
    {
        return $this->pdfGenerator->pdfByHtml(
            view($this->view, array_merge($data, $this->viewData)),
            [
                'orientation' => $this->orientation,
                'footerHeight' => $this->footerHeight,
                'headerHeight' => $this->headerHeight,
                'border' => $this->border,
            ]
        );
    }

}
