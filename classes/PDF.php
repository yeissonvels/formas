<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 23/02/16
 * Time: 11:04
 */

//require_once('html2pdf-4.5.0/vendor/autoload.php');
//require_once ('html2pdf-5.0.1/src/Html2Pdf.php');
//require_once ('vendor/autoload.php');
//require_once ('vendor/spipu/html2pdf/src/Html2Pdf.php');

require 'vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

class PDF extends Html2Pdf {
//class PDF extends HTML2PDF {
    protected $logowidth = 0;

    /**
     * @return mixed
     */
    public function getLogowidth()
    {
        return $this->logowidth;
    }

    /**
     * @param mixed $logowidth
     */
    public function setLogowidth($logowidth)
    {
        $this->logowidth = $logowidth;
    }
    protected $qrText = '';

    /**
     * @return HTML2PDF
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * @param HTML2PDF $pdf
     */
    public function setPdf($pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @return mixed
     */
    public function getQrText()
    {
        return $this->qrText;
    }

    /**
     * @param mixed $qrText
     */
    public function setQrText($qrText)
    {
        $this->qrText = $qrText;
    }

    protected $logo = '';

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    protected $pdfTitel;

    /**
     * @return mixed
     */
    public function getPdfTitel()
    {
        return $this->pdfTitel;
    }

    /**
     * @param mixed $pdfTitel
     */
    public function setPdfTitel($pdfTitel)
    {
        $this->pdfTitel = $pdfTitel;
    }
    /**
     * @var HTML2PDF
     */
    public $pdf;
    /**
     * @var
     */
    protected $pdfName = 'pdf';
    /**
     * @var string
     */
    protected $orientation = 'P';
    /**
     * @var
     */
    protected $pdfContent;

    /**
     * @return mixed
     */
    public function getPdfContent()
    {
        return $this->pdfContent;
    }

    /**
     * @param mixed $pdfContent
     */
    public function setPdfContent($pdfContent)
    {
        $this->pdfContent = $pdfContent;
    }

    protected $waterMark = '';

    function __construct() {
        $pdf =  new HTML2PDF($this->orientation,'A4','es');
        $this->pdf = $pdf;
    }

    /**
     * @return mixed
     */
    public function getPdfName()
    {
        return $this->pdfName;
    }

    /**
     * @param mixed $pdfName
     */
    public function setPdfName($pdfName)
    {
        $this->pdfName = $pdfName;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * @param string $orientation
     */
    public function setOrientation($orientation)
    {
        // Debemos de cambiar la orientacion de la clase padre HTML
        //$this->getPdf()->setOrientation($orientation);
        $this->getPdf()->_orientation = $orientation;
    }

    function show($mode = "") {
        $content = '';
        $content .= $this->getPdfHeader();
        $content .= $this->getPdfContent();

        $this->pdf->writeHTML($content);

        if ($mode != "") {
            global $user;
            $id = $user->getId();
            $this->pdf->Output(__DIR__ . "/../" . GENERATED_FILES_PATH . "/" . $id . "/" .$this->pdfName . '.pdf', $mode);
        } else {
            $this->pdf->Output($this->pdfName . '.pdf');
        }


    }

    function setWaterMark($text) {
        $this->waterMark = $text;
    }

    function getWaterMark() {
        return $this->waterMark;
    }

    function buildWaterMark() {
        $content = '';
        $left = "185";

        if ($this->getOrientation() == "L") {
            $left = "267";
        }

        for($i=-150; $i <= 750; $i = $i+150) {
            $content .= '<div style="rotate: 90; position: absolute; width: 100mm; height: 4mm; left: '.$left.'mm; top: '.$i.'; font-style: italic; font-weight: normal; text-align: center; font-size: 6px;">
						    ' . $this->getWaterMark() . '
					    </div>';
        }
        return $content;
    }

    function getPdfHeader() {
        $imgLogo = '';
        $logo = $this->getLogo();
        $style = "";
        if ($this->logowidth != 0) {
            $style = ' style="width: ' . $this->logowidth . 'px; "';
        }

        if ($logo != '') {
            $imgLogo = '<img src="' . $logo . '" ' . $style . '>';
        }

        $content = '
                <page backtop="7mm" backbottom="14mm" backleft="10mm" backright="10mm">
                    <table>
                        <tr>
                            <td style="width: 210px;"> ' . $imgLogo . ' </td>';
        //$content .=         '<td style="width: 410px;">'  . $this->getPdfTitel() .  '</td>';
        $content .=         '<td> ' . $this->qrCode() . ' </td>
                        </tr>
                    </table>

                    <div>
                        <h4>' . $this->getPdfTitel() . '</h4>
                    </div>

                    <page_footer>
                        <div style="font-size: 11px; margin-top: 20px; margin-left: 40px;">
                            Documento generado el ' . date('d/m/Y') . ' a las ' . date('H:i:s') . '
                        </div>
                        <div style="text-align: right;">
                            [[page_cu]]/[[page_nb]]
                        </div>
                    </page_footer>
                    <bookmark>
                    </bookmark>
                </page>';

        $content .= $this->buildWaterMark();

        return $content;
    }

    /**
     * @param $msg
     * @return string
     */
    function qrCode() {
        if ($this->getQrText() != '') {
            return '<qrcode value="'.$this->getQrText().'" ec="H" style="width: 25mm; background-color: white; color: black;"></qrcode>';
        }

        return '';
    }

}
