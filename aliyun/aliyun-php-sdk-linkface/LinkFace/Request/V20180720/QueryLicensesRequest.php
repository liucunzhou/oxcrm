<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
namespace LinkFace\Request\V20180720;

class QueryLicensesRequest extends \RpcAcsRequest
{
	function  __construct()
	{
		parent::__construct("LinkFace", "2018-07-20", "QueryLicenses");
		$this->setProtocol("https");
		$this->setMethod("POST");
	}

	private  $licenseType;

	private  $pageSize;

	private  $currentPage;

	public function getLicenseType() {
		return $this->licenseType;
	}

	public function setLicenseType($licenseType) {
		$this->licenseType = $licenseType;
		}

	public function getPageSize() {
		return $this->pageSize;
	}

	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
		}

	public function getCurrentPage() {
		return $this->currentPage;
	}

	public function setCurrentPage($currentPage) {
		$this->currentPage = $currentPage;
		}
	
}