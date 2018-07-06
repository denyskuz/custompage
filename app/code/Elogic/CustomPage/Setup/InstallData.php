<?php

namespace Elogic\CustomPage\Setup;

use Elogic\Cms\Model\CmsSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData.
 *
 * @package Elogic\CustomPage\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * SetupCms.
     *
     * @var CmsSetup
     */
    protected $setupCMS;

    /**
     * InstallData constructor.
     *
     * @param CmsSetup $setupCMS
     */
    public function __construct(
        CmsSetup $setupCMS
    ) {
        $this->setupCMS = $setupCMS;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $cmsBlocks = [
            'Elogic_CustomPage::Setup/include/custom-page.csv',
            'Elogic_CustomPage::Setup/include/footer-links.csv',
        ];

        foreach ($cmsBlocks as $blockId) {
            $this->setupCMS->loadAndInstallBlock($blockId);
        }

        $setup->endSetup();
    }
}
