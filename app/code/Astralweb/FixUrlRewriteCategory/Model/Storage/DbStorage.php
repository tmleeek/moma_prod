<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Astralweb\FixUrlRewriteCategory\Model\Storage;
use Magento\Framework\App\ResourceConnection;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\Framework\Api\DataObjectHelper;

class DbStorage extends \Magento\UrlRewrite\Model\Storage\DbStorage
{
protected function doReplace($urls)
    {
        foreach ($this->createFilterDataBasedOnUrls($urls) as $type => $urlData) {
            $urlData[UrlRewrite::ENTITY_TYPE] = $type;
            $this->deleteByData($urlData);
        }
        $data = [];
       $storeId_requestPaths = [];
    foreach ($urls as $url) {
        $storeId = $url->getStoreId();
        $requestPath = $url->getRequestPath();
        // Skip if is exist in the database
        $sql = "SELECT * FROM url_rewrite where store_id = $storeId and request_path = '$requestPath'";
        $exists = $this->connection->fetchOne($sql);

        if ($exists) continue;

        $storeId_requestPaths[] = $storeId . '-' . $requestPath;
        $data[] = $url->toArray();
    }

    // Remove duplication data;
    $n = count($storeId_requestPaths);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = $i + 1; $j < $n; $j++) {
            if ($storeId_requestPaths[$i] == $storeId_requestPaths[$j]) {
                unset($data[$j]);
            }
        }
    }
    $this->insertMultiple($data);
    }

}