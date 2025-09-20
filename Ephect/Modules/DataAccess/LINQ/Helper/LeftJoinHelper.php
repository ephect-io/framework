<?php
	/**
	 * User: Milan Gallas
	 * Date: 22.10.2016
	 * Time: 21:36
	 */

	namespace Ephect\Modules\DataAccess\LINQ\Helper;

	/**
	 * Class LeftJoinHelper
	 *
	 * @package Linq\Helper
	 */
	class LeftJoinHelper extends JoinHelper
	{
		/** @var boolean */
		protected bool $tryLeftJoin = true;

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

					if($this->tryLeftJoin){
						$this->joinedarray[] = $source;
					}
					$this->tryLeftJoin = true;
				}
			}
			return $this->joinedarray;
		}

		/**
		 * @param $source
		 * @param $secondarySource
		 */
		protected function mergeSources($source, $secondarySource): void
        {
			parent::mergeSources($source, $secondarySource);
			$this->tryLeftJoin = false;
		}


	}