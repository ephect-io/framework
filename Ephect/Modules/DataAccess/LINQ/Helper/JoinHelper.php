<?php

	/**
	 * User: Milan Gallas
	 * Date: 22.10.2016
	 * Time: 21:16
	 */
	namespace Ephect\Modules\DataAccess\LINQ\Helper;

	/**
	 * Class JoinHelper
	 *
	 * @package Linq\Helper
	 */
	class JoinHelper implements IJoinHelper
	{
		/** @var array */
		protected array $firstSource;

		/** @var array */
		protected array $secondarySource;

		/** @var array */
		protected array $joinedarray;

		/**
		 * @param $condition
		 * @return array
		 */
		public function join($condition): array
        {
			if(is_callable($condition)){
				foreach($this->firstSource as $source){
					foreach($this->secondarySource as $secondarySource){
						$this->tryMergeSources($condition, $source, $secondarySource);
					}
					/*
					if($this->joinType == "left join" && $useLeftJoin){
						$joinedarray[] = $source;
					}*/
					//$useLeftJoin = true;
				}
			}
			return $this->joinedarray;
		}
		/**
		 * @param array $firstSource
		 * @return JoinHelper
		 */
		public function setFirstSource($firstSource): JoinHelper
        {
			$this->firstSource = $firstSource;

			return $this;
		}

		/**
		 * @param array $secondarySource
		 * @return JoinHelper
		 */
		public function setSecondarySource($secondarySource): JoinHelper
        {
			$this->secondarySource = $secondarySource;

			return $this;
		}

		/**
		 * @param $condition
		 * @param $source
		 * @param $secondarySource
		 */
		protected function tryMergeSources($condition, $source, $secondarySource): void
        {
 			if (call_user_func_array($condition, array($source, $secondarySource))) {
				$this->mergeSources($source, $secondarySource);
			}
		}

		/**
		 * @param $source
		 * @param $secondarySource
		 */
		protected function mergeSources($source, $secondarySource): void
        {
			if (is_array($source) and is_array($secondarySource)) {
				$this->joinedarray[] = array_merge_recursive($source, $secondarySource);
			} else {
				$this->joinedarray[] = (object)array_merge_recursive((array)$source, (array)$secondarySource);
			}
		}

	}