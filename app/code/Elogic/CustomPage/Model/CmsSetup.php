<?php

namespace Elogic\CustomPage\Model;

use Magento\Cms\Api\BlockRepositoryInterface as CmsBlockRepository;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Cms\Api\PageRepositoryInterface as CmsPageRepository;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\Data\PageInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Store\Model\Store;

/**
 * Class SetupCms.
 *
 * @package Elogic\CustomPage\Model
 */
class CmsSetup
{
    /**
     * Block factory.
     *
     * @var BlockInterfaceFactory
     */
    protected $blockFactory;

    /**
     * Block repository.
     *
     * @var CmsBlockRepository
     */
    protected $cmsBlockRepository;

    /**
     * Page factory.
     *
     * @var PageInterfaceFactory
     */
    protected $pageFactory;

    /**
     * Page repository.
     *
     * @var CmsPageRepository
     */
    protected $cmsPageRepository;

    /**
     * SampleDataContext.
     *
     * @var SampleDataContext
     */
    protected $sampleDataContext;

    /**
     * Fixture manager.
     *
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * CSV reader.
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * CmsSetup constructor.
     * @param BlockInterfaceFactory $blockFactory
     * @param CmsBlockRepository $cmsBlockRepository
     * @param PageInterfaceFactory $pageFactory
     * @param CmsPageRepository $cmsPageRepository
     * @param SampleDataContext $sampleDataContext
     */
    public function __construct(
        BlockInterfaceFactory $blockFactory,
        CmsBlockRepository $cmsBlockRepository,
        PageInterfaceFactory $pageFactory,
        CmsPageRepository $cmsPageRepository,
        SampleDataContext $sampleDataContext
    ) {
        $this->blockFactory = $blockFactory;
        $this->cmsBlockRepository = $cmsBlockRepository;
        $this->pageFactory = $pageFactory;
        $this->cmsPageRepository = $cmsPageRepository;
        $this->sampleDataContext = $sampleDataContext;
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
    }

    /**
     * Load data from CSV.
     *
     * @param string $fixture
     *
     * @return array|bool
     */
    protected function loadData($fixture)
    {
        $fileName = $this->fixtureManager->getFixture($fixture);
        if (!file_exists($fileName)) {
            $this->addExecutionMessage('File not found:' . $fileName . '.');

            return false;
        }

        return $this->csvReader->getData($fileName);
    }

    /**
     * Load blocks.
     *
     * @param string $fixture
     *
     * @return void
     * @throws \Exception
     */
    public function loadAndInstallBlock($fixture)
    {
        $rows = $this->loadData($fixture);
        array_shift($rows);
        $header = [
            BlockInterface::TITLE,
            BlockInterface::IDENTIFIER,
            BlockInterface::CONTENT,
        ];
        foreach ($rows as $row) {
            $block = [];
            foreach ($row as $key => $value) {
                $block[$header[$key]] = $value;
            }
            $this->installCmsBlock($block);
        }
    }

    /**
     * Install CMS block (create/update).
     *
     * @param [] $block
     *
     * @return void
     */
    public function installCmsBlock($block)
    {
        try {
            $cmsBlock = $this->cmsBlockRepository->getById($block[BlockInterface::IDENTIFIER]);
        } catch (NoSuchEntityException $e) {
            $cmsBlock = $this->blockFactory->create();
        }

        $cmsBlock->setIdentifier($block[BlockInterface::IDENTIFIER])
            ->setTitle($block[BlockInterface::TITLE])
            ->setContent($block[BlockInterface::CONTENT])
            ->setData('stores', Store::DEFAULT_STORE_ID);
        $this->cmsBlockRepository->save($cmsBlock);
    }

    /**
     * Load a CMS block by id.
     *
     * @param int/string $identifier
     *
     * @return \Magento\Cms\Api\Data\BlockInterface|bool
     */
    public function getCmsBlockById($identifier)
    {
        try {
            $cmsBlock = $this->cmsBlockRepository->getById($identifier);
            return $cmsBlock;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Function save cms block.
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $cmsBlock cmsBlock
     *
     * @return void
     */
    public function saveCmsBlock($cmsBlock)
    {
        /* @var $cmsBlockRepository CmsBlockRepository */
        $this->cmsBlockRepository->save($cmsBlock);
    }

    /**
     * Load pages.
     *
     * @param string $fixture
     *
     * @return void
     * @throws \Exception
     */
    public function loadAndInstallPage($fixture)
    {
        $rows = $this->loadData($fixture);
        array_shift($rows);
        $header = [
            PageInterface::TITLE,
            PageInterface::IDENTIFIER,
            PageInterface::CONTENT_HEADING,
            PageInterface::CONTENT,
            PageInterface::LAYOUT_UPDATE_XML,
            PageInterface::PAGE_LAYOUT
        ];
        foreach ($rows as $row) {
            $page = [];
            foreach ($row as $key => $value) {
                $page[$header[$key]] = $value;
            }
            $this->installCmsPage($page);
        }
    }

    /**
     * Install CMS page (create/update).
     *
     * @param [] $page
     *
     * @return void
     */
    public function installCmsPage($page)
    {
        try {
            $cmsPage = $this->cmsPageRepository->getById($page[PageInterface::IDENTIFIER]);
        } catch (NoSuchEntityException $e) {
            $cmsPage = $this->pageFactory->create();
        }

        $cmsPage->setIdentifier($page[PageInterface::IDENTIFIER])
            ->setTitle($page[PageInterface::TITLE])
            ->setContent($page[PageInterface::CONTENT])
            ->setContentHeading($page[PageInterface::CONTENT_HEADING])
            ->setLayoutUpdateXml($page[PageInterface::LAYOUT_UPDATE_XML])
            ->setPageLayout($page[PageInterface::PAGE_LAYOUT])
            ->setData('stores', [Store::DEFAULT_STORE_ID]);
        $this->cmsPageRepository->save($cmsPage);
    }

    /**
     * Load a CMS page by id.
     *
     * @param int/string $identifier
     *
     * @return \Magento\Cms\Api\Data\PageInterface|bool
     */
    public function getCmsPageById($identifier)
    {
        try {
            $cmsPage = $this->cmsPageRepository->getById($identifier);
            return $cmsPage;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Function save cms page.
     *
     * @param \Magento\Cms\Api\Data\PageInterface $cmsPage
     *
     * @return void
     */
    public function saveCmsPage($cmsPage)
    {
        /* @var $cmsPageRepository CmsPageRepository */
        $this->cmsPageRepository->save($cmsPage);
    }
}
