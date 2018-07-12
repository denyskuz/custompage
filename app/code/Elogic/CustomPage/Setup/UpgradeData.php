<?php

namespace Elogic\CustomPage\Setup;

use Elogic\CustomPage\Model\CmsSetup;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData.
 *
 * @package Elogic\CustomPage\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * SetupCms.
     *
     * @var CmsSetup
     */
    protected $setupCMS;

    /**
     * PageRepository.
     *
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * UpgradeData constructor.
     *
     * @param CmsSetup $setupCMS
     * @param PageRepository $pageRepository
     */
    public function __construct(
        CmsSetup $setupCMS,
        PageRepository $pageRepository
    ) {
        $this->setupCMS = $setupCMS;
        $this->pageRepository = $pageRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $cmsBlocksList = [
                'Elogic_CustomPage::Setup/include/custom-page.csv'
            ];

            $this->installCmsBlock($cmsBlocksList);
        }

        $setup->endSetup();
    }


    /**
     * Load and install cms block.
     *
     * @param [] $blockList
     *
     * @return void
     */
    protected function installCmsBlock($blockList)
    {
        foreach ($blockList as $blockId) {
            $this->setupCMS->loadAndInstallBlock($blockId);
        }
    }

    /**
     * Load and install cms page.
     *
     * @param [] $pageList
     *
     * @return void
     */
    protected function installCmsPage($pageList)
    {
        foreach ($pageList as $pageId) {
            $this->setupCMS->loadAndInstallPage($pageId);
        }
    }
}
