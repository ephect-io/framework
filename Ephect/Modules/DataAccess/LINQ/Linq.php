<?php
	/**
	 * User: Milan Gallas
	 * Date: 6.4.2016
	 * Time: 8:57
	 */

	namespace Ephect\Modules\DataAccess\LINQ;

	use Ephect\Modules\DataAccess\LINQ\Factory\JoinFactory;

    /**
	 * Class Linq
	 *
	 * easy and fast tranform, filter, sort
	 *
	 * @package lib\utils\linq
	 */
	class Linq
	{
		/** @var JoinFactory  */
		protected JoinFactory $joinFactory;

		/**
		 * Linq constructor.
		 *
		 * @param JoinFactory $joinFactory
		 */
		public function __construct(JoinFactory $joinFactory)
		{
			$this->joinFactory = $joinFactory;
		}

		/**
		 * @var array primary source
		 */
		protected array $array;

		/**
		 * @var array secondary source from join methods
		 */
		protected array $secondaryArray;

		/**
		 * @var string type of join.
		 */
		protected string $joinType;

		/**
		 * set primary source
		 *
		 * @param array|string $object
		 * @return $this
		 */
        public function from(array|string $object): self
		{
			$this->array = $object;

			return $this;
		}

		/**
		 * return first item from collection
		 *
		 * @return mixed
		 */
		public function first(): mixed
		{
			return $this->array[0];
		}

		/**
		 * return last item from collection
		 *
		 * @return mixed
		 */
		public function last(): mixed
		{
			return $this->array[count($this->array) - 1];
		}

		/**
		 * return result. You can use callable as first param
		 *
		 * @param callable|string|null $key       - name of key || callable
		 * @param null $key2 - ame of value. You can use as fetchPairs in dibi
		 *                   sloupec
		 * @return array
		 */
		public function select(callable|string|null $key = null, ?string $key2 = null): mixed
		{
			//call without parameters
			if (!$key) {
				return $this->array;
			}
			//first param is callable
			if(is_callable($key)){
				$array = $this->array;
				return array_map($key, $array);
			}

			else {
				//both params used
				if ($key2) {
					$firstKey = array_keys($this->array)[0];
					if (is_object($this->array[$firstKey])) {
						return array_map(function ($item) {
							return (object)$item;
						}, $this->arrayColumnToObject($key2, $key));
					}
						return array_column($this->array, $key2, $key);
				}
				//only first param
				else {
					$firstKey = array_keys($this->array)[0];
					if (is_object($this->array[$firstKey])) {
						return array_map(function ($item) {
							return (object)$item;
						}, $this->arrayColumnToObject($key, $key2));
					}
					return array_column($this->array, $key, null);
				}
			}
		}

		/**
		 * use for collection of objects
		 *
		 * @param mixed $key2
		 * @param mixed $key
		 * @return array
		 */
		protected function arrayColumnToObject(mixed $key2, mixed $key): array
		{
			return array_column(array_map(function ($item) {
				return (array)$item;
			}, $this->array), $key2, $key);
		}

		/**
		 * return collection´s length
		 *
		 * @return int
		 */
		public function count(): int
		{
			return count($this->array);
		}

		/**
		 * return first x items || return interval
		 * @example take(10, 20) -> BETWEEN 11 AND 21 (in sql language)
		 *
		 * @param int $offset
		 * @param int|null $length
		 * @return array
		 */
		public function take(int $offset, ?int $length = null): array
		{
			if (!$length) {
				return array_slice($this->array, 0, $offset);
			}

			return array_slice($this->array, $offset, $length);
		}

		/**
		 * skip x items a and return other data
		 *
		 * @param int $offset
		 * @return array
		 */
		public function skip(int $offset): array
		{
			$array = [];
			$i = 0;
			foreach ($this->array as $key => $value) {
				if ($i >= $offset) {
					$array[$key] = $value;
				}
				$i++;
			}

			return $array;
		}

		/**
		 * returns only elements that match a given condition
		 *
		 * @param $condition callable
		 * @return $this
		 * @throws \Exception
		 */
		public function where(callable $condition): self
		{
			if(!is_callable($condition)){
				throw new \Exception("parameter condition must by callable");
			}

			$this->array = array_filter($this->array, $condition);

			return $this;
		}

		/**
		 * reverse collection
		 *
		 * @return $this
		 */
		public function reverse(): self
		{
			$this->array = array_reverse($this->array, true);

			return $this;
		}

		/**
		 * sort
		 *
		 * @param string $column
		 * @param bool $desc
		 * @return $this
		 */
		public function orderBy(string $column, bool $desc = false): self
		{
			uasort($this->array, function ($a, $b) use ($column) {
				if (is_array($a)) {
					if ($a[$column] == $b[$column]) {
						return 0;
					}

					return ($a[$column] < $b[$column]) ? -1 : 1;
				} else {
					if ($a->$column == $b->$column) {
						return 0;
					}

					return ($a->$column < $b->$column) ? -1 : 1;
				}
			});
			if ($desc) {
				$this->reverse();
			}

			return $this;
		}

		/**
		 * return unique items
		 *
		 * @return $this
		 */
		public function distinct(): self
		{
			$this->array = array_unique($this->array, SORT_REGULAR);

			return $this;
		}

		/**
		 * combination = where(condition) + select()
		 *
		 * @param callable $requirement
		 * @return array
		 */
		public function takeWhile(callable $requirement): mixed
		{
			$this->where($requirement);

			return $this->select();
		}


		/**
		 * union in sql
		 *
		 * @param array $array
		 * @return $this
		 */
		public function union(array $array): self
		{
			$this->array = array_merge($this->array, $array);

			return $this;
		}

		/**
		 * group by
		 * @example - goupBy("age")
		 * @example - groupBy("name", "age")
		 * @example - groupBy("country", "distrinct", "city", "street")
		 *
		 * @param string $key
		 * @return $this
		 */
		public function groupBy(string $key): self
		{
			$params = array_reverse(func_get_args());

			$params[] = $this->array;
			$this->array = call_user_func_array(array($this, 'arrayGroupBy'), array_reverse($params));

			return $this;
		}

        /**
         * @param array $array
         * @param mixed $key
         * @return array|null
         */
		protected function arrayGroupBy(array $array, mixed $key): ?array
		{
			if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
				trigger_error('arrayGroupBy(): The key should be a string, an integer, or a callback', E_USER_ERROR);
			}


			$func = (is_callable($key) ? $key : null);
			$_key = $key;

			// Load the new array, splitting by the target key
			$grouped = [];
			foreach ($array as $value) {
				if (is_callable($func)) {
					$key = call_user_func($func, $value);
				} else {
					$key = $value[$_key];
				}

				$grouped[$key][] = $value;
			}

			// Recursively build a nested grouping if more parameters are supplied
			// Each grouped array value is grouped according to the next sequential key
			if (func_num_args() > 2) {
				$args = func_get_args();

				foreach ($grouped as $key => $value) {
					$params = array_merge([$value], array_slice($args, 2, func_num_args()));
					$grouped[$key] = call_user_func_array(array($this, 'arrayGroupBy'), $params);
				}
			}

			return $grouped;
		}

		/**
		 * return only elements that combine both fields
		 *
		 * @param array $array
		 * @return $this
		 */
		public function innerJoin(array $array): self
		{
			$this->secondaryArray = $array;
			$this->joinType = "inner";

			return $this;
		}

		/**
		 * the first collection will list all entries, even those which do not connect with other collections
		 *
		 * @param array $array
		 * @return $this
		 */
		public function leftJoin(array $array): self
		{
			$this->secondaryArray = $array;
			$this->joinType = "left";

			return $this;
		}

        /**
         * Condition for join two collections
         *
         * @param callable $condition
         * @return $this
         * @throws \Exception
         */
		public function on(callable $condition): self
		{
			if(!$this->secondaryArray){
				throw new \Exception("You must call innerJoin or leftJoin method!");
			}
			$this->array = $this->joinFactory
				->getJoinObject($this->joinType)
				->setFirstSource($this->array)
				->setSecondarySource($this->secondaryArray)
				->join($condition);
            
			return $this;
		}

		/**
		 * Accepts an array of values which elements must acquire
		 *
		 * @param array $filter
		 * @return $this
		 */
		public function in(array $filter): self
		{
			$count = count($this->array);
			for ($i = 0; $i < $count; $i++) {
				if (is_array($this->array[$i])) {
					if (!in_array($this->array[$i], $filter, true)) {
						unset($this->array[$i]);
					}
				} else {
					if (!in_array($this->array[$i], $filter)) {
						unset($this->array[$i]);
					}
				}
			}

			return $this;
		}

		/**
		 * negation of "in" method
		 *
		 * @param array $filter
		 * @return $this
		 */
		public function notIn(array $filter): self
		{
			$count = count($this->array);
			for ($i = 0; $i < $count; $i++) {
				if (is_array($this->array[$i])) {
					if (in_array($this->array[$i], $filter, true)) {
						unset($this->array[$i]);
					}
				} else {
					if (in_array($this->array[$i], $filter)) {
						unset($this->array[$i]);
					}
				}
			}

			return $this;
		}

		/**
		 * filtering data by key
		 *
		 * @param array $keys
		 * @return $this
		 */
		public function onlyKeys(array $keys): self
		{
			$count = count($this->array);
			for ($i = 0; $i < $count; $i++) {
				if (is_array($this->array[$i])) {
					$this->array[$i] = array_intersect_key($this->array[$i], array_flip($keys));
				} else {
					$this->array[$i] = (object)array_intersect_key((array)$this->array[$i], array_flip($keys));
				}
			}

			return $this;
		}

        /**
         * @param string $mainNode
         * @return $this
         */
		public function pivot(string $mainNode): self
		{
			$newArray = [];
			foreach($this->array as $key => $value){
				//echo $key.'<br>';
				$columnArray = [];
				foreach($value as $columnKey => $columnValue){
					if($columnKey != $mainNode){
						$columnArray[$columnKey] = $columnValue;
					}else{
						$mainKey = $columnValue;
					}
				}
				$newArray[$mainKey] = $columnArray;

			}

			$this->array = $newArray;

			return $this;
		}
	}