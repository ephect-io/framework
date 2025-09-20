<?php
	/**
	 * User: Milan Gallas
	 * Date: 22.10.2016
	 * Time: 21:44
	 */

	namespace Ephect\Modules\DataAccess\LINQ\Helper;

	interface IJoinHelper{

		/**
		 * @param $condition
		 * @return array
		 */
		public function join($condition): array;

		/**
		 * @param array $firstSource
		 * @return JoinHelper
		 */
		public function setFirstSource($firstSource): JoinHelper;


		/**
		 * @param array $secondarySource
		 * @return JoinHelper
		 */
		public function setSecondarySource($secondarySource): JoinHelper;
	}
