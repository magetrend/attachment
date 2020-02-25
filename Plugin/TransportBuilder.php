<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Attachment
 * @author   E. Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Attachment\Plugin;

/**
 * Plugin class for Magento\Framework\Mail\Template\TransportBuilder
 */
class TransportBuilder
{
    /**
     * @var \Magetrend\Attachment\Model\AttachmentManager
     */
    public $attachmentManager;

    /**
     * TransportBuilder constructor.
     * @param \Magetrend\Attachment\Model\AttachmentManager $attachmentManager
     */
    public function __construct(
        \Magetrend\Attachment\Model\AttachmentManager $attachmentManager
    ) {
        $this->attachmentManager = $attachmentManager;
    }

    /**
     * Save template id to registry
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateId
     * @return array
     */
    public function beforeSetTemplateIdentifier($subject, $templateId)
    {
        $this->attachmentManager->resetParts();
        $this->attachmentManager->setTemplateId($templateId);
        return [$templateId];
    }

    /**
     * Save template variables to registry
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateVars
     * @return array
     */
    public function beforeSetTemplateVars($subject, $templateVars)
    {
        $this->attachmentManager->setTemplateVars($templateVars);
        return [$templateVars];
    }
}
