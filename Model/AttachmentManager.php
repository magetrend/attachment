<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Attachment
 * @author   E. Stulpinas <edwin@magetrend.com>
 */
namespace Magetrend\Attachment\Model;

use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;

/**
 * Class where the magic will happens
 */
class AttachmentManager
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Invoice
     */
    public $invoicePdf;

    /**
     * @var \Magento\Framework\Mail\MimePartInterfaceFactory
     */
    public $mimePartInterfaceFactory;

    /**
     * @var String|Integer
     */
    private $templateId;

    /**
     * @var array
     */
    private $templateVars = [];

    /**
     * @var array|null
     */
    private $parts = null;

    /**
     * AttachmentManager constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\Order\Pdf\Invoice $invoicePdf
     * @param \Magento\Framework\Mail\MimePartInterfaceFactory $mimePartInterfaceFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Pdf\Invoice $invoicePdf,
        \Magento\Framework\Mail\MimePartInterfaceFactory $mimePartInterfaceFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->invoicePdf = $invoicePdf;
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory;
    }

    /**
     * @param $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @param $templateVars
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
    }

    /**
     * @return int|String
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @return array
     */
    public function getTemplateVars()
    {
        return $this->templateVars;
    }

    /**
     * Reset parts registry
     */
    public function resetParts()
    {
        $this->parts = null;
    }

    /**
     * Returns attachment parts
     * @return array|null
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Add attachment part to registry
     * @param $part
     */
    public function addPart($part)
    {
        $this->parts[] = $part;
    }

    /**
     * Identify email template and create the attachments
     */
    public function collectParts()
    {
        $this->parts = [];
        $invoiceTemplateId = $this->getConfigValue(
            InvoiceIdentity::XML_PATH_EMAIL_TEMPLATE,
            $this->getStoreId()
        );

        $guestInvoiceTemplateId = $this->getConfigValue(
            InvoiceIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            $this->getStoreId()
        );

        switch ($this->getTemplateId()) {
            case $invoiceTemplateId:
            case $guestInvoiceTemplateId:
                $this->attachInvoicePDF();
                break;
        }
    }

    /**
     * Returns email store id
     * @return null|int
     */
    public function getStoreId()
    {
        $vars = $this->getTemplateVars();
        if (!isset($vars['store'])) {
            return null;
        }

        $store = $vars['store'];
        return $store->getId();
    }

    /**
     * Returns value from configuration
     * @param $path
     * @param null $store
     * @return mixed
     */
    public function getConfigValue($path, $store = null)
    {
        return $configValue = $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Loads invoice pdf and creates attachment part from it
     */
    public function attachInvoicePDF()
    {
        $vars = $this->getTemplateVars();
        $invoice = $vars['invoice'];

        $fileContent = $this->invoicePdf->getPdf([$invoice])->render();
        $fileName = 'invoice.pdf';

        $attachmentPart = $this->mimePartInterfaceFactory->create(
            [
                'content' => $fileContent,
                'type' => 'application/pdf',
                'fileName' => $fileName,
                'disposition' => \Zend\Mime\Mime::DISPOSITION_ATTACHMENT,
                'encoding' => \Zend\Mime\Mime::ENCODING_BASE64
            ]
        );

        $this->addPart($attachmentPart);
    }
}
