<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2023 Vitor Mattos <vitor@php.rio>
 *
 * @author Vitor Mattos <vitor@php.rio>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace OCA\L10nOverride\Command;

use InvalidArgumentException;
use OC\Core\Command\Base;
use OCA\L10nOverride\Service\OverrideService;
use OCP\Files\NotFoundException;
use Symfony\Component\Console\Exception\InvalidArgumentException as ConsoleExceptionInvalidArgument;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends Base {
	public function __construct(
		private OverrideService $overrideService,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->setName('l10n-override:delete')
			->setDescription('Remove overwritten translation.')
			->addArgument('theme',
				InputArgument::REQUIRED,
				'Theme code.'
			)
			->addArgument('appid',
				InputArgument::REQUIRED,
				'AppId to be removed. If you wish to override the core or lib translation, use "core" or "lib" as appid.'
			)

			->addArgument('originalText',
				InputArgument::REQUIRED,
				'Original text to remove'
			)
			->addArgument('newLanguage',
				InputArgument::REQUIRED,
				'Language to remove'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		try {
			$this->overrideService->delete(
				(string) $input->getArgument('theme'),
				(string) $input->getArgument('appid'),
				(string) $input->getArgument('originalText'),
				(string) $input->getArgument('newLanguage'),
			);
		} catch (InvalidArgumentException | NotFoundException $e) {
			// Convert to Symfony Console Exception to don't display the row number
			throw new ConsoleExceptionInvalidArgument($e->getMessage());
		}
		$output->writeln('Text removed with success.');
		return 0;
	}
}
