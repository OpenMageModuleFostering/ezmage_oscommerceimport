<?php
class Ezmage_OscommerceImport_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {						
		
		Mage::register('image_status1','check-no.jpg');
		Mage::register('image_status2','check-no.jpg');
		Mage::register('image_status3','check-no.jpg');
		Mage::register('image_status4','check-no.jpg');
		Mage::register('image_status5','check-no.jpg');
		Mage::register('image_status6','check-no.jpg');
														
		
		//check if configuration has values
		$conf_hostname 			= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_hostname',Mage::app()->getStore());
		$conf_port 				= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_port',Mage::app()->getStore());
		$conf_db 				= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_db',Mage::app()->getStore());
		$conf_db_username 		= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_username',Mage::app()->getStore());
		$conf_db_password 		= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_password',Mage::app()->getStore());
		$conf_table_prefix 		= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_prefix',Mage::app()->getStore());
		$conf_website 			= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_website',Mage::app()->getStore());
		$conf_category 			= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_category',Mage::app()->getStore());
		$conf_attribute 		= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_attribute',Mage::app()->getStore());
		

			
		if ( ($conf_hostname != "") and ($conf_db != "") and ($conf_db_username != "") and ($conf_db_password != "")){		
			Mage::register('conf_hostname',$conf_hostname);
			Mage::register('conf_port',$conf_port);
			Mage::register('conf_db',$conf_db);
			Mage::register('conf_db_username',$conf_db);
			Mage::register('conf_db_password',$conf_db_password);
			Mage::register('conf_table_prefix',$conf_table_prefix);
			Mage::register('conf_website',$conf_website);	
			Mage::register('conf_category',$conf_category);	
			Mage::register('conf_attribute',$conf_attribute);
			
			Mage::unregister('image_status1');
			Mage::register('image_status1','check-ok.jpg');												
		} 
		
		// step 2
		$step2Status=Mage::getSingleton('core/session')->getstep2Status();
		if ($step2Status == 'ok'){
			Mage::unregister('image_status2');
			Mage::register('image_status2','check-ok.jpg');
		}
		
		//step3
		$importCategoryTotal = Mage::getSingleton('core/session')->getimportCategoryTotal();
		$importProductsTotal = Mage::getSingleton('core/session')->getimportProductsTotal();
		if (($importCategoryTotal > 0) or ($importProductsTotal > 0)){
			Mage::unregister('image_status3');
			Mage::register('image_status3','check-ok.jpg');
		}
		
		// step4
		$importCategoryTotal = Mage::getSingleton('core/session')->getimportCategoryTotal();
		$importedCategoryTotal = Mage::getSingleton('core/session')->getimportedCategoryTotal();
		if ( ($importCategoryTotal == $importedCategoryTotal) and ($importCategoryTotal > 0))  {
			Mage::unregister('image_status4');
			Mage::register('image_status4','check-ok.jpg');
		}
			
		// step4
		$importProductsTotal = Mage::getSingleton('core/session')->getimportProductsTotal();
		$importedProductsTotal = Mage::getSingleton('core/session')->getimportedProductsTotal();
		if (($importProductsTotal == $importedProductsTotal) and ($importProductsTotal > 0)) {
			Mage::unregister('image_status5');
			Mage::register('image_status5','check-ok.jpg');
		}
								
		// Layout Out Put			
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step1',
			array('template' => 'ezmage/oscommerceimport/step0.phtml')
		);
    	
    	$this->getLayout()->getBlock('content')->append($block);
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');			
			
		$this->renderLayout();
    }
	
	/*******************************************************************************/
	/* Step 1			 														   */
	/*******************************************************************************/
    public function step1Action()
    {
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step1',
			array('template' => 'ezmage/oscommerceimport/step1.phtml')
		);
    	
    	$this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }
	
	/*******************************************************************************/
	/* Step 2			 														   */
	/*******************************************************************************/
    public function step2Action()
    {
		// check if tmp are created
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
	 	$writeConnection = $resource->getConnection('core_write');
	 
		$query = 'show tables like "ezmage_categories"';
		$list = $readConnection->fetchAll($query);
		$ezmage_categories = sizeof($list);
		// create table if don't exit
		if ($ezmage_categories == 0){
			$query = "CREATE TABLE IF NOT EXISTS `ezmage_categories` (`osc_cat_id` int(11) NOT NULL,`osc_cat_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,`osc_cat_parent` int(11) NOT NULL,`mage_cat_id` int(11) NOT NULL,`mage_cat_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,`mage_cat_parent` int(11) NOT NULL,`cat_imported` varchar(1) COLLATE utf8_unicode_ci NOT NULL,`osc_cat_image` varchar(200) COLLATE utf8_unicode_ci NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	    	$writeConnection->query($query);
		}
		
		$query = 'show tables like "ezmage_products"';
		$list = $readConnection->fetchAll($query);
		$ezmage_products = sizeof($list);
		// create table if don't exit
		if ($ezmage_products == 0){
			$query = "CREATE TABLE IF NOT EXISTS `ezmage_products` (`osc_product_id` int(11) NOT NULL,`osc_product_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,`mage_product_id` int(11) NOT NULL,`product_imported` varchar(1) COLLATE utf8_unicode_ci NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	    $writeConnection->query($query);
		}

		$query = 'show tables like "ezmage_log"';
		$list = $readConnection->fetchAll($query);
		$ezmage_log = sizeof($list);
		if ($ezmage_log == 0){
			$query = "CREATE TABLE IF NOT EXISTS `ezmage_log` (`importtime` datetime NOT NULL,`log_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,`log_desc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,`mage_log` varchar(255) COLLATE utf8_unicode_ci NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	    	$writeConnection->query($query);
		}
				
		if ( ($ezmage_categories == 1) and ($ezmage_products == 1) and ($ezmage_log == 1) ){
			$step2Status = 'ok';
			Mage::getSingleton('core/session')->setstep2Status($step2Status);
											
		}
		
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step2',
			array('template' => 'ezmage/oscommerceimport/step2.phtml')
		);
    	
    	$this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }
	
	
	/*******************************************************************************/
	/* Step 2.1																			 														   */
	/*******************************************************************************/
    public function step21Action()
    {										
			
			try{
				// check if tmp are created
				$resource = Mage::getSingleton('core/resource');
				$writeConnection = $resource->getConnection('core_write');
				// Remove tmp tables
				$query = 'TRUNCATE TABLE ezmage_categories';
				$writeConnection->query($query);
				$query = 'TRUNCATE TABLE ezmage_log';
				$writeConnection->query($query);
				$query = 'TRUNCATE TABLE ezmage_products';
				$writeConnection->query($query);
						
			}		
			catch (Exception $ex) {			
				echo $ex->getMessage();
			}										
			
			Mage::register('truncate_tables','yes');
			Mage::getSingleton('core/session')->unsimportCategoryTotal();
			Mage::getSingleton('core/session')->unsimportProductsTotal();
			Mage::getSingleton('core/session')->unsimportCategoryTotal();
			Mage::getSingleton('core/session')->unsimportedCategoryTotal();
			Mage::getSingleton('core/session')->unsimportProductsTotal();
			Mage::getSingleton('core/session')->unsimportedProductsTotal();
														
			$this->loadLayout();
			
				$block = $this->getLayout()->createBlock(
					'Mage_Core_Block_Template',
					'block_ezmage_oscommerce_import_step4',
				array('template' => 'ezmage/oscommerceimport/step2.phtml')
			);
				
			$this->getLayout()->getBlock('content')->append($block);			
			$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
						
			$this->renderLayout();
    }	
		
			
	/*******************************************************************************/
	/* Step 3			 														   */
	/*******************************************************************************/
    public function step3Action()
    {
		
		$_config = $this->setRemoteConectionConfig();
		try {
			$_connection = Mage::getSingleton('core/resource')->createConnection('oscommerce_conection', 'pdo_mysql', $_config);
			$query = 'show tables like "ezmage_log"';
			$list = $_connection->fetchAll($query);
			Mage::register('conection_status',"ok");
		}
		catch (Exception $ex) {
			 Mage::register('conection_status',$ex->getMessage());
		}
		
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step3',
			array('template' => 'ezmage/oscommerceimport/step3.phtml')
		);
    	
    	$this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }
	
	/*******************************************************************************/
	/* Step 3.1			 														   */
	/*******************************************************************************/
    public function step31Action()
    {
			
		$_config = $this->setRemoteConectionConfig();
		try {
				
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$writeConnection = $resource->getConnection('core_write');
							
			$_connection_remote = Mage::getSingleton('core/resource')->createConnection('oscommerce_conection', 'pdo_mysql', $_config);
			
			$conf_prefix	= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_prefix',Mage::app()->getStore());
			
			$query = 'select categories.categories_id ,categories.categories_image, categories.parent_id, categories_description.categories_name from categories LEFT JOIN categories_description on categories_description.categories_id = categories.categories_id and categories_description.language_id = 1';
			$results  = $_connection_remote->fetchAll($query);
			foreach($results as $row) {
				// check if category exist
				$sql = "select osc_cat_id from ezmage_categories where osc_cat_id=".$row['categories_id'];
				$list = $readConnection->fetchAll($sql);
				if (sizeof($list) == 0){
					$row['categories_name'] = str_replace('"',"",$row['categories_name']);
					$sql = 'insert into ezmage_categories (osc_cat_id,osc_cat_title,osc_cat_parent,osc_cat_image) values('.$row['categories_id'].',"'.$row['categories_name'].'","'.$row['parent_id'].'","'.$row['categories_image'].'")';
					$writeConnection->query($sql);				
				}
			}
			
			
			$sql = "select osc_cat_id from ezmage_categories";
			$list = $readConnection->fetchAll($sql);
			$importCategoryTotal = sizeof($list);			
			Mage::getSingleton('core/session')->setimportCategoryTotal($importCategoryTotal);
			
			// products
			$query = 'select products_id,products_name from products_description where language_id = 1';
			$results  = $_connection_remote->fetchAll($query);
			foreach($results as $row) {
				// check if category exist
				$sql = "select osc_product_id from ezmage_products where osc_product_id=".$row['products_id'];
				$list = $readConnection->fetchAll($sql);
				if (sizeof($list) == 0){
					$row['products_name'] = str_replace('"',"",$row['products_name']);
					$sql = 'insert into ezmage_products values('.$row['products_id'].',"'.$row['products_name'].'","","")';
					$writeConnection->query($sql);				
				}
			}	
			
			$sql = "select osc_product_id from ezmage_products";
			$list = $readConnection->fetchAll($sql);
			$importProductsTotal = sizeof($list);			
			Mage::getSingleton('core/session')->setimportProductsTotal($importProductsTotal);
								
			
		}
		catch (Exception $ex) {
			//Mage::register('conection_status',$ex->getMessage());
			echo $ex->getMessage();
		}
		
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step31',
			array('template' => 'ezmage/oscommerceimport/step31.phtml')
		);
    	
    	$this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }
	
	
	/*******************************************************************************/
	/* Step 4			 														   */
	/*******************************************************************************/
    public function step4Action()
    {				
		try {			
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
										
			$sql = "select osc_cat_id from ezmage_categories where cat_imported='y'";
			$list = $readConnection->fetchAll($sql);
			$importedCategoryTotal = sizeof($list);		
			Mage::getSingleton('core/session')->setimportedCategoryTotal($importedCategoryTotal);											
		}
		catch (Exception $ex) {
			//Mage::register('conection_status',$ex->getMessage());
			echo $ex->getMessage();
		}										
				
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step4',
			array('template' => 'ezmage/oscommerceimport/step4.phtml')
		);
    	
    	$this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }	
	
	
	/*******************************************************************************/
	/* Step 4.1			 														   */
	/*******************************************************************************/
    public function step41Action()
    {			
						
		// Stop Indexes     
		// http://www.clounce.com/magento/magento-reindex-programmatically
		// http://stackoverflow.com/questions/5420552/magento-programmatically-disable-automatic-indexing
		$pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
		foreach ($pCollection as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
		}
		
		$totalimport = 0;
		
		try {			
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$writeConnection = $resource->getConnection('core_write');
			
			$storeId = Mage::getStoreConfig('oscommerceimportconf/mageconfiguration/conf_website',Mage::app()->getStore());

			$categories_array = array();
			$this->tep_get_subcategories(&$categories_array, 0, $readConnection);
			foreach($categories_array as $category_row){
				if ($category_row['cat_imported'] != 'y'){
					//print $category_row['osc_cat_id'].' => '.$category_row['osc_cat_title'].' => '.$category_row['osc_cat_parent'].'<br>';				
					$category = Mage::getModel( 'catalog/category' );
					$category->setStoreId( $storeId );
					$category->setName($category_row['osc_cat_title']);
					$category->setIsActive(1);
					$category->setIsAnchor(0);
					$category->setDisplayMode('PRODUCTS');
					$category->setIncludeInMenu(0);
					
					if ($category_row['osc_cat_parent'] != 0){
						$parent_id = $this->getMageParentID($category_row['osc_cat_parent'],$readConnection);		
						$parent = Mage::getModel('catalog/category')->load($parent_id);	
					}else{
						$parent = Mage::getModel('catalog/category')->load(Mage::getStoreConfig('oscommerceimportconf/mageconfiguration/conf_category',Mage::app()->getStore()));	
					} 
					$category->setPath( $parent->getPath() ); 
					
					$path = $this->getDownloadImage("category",$category_row['osc_cat_image']);
					if ($category_row['osc_cat_image'] != ""){
						$data['thumbnail'] = $path;
						$category->addData($data);  
					}
									
					$category->save();
									
					// update tmp table
					$sql = "update ezmage_categories set cat_imported='y',mage_cat_id=".$category->getId().", mage_cat_parent=".$parent->getId()." where osc_cat_id=".$category_row['osc_cat_id'];				
					$writeConnection->query($sql);	
					
					$totalimport++;
					if ($totalimport == 10){
						break;
					}	
				}
			}  
									
						
			// get import status							
			$sql = "select osc_cat_id from ezmage_categories where cat_imported='y'";
			$list = $readConnection->fetchAll($sql);
			$importedCategoryTotal = sizeof($list);		
			Mage::getSingleton('core/session')->setimportedCategoryTotal($importedCategoryTotal);	
												
		}
		catch (Exception $ex) {
			//Mage::register('conection_status',$ex->getMessage());
			echo $ex->getMessage();
		}										
				
		// Start Indexes		
		foreach ($pCollection as $process) {
		  $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
		}
													
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step4',
			array('template' => 'ezmage/oscommerceimport/step4.phtml')
		);
    	
    $this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();		
    }	
	

	/*******************************************************************************/
	/* Step 5			 														   */
	/*******************************************************************************/
    public function step5Action()
    {				
		try {			
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
										
			$sql = "select osc_product_id from ezmage_products where product_imported='y'";
			$list = $readConnection->fetchAll($sql);
			$importedProductsTotal = sizeof($list);		
			Mage::getSingleton('core/session')->setimportedProductsTotal($importedProductsTotal);											
		}
		catch (Exception $ex) {
			//Mage::register('conection_status',$ex->getMessage());
			echo $ex->getMessage();
		}										
				
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step4',
			array('template' => 'ezmage/oscommerceimport/step5.phtml')
		);
    	
    $this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }	
	
	/*******************************************************************************/
	/* Step 5.1			 														   */
	/*******************************************************************************/
    public function step51Action()
    {				
		
		$_config = $this->setRemoteConectionConfig();
		// Stop Indexes     
		// http://www.clounce.com/magento/magento-reindex-programmatically
		// http://stackoverflow.com/questions/5420552/magento-programmatically-disable-automatic-indexing
		$pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
		foreach ($pCollection as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
		}
				
		try {			
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$writeConnection = $resource->getConnection('core_write');
			$_connection_remote = Mage::getSingleton('core/resource')->createConnection('oscommerce_conection', 'pdo_mysql', $_config);
						
			$storeId = Mage::getStoreConfig('oscommerceimportconf/mageconfiguration/conf_website',Mage::app()->getStore());
			$AttributeSetId = Mage::getStoreConfig('oscommerceimportconf/mageconfiguration/conf_attribute',Mage::app()->getStore());
			
			$sql = "select * from ezmage_products where product_imported<>'y'";
			$results = $readConnection->fetchAll($sql);

			foreach($results as $row) {
				//print $row['osc_product_id'].' => '.$row['osc_product_name'].' => '.$row['mage_product_id'].'<br>';				
				
				$product_osc = $this->getProductFromOSC($row['osc_product_id'],$readConnection,$_connection_remote);
				$product_categories_osc = $this->getCategoriesProductFromOSC($row['osc_product_id'],$readConnection,$_connection_remote);		
				
				// some validation
				if ($product_osc['products_model'] == '') {
					$product_osc['products_model'] = $row['products_id'];
				}
				
				//search if sku exits
				$oProduct = Mage::getModel("catalog/product")
                ->getCollection()
                ->setStoreId($storeId)
                ->addAttributeToSelect("sku")
                ->addFieldToFilter("sku", array('eq' => $product_osc['products_model']))                        
                ->getFirstItem();
				
				if ( sizeof($oProduct->getData()) > 0){
					$product_osc['products_model'] = $product_osc['products_model'].rand(1111,9999);
				}
				

				//$product = Mage::getModel('catalog/product');
				$product = new Mage_Catalog_Model_Product();
				 
				// Build the product
				$product->setSku($product_osc['products_model']);
				$product->setAttributeSetId($AttributeSetId); 
				$product->setTypeId('simple');
				$product->setName($product_osc['products_name']);
				$product->setCategoryIds($product_categories_osc); 
				$product->setWebsiteIDs(array($storeId)); 
				$product->setDescription($product_osc['products_description']);
				$product->setShortDescription('-');
				$product->setPrice($product_osc['products_price']);  			 
				$product->setWeight($product_osc['products_weight']);
				 
				$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
				$product->setStatus($product_osc['products_status']);
				$product->setTaxClassId(0); # My default tax class
				$product->setStockData(array(
					'is_in_stock' => 1,
					'qty' => $product_osc['products_quantity']
				));
				 
				$product->setCreatedAt(strtotime('now'));
				
				if ($product_osc['products_image'] != ''){
					$image_location = $this->getDownloadImage("product",$product_osc['products_image']);				
					if ( file_exists($image_location) ) {
						$product->addImageToMediaGallery($image_location,array('thumbnail','small_image','image'),true,false);
					}
				}
														 
				$product->save();
																								
				// update tmp table
				$sql = "update ezmage_products set product_imported='y',mage_product_id=".$product->getId()." where osc_product_id=".$row['osc_product_id'];				
				$writeConnection->query($sql);	
				
				$totalimport++;
				if ($totalimport == Mage::getStoreConfig('oscommerceimportconf/mageconfiguration/conf_totalperimport',Mage::app()->getStore())){
					break;
				}				
			}  									
						
			// get import status							
			$sql = "select osc_product_id from ezmage_products where product_imported='y'";
			$list = $readConnection->fetchAll($sql);
			$importedProductsTotal = sizeof($list);		
			Mage::getSingleton('core/session')->setimportedProductsTotal($importedProductsTotal);	
												
		}
		catch (Exception $ex) {
			//Mage::register('conection_status',$ex->getMessage());
			echo $row['osc_product_id'].' - '.$ex->getMessage();
		}										
		
		
		// Start Indexes		
		foreach ($pCollection as $process) {
		  $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
		}
								
		// Layaout		
		$this->loadLayout();
		
    	$block = $this->getLayout()->createBlock(
		    'Mage_Core_Block_Template',
		    'block_ezmage_oscommerce_import_step4',
			array('template' => 'ezmage/oscommerceimport/step5.phtml')
		);
    	
    $this->getLayout()->getBlock('content')->append($block);			
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					
		$this->renderLayout();
    }
		
		

	/*******************************************************************************/
	/* Step 6																				 														   */
	/*******************************************************************************/
    public function step6Action()
    {																
			$this->loadLayout();
			
				$block = $this->getLayout()->createBlock(
					'Mage_Core_Block_Template',
					'block_ezmage_oscommerce_import_step4',
				array('template' => 'ezmage/oscommerceimport/step6.phtml')
			);
				
			$this->getLayout()->getBlock('content')->append($block);			
			$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
						
			$this->renderLayout();
    }	

	/*******************************************************************************/
	/* Step 6.1																			 														   */
	/*******************************************************************************/
    public function step61Action()
    {										
			
			try{
				// check if tmp are created
				$resource = Mage::getSingleton('core/resource');
				$writeConnection = $resource->getConnection('core_write');
				// Remove tmp tables
				$query = 'drop table ezmage_categories';
				$writeConnection->query($query);
				$query = 'drop table ezmage_log';
				$writeConnection->query($query);
				$query = 'drop table ezmage_products';
				$writeConnection->query($query);
						
			}		
			catch (Exception $ex) {			
				echo $ex->getMessage();
			}										
					
			// unregister variables
			Mage::getSingleton('core/session')->unsstep2Status();
			Mage::getSingleton('core/session')->unsimportCategoryTotal();
			Mage::getSingleton('core/session')->unsimportProductsTotal();
			Mage::getSingleton('core/session')->unsimportCategoryTotal();
			Mage::getSingleton('core/session')->unsimportedCategoryTotal();
			Mage::getSingleton('core/session')->unsimportProductsTotal();
			Mage::getSingleton('core/session')->unsimportedProductsTotal();
						
			$this->loadLayout();
			
				$block = $this->getLayout()->createBlock(
					'Mage_Core_Block_Template',
					'block_ezmage_oscommerce_import_step4',
				array('template' => 'ezmage/oscommerceimport/step6.phtml')
			);
				
			$this->getLayout()->getBlock('content')->append($block);			
			$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
						
			$this->renderLayout();
    }	

	
			
	/***************************************************************************************************************/
	// Utils 
	/***************************************************************************************************************/

	// Get product Information from oscommerce
	public function getProductFromOSC($osc_product_id,$readConnection,$_connection_remote){
			$query = 'select products_description.*,products.* from products left join products_description on products.products_id=products_description.products_id where  products.products_id = '.$osc_product_id.' and  products_description.language_id = 1';
			$results  = $_connection_remote->fetchAll($query);			
			if (sizeof($results) == 1){
				$product = $results[0];
			}else{
				$product['products_id'] = 0;
			}			
		return $product;
	}
	
	// Returns Mage Categories			
	public function getCategoriesProductFromOSC($osc_product_id,$readConnection,$_connection_remote){
		$query = 'select categories_id from products_to_categories where products_id = '.$osc_product_id;
		$results  = $_connection_remote->fetchAll($query);	
		foreach($results as $row) {
			// find magento category id 			
		 $sql = "select mage_cat_id from ezmage_categories where osc_cat_id=".$row['categories_id'];
		 $mage_cat_id = $readConnection->fetchOne($sql);			
		 if ($mage_cat_id > 0){
		 		$product_categories[] = $mage_cat_id;
		 }
		}						
		if (sizeof($product_categories) == 0){
			$product_categories[] = Mage::getModel('catalog/category')->load(Mage::getStoreConfig('oscommerceimportconf/mageconfiguration/conf_category',Mage::app()->getStore()));
		}		 
		return $product_categories;
	}
				
				
	// Download Image
	public function  getDownloadImage($type,$file){
		$path = str_replace("index.php","",$_SERVER["SCRIPT_FILENAME"]);		
		$import_location = $path.'media/catalog/';
		if (!file_exists($import_location)){
			mkdir($import_location, 0755);
		}
		$import_location = $path.'media/catalog/'.$type.'/';
		if (!file_exists($import_location)){
			mkdir($import_location, 0755);
		}
		
		// todo check if last character has /
		
		$file_source = Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_imageurl',Mage::app()->getStore()).$file;
		$file_target = $import_location."/".basename($file);
						
		$file_path = "";
		if (($file != '') and (!file_exists($file_target))){
			$rh = fopen($file_source, 'rb');
			$wh = fopen($file_target, 'wb');
			if ($rh===false || $wh===false) {
				// error reading or opening file
				$file_path = "";
			}
			while (!feof($rh)) {
				if (fwrite($wh, fread($rh, 1024)) === FALSE) {
					$file_path = $file_target;
				}
			}
			fclose($rh);
			fclose($wh);
		}
		if (file_exists($file_target)){
			if ($type == 'category'){
				$file_path = $file;
			}else{
				$file_path = $file_target;
			}			
		}
				
		return $file_path;
	}
	
	// Generate mage parent id
	public function getMageParentID($osc_cat_parent,$readConnection) {		
		$sql = "select mage_cat_id from ezmage_categories where osc_cat_id=".(int)$osc_cat_parent;
		$mage_cat_id = $readConnection->fetchOne($sql);
		return $mage_cat_id;
	}
		
	// Generate category tree
	public function tep_get_subcategories(&$categories_array = '', $parent_id = '0',$readConnection) {
		$languages_id = 1;		
		if (!is_array($categories_array)) $categories_array = array();
		$sql = "select * from ezmage_categories where osc_cat_parent=".(int)$parent_id;
		$results = $readConnection->fetchAll($sql);
		foreach($results as $row) {
			$counter = count($categories_array);
			$categories_array[$counter]['osc_cat_id'] = $row['osc_cat_id'];
			$categories_array[$counter]['osc_cat_title'] = $row['osc_cat_title'];
			$categories_array[$counter]['osc_cat_parent'] = $row['osc_cat_parent'];
			$categories_array[$counter]['osc_cat_image'] = $row['osc_cat_image'];		
			$categories_array[$counter]['cat_imported'] = $row['cat_imported'];
								
			if ($row['osc_cat_id'] != $parent_id) {
				$categories_array = $this->tep_get_subcategories(&$categories_array, $row['osc_cat_id'],$readConnection);
	  		}
			
		}
		return $categories_array;
	}
  
  	
	// Save Log
	public function saveException($log_type,$log_desc,$mage_log){					
	}
	
	// Set remote conection
	public function setRemoteConectionConfig(){
	// Conect to remote db
		$_config = array();
		$_config['host'] = Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_hostname',Mage::app()->getStore());
		if (Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_port',Mage::app()->getStore()) != ''){
			$_config['port'] = Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_port',Mage::app()->getStore());
		}
		$_config['dbname'] = Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_db',Mage::app()->getStore());
		$_config['username'] = Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_username',Mage::app()->getStore());
		$_config['password'] = Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_password',Mage::app()->getStore());
		$_config_prefix 		= Mage::getStoreConfig('oscommerceimportconf/oscconfiguration/conf_prefix',Mage::app()->getStore());						
		// Setting the default Values
		$_config['initStatements'] = 'SET NAMES utf8';
		$_config['model'] = 'mysql4';
		$_config['type'] = 'pdo_mysql';
		$_config['pdoType'] = '';
		$_config['active'] = '1';
		
		return $_config;
	}
	
	
}