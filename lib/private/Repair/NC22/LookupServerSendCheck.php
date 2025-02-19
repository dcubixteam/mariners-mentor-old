<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2021 Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OC\Repair\NC22;

use OC\Core\BackgroundJobs\LookupServerSendCheckBackgroundJob;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class LookupServerSendCheck implements IRepairStep {

	/** @var IJobList */
	private $jobList;

	/** @var IConfig */
	private $config;

	public function __construct(IJobList $jobList, IConfig $config) {
		$this->jobList = $jobList;
		$this->config = $config;
	}

	public function getName(): string {
		return 'Add background job to set the lookup server share state for users';
	}

	private function shouldRun(): bool {
		$versionFromBeforeUpdate = $this->config->getSystemValue('version', '0.0.0.0');

		// was added to 22.0.0.3
		return version_compare($versionFromBeforeUpdate, '20.0.9.2', '<') &&
			version_compare($versionFromBeforeUpdate, '20.0.0.0', '>');
	}

	public function run(IOutput $output): void {
		$this->jobList->add(LookupServerSendCheckBackgroundJob::class);
	}
}
