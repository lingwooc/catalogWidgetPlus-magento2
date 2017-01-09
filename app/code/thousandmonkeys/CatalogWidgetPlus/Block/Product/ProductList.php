<?php
namespace thousandmonkeys\CatalogWidgetPlus\Block\Product;

use Magento\Framework\App\ResourceConnection;

class ProductList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    protected $_resource;
    /**
    * @param \Magento\Catalog\Block\Product\Context $context
    * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
    * @param \Magento\Framework\App\Http\Context $httpContext
    * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
    * @param \Magento\CatalogWidget\Model\Rule $rule
    * @param \Magento\Widget\Helper\Conditions $conditionsHelper
    * @param \Magento\Framework\App\State $state
    * @param array $data
    */
    public function __construct(
    ResourceConnection $resource,
    \Magento\Catalog\Block\Product\Context $context,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    \Magento\Framework\App\Http\Context $httpContext,
    \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
    \Magento\CatalogWidget\Model\Rule $rule,
    \Magento\Widget\Helper\Conditions $conditionsHelper,
    array $data = []
    )
    {
        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder, $rule, $conditionsHelper, $data);
        
        $this->setTemplate('Magento_CatalogWidget::product/widget/content/grid.phtml');
        $this->_resource = $resource;
    }
    
    /**
    * Prepare and return product collection
    *
    * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
    */
    public function createCollection()
    {
        $collection = parent::createCollection();
        
        if($this->isBestSellerFirst()){
            $salesOrderItemTable = $this->_resource->getTableName('sales_order_item');
            $collection->getSelect()->joinLeft(
            $salesOrderItemTable,
            'e.entity_id = '.$salesOrderItemTable.'.product_id',
            array('qty_ordered'=>'SUM('.$salesOrderItemTable.'.qty_ordered)'))
            ->group('e.entity_id')
            ->order('qty_ordered desc');
        }
        return $collection;
    }
    
    /**
    * Get the configured limit of products
    * @return int
    */
    public function isBestSellerFirst() {
        if($this->getData('best_sellers')==''){
            return false;
        }
        return $this->getData('best_sellers')==1;
    }
}
?>