<?php
	/**
	 * User: Milan Gallas
	 * Date: 22.10.2016
	 * Time: 23:32
	 */

	namespace Ephect\Modules\DataAccess\LINQ;


	class JsonLinq extends Linq
	{
        /**
         * load json and transform to array
         *
         * @param array|string $object
         * @return $this
         */
        #[\Override]
		public function from(array|string $object): self
        {
			$this->array = json_decode($object, true);
			return $this;
		}

		/**
		 * return only elements that combine both fields
		 *
		 * @param array|string $array
		 * @return $this
		 */
		public function innerJoin(array|string $array): self
		{
			$this->secondaryArray = json_decode($array, true);
			$this->joinType = "inner";

			return $this;
		}

        /**
         * the first collection will list all entries, even those which do not connect with other collections
         *
         * @param array|string $array
         * @return $this
         */
		public function leftJoin(array|string $array): self
		{
			$this->secondaryArray = json_decode($array, true);
			$this->joinType = "left";

			return $this;
		}
	}