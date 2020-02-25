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
 * Plugin class for Magento\Framework\Mail\MimeMessage
 */
class MimeMessage
{
    /**
     * @var \Magetrend\Attachment\Model\AttachmentManager
     */
    public $attachmentManager;

    /**
     * MimeMessage constructor.
     * @param \Magetrend\Attachment\Model\AttachmentManager $attachmentManager
     */
    public function __construct(
        \Magetrend\Attachment\Model\AttachmentManager $attachmentManager
    ) {
        $this->attachmentManager = $attachmentManager;
    }

    /**
     * Add attachment part in the end of email parts
     * @param $subject
     * @param $parts
     * @return array
     */
    public function afterGetParts($subject, $parts)
    {
        if (!empty($parts) && $this->attachmentManager->getParts() === null) {
            $this->attachmentManager->collectParts();
            $additionalParts = $this->attachmentManager->getParts();
            if (!empty($additionalParts)) {
                foreach ($additionalParts as $aPart) {
                    $parts[] = $aPart;
                }
            }
        }

        return $parts;
    }
}
